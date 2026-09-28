@extends('admin.layouts.master')
@section('title')
    User Management - Users
@endsection

@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/lightbox2/css/lightbox.min.css') }}" />




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

        .scrollable-div {
            max-height: 229px;
            padding: 10px;
            resize: both;
            overflow: auto;
            /* width: 100%; */
        }

        #ppImageDiv img {
            max-width: 50px;
        }

        #map {
            height: 400px;
        }

        .pac-container {
            z-index: 10000 !important;
        }

        .btn-sm-loc {
            border-radius: .2rem !important;
            font-size: .6rem !important;
            padding: 5px !important;
        }
    </style>
    <style>
        .google-authenticator-on::before {
            content: '';
            margin-left: 5px;
            display: inline-block;
            width: 10px;
            /* Adjust the size of the dot */
            height: 10px;
            /* Adjust the size of the dot */
            background-color: green;
            /* Set the color of the dot */
            border-radius: 50%;
            /* Make it a circle */
            position: absolute;
            top: 50%;
            left: calc(100% + 5px);
            /* Adjust the distance of the dot from the checkbox */
            transform: translate(-50%, -50%);
        }

        .google-authenticator-off::before {
            content: '';
            margin-left: 5px;
            display: inline-block;
            width: 10px;
            /* Adjust the size of the dot */
            height: 10px;
            /* Adjust the size of the dot */
            background-color: rgb(249, 181, 23);
            /* Set the color of the dot */
            border-radius: 50%;
            /* Make it a circle */
            position: absolute;
            top: 50%;
            left: calc(100% + 5px);
            /* Adjust the distance of the dot from the checkbox */
            transform: translate(-50%, -50%);
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">User List</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">User Management</a></li>
                            <li class="breadcrumb-item active">User List</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can('Users Create')
                                <button type="button" id="newBtn" class="btn btn-danger add-btn action-add">
                                    <i class="ri-add-line align-bottom me-1"></i> Add New
                                </button>
                            @endcan
                            @can('Users Create')
                                <button type="button" class="btn btn-soft-success action-export" id="exportBtn"
                                    title="Export to Excel"><i class="ri-file-excel-2-line"></i></button>
                            @endcan
                            <button type="submit" class="btn btn-success d-none action-add" id="saveBtn">Save</button>
                            @can('Users Edit')
                                <button type="button" class="btn btn-primary d-none action-edit" id="editBtn">Edit</button>
                            @endcan




                            <button type="button" class="btn btn-light d-none action-close" onclick="toggleForm(false)"
                                id="closeBtn">Close
                            </button>
                            @can('Users Delete')
                                {{-- <button type="button" class="btn btn-soft-danger action-delete d-none" id="multiDeleteBtn"
                                        onClick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button> --}}
                            @endcan
                            <button type="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false"
                                class="btn btn-soft-info shadow-none d-none action-more"><i
                                    class="ri-more-2-fill"></i></button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">
                                @can('Users Change Permission')
                                    <li>
                                        <a class="dropdown-item" id="changePermissionUrl"
                                            href="{{ route('admin.user.users.permissions') }}">
                                            <i class="ri-lock-line align-bottom me-1"></i> Change Permissions
                                        </a>
                                    </li>
                                @endcan
                                @can('Users Delete')
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" id="deleteBtn">
                                            <i class="ri-delete-bin-2-line align-bottom me-1"></i> Delete
                                        </a>
                                    </li>
                                @endcan
                                @can('Users Impersonate')
                                    <li class="bg-warning text-danger">
                                        <a class="dropdown-item d-none impersonate" href="javascript:void(0)" id="impersonate">
                                            <i class="ri-eye-line align-bottom me-1"></i> Impersonate this user
                                        </a>
                                    </li>
                                @endcan
                                @can('See User Activity')
                                    <li class="">
                                        <a class="dropdown-item d-none" href="javascript:void(0)" id="viewActivity">
                                            <i class="ri-eye-line align-bottom me-1"></i> View All Activities
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="collapse slideIn" id="formSection">
            <div class="row">
                <div class="col-12" id="addEditMode">
                    <div class="card">
                        <div class="card-body">
                            <form id="mainForm" class="needs-validation" novalidate>
                                <input type="hidden" id="id" name="id">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-4">
                                                <label for="roles" class="form-label">Role*</label>
                                            </div>
                                            <div class="col-8">
                                                <select name='roles[]' id="roles" class="form-control select2 inputs"
                                                    multiple required>
                                                    @foreach ($roles ?? [] as $role)
                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please choose at least one role.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="roles" class="form-label">Parent*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <select name='parent_id' id="parent_id"
                                                    class="form-select select2 inputs" disabled>
                                                    <option value=""></option>
                                                    @foreach ($userAgents as $index => $group)

                                                        <optgroup label="{{ $index }}">
                                                            @foreach ($group as $user)
                                                                <option value="{{ $user['id'] }}" data-image="{{ $user['thumbnail'] }}" data-status="{{ $user['status'] }}" data-zone="{{ $user["zone_id"] }}">{{ $user['name'] }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please choose public user.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="zone_id" class="form-label">Zone*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <select name='zone_id' id="zone_id"
                                                    class="form-control select2 inputs" disabled>
                                                    @foreach ($groups ?? [] as $index => $group)
                                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please choose public user.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="first_name" class="form-label">First Name*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="text" class="form-control inputs" name="first_name"
                                                    id="first_name" placeholder="Enter first name" required>
                                                <div class="invalid-feedback">
                                                    Please enter first name.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="last_name" class="form-label">Last Name*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="text" class="form-control inputs" name="last_name"
                                                    id="last_name" placeholder="Enter last name" required>
                                                <div class="invalid-feedback">
                                                    Please enter last name.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="mobile" class="form-label">Mobile*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="tel" class="form-control inputs" name="mobile"
                                                    id="mobile" placeholder="Enter mobile number" required>
                                                <div class="invalid-feedback">
                                                    Please enter mobile number.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="email" class="form-label">Email*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="email" class="form-control inputs" name="email"
                                                    id="email" placeholder="Enter email address" required>
                                                <div class="invalid-feedback">
                                                    Please enter email address.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="email" class="form-label">Secondary Email</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="email" class="form-control inputs" name="secondary_email"
                                                    id="secondary_email" placeholder="Enter email address">
                                                <div class="invalid-feedback">
                                                    Please enter email address.
                                                </div>
                                            </div>

                                            <div class="col-4 mt-2">
                                                <label for="position" class="form-label">Position*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="text" class="form-control inputs" name="position"
                                                    id="position" placeholder="Enter position" required>
                                                <div class="invalid-feedback">
                                                    Please enter position.
                                                </div>
                                            </div>

                                            <div class="col-4 mt-2">
                                                <label for="bitrix_role" class="form-label">Bitrix Role</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="text" class="form-control inputs" name="bitrix_role"
                                                    id="bitrix_role" placeholder="Enter Bitrix Role">
                                                <div class="invalid-feedback">
                                                    Please enter Bitrix Role.
                                                </div>
                                            </div>

                                            <div class="col-4 mt-2">
                                                <label for="bio" class="form-label">Bio</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <textarea name="bio" id="bio" class="form-control inputs" placeholder="Enter bio" rows="5"></textarea>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="nationality_id" class="form-label">Nationality</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <select class="form-control select2 inputs" name="nationality_id"
                                                    id="nationality_id">
                                                    <option value="" selected disabled>Select nationality</option>
                                                    @foreach ($countries ?? [] as $country)
                                                        <option value="{{ $country->id }}">{{ $country->nationality }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please choose nationality.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="region" class="form-label">Region*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <select class="form-control select2 inputs" name="region_id"
                                                    id="region_id" required>
                                                    <option value="" selected disabled>Select Region</option>
                                                    @foreach ($regions ?? [] as $region)
                                                        <option value="{{ $region->id }}">{{ $region->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please choose region.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="license_no" class="form-label">Licence/RERA No</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="text" class="form-control inputs" name="license_no"
                                                    id="license_no" placeholder="Enter license /RERA #">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-6 mt-2">
                                            </div>
                                            <div class="col-6 mt-2" id="imageDiv">
                                                Image
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label for="photo" class="form-label">Image</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="file" class="form-control inputs" name="photo"
                                                    id="photo">
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label for="telegram_username" class="form-label">Telegram
                                                    Username</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="text" class="form-control inputs"
                                                    name="telegram_username" id="telegram_username"
                                                    placeholder="Enter telegram username">

                                                {{-- <a href="javascript:void(0)" title="Telegram Chat ID"  id="telegram_chatid"> </a> --}}
                                            </div>

                                            <div class="col-6 mt-2">
                                                <label for="telegram_username" class="form-label">Telegram Chat Id</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="text" class="form-control inputs" name="telegram_chatid"
                                                    id="telegram_chatid" placeholder="Enter telegram chat id">
                                            </div>

                                            <div class="col-6 mt-2">
                                                <label for="website_link" class="form-label">Website Link</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="url" class="form-control inputs" name="website_link"
                                                    id="website_link" placeholder="Enter website link">
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label for="facebook_link" class="form-label">Facebook Link</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="url" class="form-control inputs" name="facebook_link"
                                                    id="facebook_link" placeholder="Enter facebook link">
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label for="instagram_link" class="form-label">Instagram Link</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="url" class="form-control inputs" name="instagram_link"
                                                    id="instagram_link" placeholder="Enter instagram link">
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label for="youtube_link" class="form-label">Youtube Link</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="url" class="form-control inputs" name="youtube_link"
                                                    id="youtube_link" placeholder="Enter youtube link">
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label for="linkedin_link" class="form-label">Linkedin Link</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="url" class="form-control inputs" name="linkedin_link"
                                                    id="linkedin_link" placeholder="Enter linked link">
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label for="linkedin_link" class="form-label">Bitrix id</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="text" class="form-control inputs" name="bitrix_id"
                                                    id="bitrix_id" placeholder="Enter bitrix id">
                                            </div>

                                            <div class="col-6 mt-2">
                                                <label for="joining_date" class="form-label">Joining Date</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="date" class="form-control inputs" name="joining_date"
                                                    data-provider="flatpickr" data-date-format="d.m.y" data-enable-time
                                                    id="joining_date" placeholder="Enter Date of Joining">
                                            </div>


                                            <div class="col-6 mt-2">
                                                <label for="last_deal_date" class="form-label">Last Deal Date</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="date" class="form-control inputs" name="last_deal_date"
                                                    data-provider="flatpickr" data-date-format="d.m.y" data-enable-time
                                                    id="last_deal_date" placeholder="Enter Last Deal Date">
                                            </div>
                                            <div class="col-6 mt-2">
                                                <label for="listing_allowed" class="form-label">Listings Allowed</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <input type="number" class="form-control inputs" name="listing_allowed"

                                                    id="listing_allowed" placeholder="Enter Number of listings allowed">
                                            </div>

                                            <div class="col-6 mt-2">
                                                <label for="entity" class="form-label">Entity</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <select class="form-control inputs select2" name="entity"
                                                    id="entity">
                                                    <option value="Secondary">Secondary</option>
                                                    <option value="Primary">Primary</option>
                                                    <option value="Commercial">Commercial</option>
                                                    <option value="Abu Dhabi">Abu Dhabi</option>
                                                    <option value="Chinese">Chinese</option>
                                                </select>
                                            </div>

                                            <div class="col-6 mt-2">
                                                <label for="telelog_entity" class="form-label">Telelog Entity</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <select class="form-control inputs select2" name="telelog_entity"
                                                    id="telelog_entity">
                                                    @foreach($telelog_entities ?? [] as $telelogEntity)
                                                        <option value="{{ $telelogEntity }}">{{ $telelogEntity }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-6 mt-2">
                                                <label for="statusCheckbox" class="form-label">Status</label>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <div class="form-check form-switch form-switch-lg form-switch-success"
                                                    dir="ltr">
                                                    <input type="checkbox" name="status"
                                                        class="form-check-input deactivate-link remove-item-btn"
                                                        id="statusCheckbox" checked="" data-thumb=""
                                                        data-modal="App\\Models\\User">
                                                    <label class="form-check-label" for="statusCheckbox"></label>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" id="submitBtn" class="btn btn-primary d-none">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card" id="getUserDevices">
                    </div>
                    <div class="card" id="getUserSuspensions">
                    </div>
                </div>

                <div class="col-4 d-none" id="viewMode">
                    @can('User Set Credentials')
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" id="setCredentialsBtn" class="btn btn-primary action-save">Set
                                    Credentials
                                </button>
                            </div>
                        </div>
                    @endcan
                    @can('User Activity Log')
                        <div class="row">
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
                                                            <div class="text-center mb-2 mt-2 act-spinner">
                                                                <div class="spinner-border text-success " role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="button" class="btn btn-sm btn-light align-center mb-2" style="display:none;"
                                                                    id="loadMoreActivityButton">More</button>

                                                            <button id="loadMoreActivityButtonLoader" class="btn btn-light btn-load btn-sm" style="display:none;">
        <span class="d-flex align-items-center">
            <span class="spinner-border flex-shrink-0" role="status">
                <span class="visually-hidden">Loading...</span>
            </span>
            <span class="flex-grow-1 ms-2">
                Loading...
            </span>
        </span>
                                                            </button>
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
                    @endcan

                    @can('User Permissions')
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Permissions</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 mt-2">
                                        <label for="callTrackingCheckbox" class="form-label">Call Tracking</label>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                            <input type="checkbox" name="call_tracking"
                                                class="form-check-input updatePermission" id="callTrackingCheckbox"
                                                checked="" data-name="call_tracking">
                                            <label class="form-check-label" for="callTrackingCheckbox"></label>
                                        </div>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <label for="callLeadCheckbox" class="form-label">Call Lead</label>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                            <input type="checkbox" name="call_lead" class="form-check-input updatePermission"
                                                id="callLeadCheckbox" data-name="call_lead">
                                            <label class="form-check-label" for="callLeadCheckbox"></label>
                                        </div>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <label for="IsReportExempt" class="form-label">Report Exempt</label>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                            <input type="checkbox" name="is_report_exempt"
                                                class="form-check-input updatePermission" id="IsReportExempt"
                                                data-name="is_report_exempt">
                                            <label class="form-check-label" for="IsReportExempt"></label>
                                        </div>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <label for="commercialCheckbox" class="form-label">Commercial</label>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                            <input type="checkbox" name="is_commercial"
                                                class="form-check-input updatePermission" id="commercialCheckbox"
                                                data-name="is_commercial">
                                            <label class="form-check-label" for="commercialCheckbox"></label>
                                        </div>
                                    </div>

                                    <div class="col-6 mt-2">
                                        <label for="temporaryDisable" class="form-label">Temporary Disable</label>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                            <input type="checkbox" name="temporary_disable"
                                                class="form-check-input updatePermission" id="temporaryDisable"
                                                data-name="temporary_disable">
                                            <label class="form-check-label" for="temporaryDisable"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan

                    @can('User Api Access')
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <label for="" class="form-label">Mobile App Status</label>
                                    </div>
                                    <div class="col-5 text-end">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="">
                                            <input type="checkbox" name="app_status" class="form-check-input"
                                                id="app_status" disabled>
                                            <label class="form-check-label" for="app_status"></label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <button type="button" id="logoutFromMobileApp"
                                            class="btn btn-primary action-save">Logout</button>
                                    </div>
                                    <div class="col-md-12">
                                        <p id="app_status_msg"></p>
                                    </div>
                                </div>




                            </div>
                        </div>
                    @endcan


                    @can('User Api Access')
                        <div class="card">
                            <div class="card-body">

                                <form id="apiAccessForm">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="" class="form-label">Api Access</label>
                                        </div>
                                        <div class="col-8">
                                            <div class="form-check form-switch form-switch-lg form-switch-success"
                                                dir="ltr">
                                                <input type="checkbox" name="api_access" class="form-check-input"
                                                    id="ApiAccessCheckbox">
                                                <label class="form-check-label" for="ApiAccessCheckbox"></label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <label for="" class="form-label">Rate Limit (Per min.)</label>
                                        </div>
                                        <div class="col-5">
                                            <input type="number" class="form-control" name="api_rate_limit"
                                                id="api_rate_limit">
                                        </div>
                                        <div class="col-3">
                                            <button type="submit" id="apiAccessFormSubmit"
                                                class="btn btn-primary action-save">Save</button>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan

                    @can('User Traccar Settings')
                        <div class="card">
                            <div class="card-body">
                                <form id="traccarForm">

                                    <div class="row mb-1">
                                        <div class="col-4">
                                            <label for="" class="form-label">Traccar status</label>
                                        </div>
                                        <div class="col-5">
                                            <div class="form-check form-switch form-switch-lg form-switch-success"
                                                dir="ltr">
                                                <input type="checkbox" name="traccar_status" class="form-check-input"
                                                    id="traccar_status_switch">
                                                <label class="form-check-label" for="traccar_status_switch"></label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <button type="submit" id="traccarFormSubmit"
                                                class="btn btn-primary action-save traccar_check">Save</button>

                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <small id="traccar_msg" class="text-danger"></small>
                                        </div>

                                    </div>
                                    <div class="row d-none" id="traccarInputArea">
                                        <div class="col-4">
                                            <label for="" class="form-label">Device Identifier</label>
                                        </div>
                                        <div class="col-8">

                                            <input type="text" name="device_identifier" class="traccar_check form-control"
                                                id="device_identifier">

                                        </div>
                                        <div class="col-4 mt-2">
                                            <label for="" class="form-label">Port</label>
                                        </div>
                                        <div class="col-8 mt-2">

                                            <input type="number" name="port" class="traccar_check form-control"
                                                id="port">

                                        </div>

                                        <div class="col-4 mt-2">
                                            <label for="" class="form-label">Mic Status</label>
                                        </div>
                                        <div class="col-8 mt-2">

                                            <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                <input type="checkbox" class="traccar_check form-check-input"
                                                    name="mic_status" id="micSwitchsizelg">
                                                <label class="form-check-label" for="micSwitchsizelg"></label>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="text-end mt-2">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#locationModal"
                                            class="btn btn-light action-close float-left d-none" id="traccarLocationBtn">
                                            <i class="ri-map-pin-line"></i>
                                            Location</button>

                                    </div>
                                </form>

                                <!-- Accordions with Icons -->
                                <div class="accordion custom-accordionwithicon mt-2 d-none" onclick="getTraccarLocation()"
                                    id="accordionWithiconLocation">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="accordionwithiconHead">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#accor_iconExamplecollapse1" aria-expanded="true"
                                                aria-controls="accor_iconExamplecollapse1">
                                                <i class="ri-map-pin-user-fill"></i> Locations
                                            </button>
                                        </h2>
                                        <div id="accor_iconExamplecollapse1" class="accordion-collapse collapse"
                                            aria-labelledby="accordionwithiconHead"
                                            data-bs-parent="#accordionWithiconLocation">
                                            <div class="accordion-body">

                                                <div class="text-center" id="traccerLLL">
                                                    <div class="spinner-border text-secondary" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>

                                                <div class="mx-n3">
                                                    <div data-simplebar="init" data-simplebar-auto-hide="false"
                                                        data-simplebar-track="info" style="max-height: 238px;"
                                                        class="simplebar-scrollable-y">
                                                        <div class="simplebar-wrapper" style="margin: 0px;">
                                                            <div class="simplebar-height-auto-observer-wrapper">
                                                                <div class="simplebar-height-auto-observer"></div>
                                                            </div>
                                                            <div class="simplebar-mask">
                                                                <div class="simplebar-offset"
                                                                    style="right: 0px; bottom: 0px;">
                                                                    <div class="simplebar-content-wrapper" tabindex="0"
                                                                        role="region" aria-label="scrollable content"
                                                                        style="height: auto; overflow: hidden scroll;">
                                                                        <div id="traccarLocationList"
                                                                            class="simplebar-content" style="padding: 0px;">


                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="simplebar-placeholder"
                                                                style="width: 365px; height: 256px;"></div>
                                                        </div>
                                                        <div class="simplebar-track simplebar-horizontal"
                                                            style="visibility: hidden;">
                                                            <div class="simplebar-scrollbar simplebar-visible"
                                                                style="width: 0px; display: none;"></div>
                                                        </div>
                                                        <div class="simplebar-track simplebar-vertical"
                                                            style="visibility: visible;">
                                                            <div class="simplebar-scrollbar simplebar-visible"
                                                                style="height: 221px; transform: translate3d(0px, 0px, 0px); display: block;">
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
                    @endcan
                    @can('User Public Profile')
                        <div class="card">
                            <div class="card-body">
                                <form id="publicProfileForm">
                                    <input type="hidden" id="public_profile_user_id" name="id">
                                    <div class="row mb-1">
                                        <div class="col-4">
                                            <label for="public_profile" class="form-label">Public Profile</label>
                                        </div>
                                        <div class="col-5">
                                            <div class="form-check form-switch form-switch-lg form-switch-success"
                                                dir="ltr">
                                                <input type="checkbox" name="pp_enabled" class="form-check-input"
                                                    id="pp_enabled">
                                                <label class="form-check-label" for="ppCheckbox"></label>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <button type="submit" id="publicProfileSubmitBtn"
                                                class="btn btn-primary action-save">Save</button>
                                        </div>
                                    </div>
                                    <div class="mt-2 row pp_enabled_dependant" style="display: none;">
                                        <div class="col-4">
                                            <label for="public_profile" class="form-label">Full Name</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control inputs pp_enabled_dependant_field"
                                                name="pp_name" id="pp_name" placeholder="Enter Public Profile Name">
                                        </div>
                                    </div>
                                    <div class="mt-2 row pp_enabled_dependant" style="display: none;">
                                        <div class="col-6 mt-2">
                                        </div>
                                        <div class="col-6 mt-2" id="ppImageDiv">
                                            Public Photo
                                        </div>
                                    </div>
                                    <div class="mt-2 row pp_enabled_dependant" style="display: none;">
                                        <div class="col-4">
                                            <label for="public_profile" class="form-label">Photo</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="file"
                                                class="form-control inputs pp_enabled_dependant_field_optional"
                                                name="pp_image" id="pp_image">
                                        </div>
                                    </div>
                                    <div class="mt-2 row pp_enabled_dependant" style="display: none;">
                                        <div class="col-4">
                                            <label for="public_profile" class="form-label">License no</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control inputs pp_enabled_dependant_field"
                                                name="pp_license_no" id="pp_license_no"
                                                placeholder="Enter Public Profile License no">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan
                    @can('User Location Allocation')
                        <div class="card">
                            <div class="card-body">
                                <form id="locationForm">
                                    <input type="hidden" id="location_allocation_id" name="id">
                                    <div class="row">
                                        {{-- <div class="col-4">
                                        <label for="community_id" class="form-label">Community*</label>
                                    </div>
                                    <div class="col-8">
                                        <select name='community_id' id="community_id" class="form-control select2"
                                            required>
                                            <option value="">Select</option>
                                            @foreach ($communities ?? [] as $community)
                                                <option value="{{ $community->id }}">{{ $community->name }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}

                                        <div class="col-3 mt-2">
                                            <label for="sub_communities" class="form-label">Location</label>
                                        </div>
                                        <div class="col-9 mt-2 scrollable-div">
                                            <select name='sub_communities[]' id="sub_communities"
                                                class="form-control select2" multiple required size="9"></select>
                                        </div>
                                        {{-- <p id="locationINFO"></p> --}}
                                    </div>
                                    <div class="text-end mt-2">


                                        <button type="submit" id="locationSubmitBtn"
                                            class="btn btn-primary action-save">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan

                    <div class="card d-none">
                        <div class="card-body">
                            <form id="portalForm">
                                <input type="hidden" id="portal_allocation_id" name="id">
                                <div class="row">
                                    @foreach ($portals ?? [] as $portal)
                                        <div class="col-4 mt-2">
                                            <label for="portal-{{ $portal->id }}"
                                                class="form-label">{{ $portal->name }}</label>
                                        </div>
                                        <div class="col-8 mt-2">
                                            <div class="input-group">
                                                <label class="input-group-text" for="portal-{{ $portal->id }}">Quota:
                                                    <span class="portal-quota-label"
                                                        id="portal-{{ $portal->id }}-label">{{ $portal->quota }}</span></label>
                                                <input type="number" class="form-control portal-allocation"
                                                    id="portal-{{ $portal->id }}"
                                                    name="portals[{{ $portal->id }}][allocation]"
                                                    max="{{ $portal->quota }}"
                                                    placeholder="{{ $portal->name }} allocation">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-end mt-2">
                                    <button type="button" onclick="toggleForm()"
                                        class="btn btn-light action-close">Close</button>
                                    <button type="submit" id="portalSubmitBtn"
                                        class="btn btn-primary action-save">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @includeIf('admin.user.includes.filterForm')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <table id="categoryDatatable" class="table align-middle" style="width:100%">
                        <thead class="">
                            <tr class="table-light2 text-uppercase">
                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Parent</th>
                                <th>Role</th>
                                <th>Entity</th>
                                <th>Email</th>
                                <th>Updated</th>
                                <th>Created</th>
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
    <div class="modal zoomIn" id="credentialModal" aria-labelledby="credentialModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header card-header pb-2">
                    <h5 class="modal-title" id="credentialModalLabel">Set Credentials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="credentialsForm" class="needs-validation" novalidate>
                        <input type="hidden" id="type" name="type" value="save">
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="password" class="form-label">Password*</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="password" name="password"
                                    placeholder="Enter password" required>
                                <div class="invalid-feedback">
                                    Please enter password.
                                </div>
                            </div>

                            <div class="col-4">
                                <label for="password_confirmation" class="form-label">Confirm Password*</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Confirm password" required>
                                <div class="invalid-feedback">
                                    Please enter confirm password.
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button id="credSaveBtn" type="submit" class="btn btn-primary">Save</button>
                                    <button id="credSaveAndSendBtn" type="submit" class="btn btn-primary">Save and
                                        Send
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @includeIf('admin.user.includes.modals')
@endsection
@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="{{ URL::asset('/assets/libs/glightbox/glightbox.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/lightbox2/js/lightbox.min.js') }}"></script>
    {{-- <script src=" "></script> --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="{{ asset('assets/js/traccar.js') }}"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAcZNvJFINzsKGTuGyABZ6WcVJWriqZbqk&callback=initTraccarMap&v=weekly&libraries=places"
        defer async></script>
    <script>
        let isUpdate = false;
        let mapMarker;
        let locationAccordOpen = false;
        let dataObj = {};
        let changePermissionUrl = "{{ route('admin.user.users.permissions') }}?user_id=:id";
        let mobileInput = initPhoneValidator()
        let changeStatus = true;
        let selectedUser = null;
        let actCurrentPage = 1;
        $("#parent_id").select2({
            placeholder: "Select parent user",
            allowClear: true
        });
        $("#public_profile_id").select2({
            placeholder: "Select public user",
            allowClear: true
        });
        $("#entity").select2({
            placeholder: "Select Entity",
            allowClear: true
        });

        $("#zone_id").select2({
            placeholder: "Select a zone",
            allowClear: true
        });

        $("#telelog_entity").select2({
            placeholder: "Select Telelog Entity",
            allowClear: true
        });








        $(document).on("click", "#select-all-sub-communities", function() {
            if (this.checked) {
                $("#sub_communities>option").prop("selected", "selected")
            } else {
                $("#sub_communities").val("");
            }
            $("#sub_communities").trigger("change")
        })


        $('#sub_communities').select2({
            placeholder: 'Select Location',
            allowClear: true,
            closeOnSelect: false,
            ajax: {
                url: "{{ route('ajax.community.getUsersCommunity.select2') }}",
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


        $(document).on("change", "#select-all-export", function() {
            $(`.export-columns`).prop("checked", this.checked).trigger("change");
        })
        $(document).on("click", "#exportBtn", function() {
            $("#exportForm").removeClass("was-validated");
            $("#exportModal").modal("show")
        })
        $(document).on("submit", "#exportForm", function(event) {
            event.preventDefault();
            let isSelected = $(".export-columns:checked").length > 0;
            if (!isSelected) {
                showToastify("Please select at least one column.", "error");
                return;
            }
            let exportRoute = "{{ route('admin.user.users.export-users-excel') }}";
            let columns = $(this).serialize();

            window.open(`${exportRoute}?${columns}`);

            $("#exportForm").trigger("reset");
            $("#exportModal").modal("hide");
            return;
        })
        let filtersData = [];
        let statusChanged = false;
        $(document).on("submit", "#searchForm", function(e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);
            if ($("#search_status").is(':checked') && statusChanged) {
                filtersData.search_status = "true";
            } else if (!$("#search_status").is(':checked') && statusChanged) {
                filtersData.search_status = "false";
            }
            dataTable.ajax.reload();
        })
        $("#search_status").change(function() {
            statusChanged = true;
        })

        $(document).on("click", "#clearSearchBtn", function() {
            $("#search_status").prop("checked", false);
            filtersData.search_status = null;
            $(".search-inputs").val("").trigger("change");
            statusChanged = false;
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            //$("#searchSearchBtn").trigger("click")
            $("#search_active").val('1');
            $("#search_inactive").val('0');
            dataTable.ajax.reload();
        })

        $(document).on("click", "#refreshTableButton", function() {
            $("#searchSearchBtn").trigger("click")
        })

        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }

        function getUsers(userFor) {
            $("#parent_id").val("").trigger("change")
            $("#parent_id").html("")
            if (userFor) {
                $.ajax({
                    url: "{{ route('ajax.users-for-role') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        for: userFor
                    },
                    success: function(result) {
                        let options = "";
                        result?.data.map(item => {
                            options += `<option value="${item.id}">${item.name}</option>`
                        })
                        $("#parent_id").html(options)
                        $("#parent_id").val(dataObj?.parent_id).trigger("change");

                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }
        }

        function getAgents(userFor) {
            $("#public_profile_id").val("").trigger("change")
            $("#public_profile_id").html("")
            if (userFor) {
                $.ajax({
                    url: "{{ route('ajax.getAgents') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        for: userFor
                    },
                    success: function(result) {
                        let options = "";
                        result?.data.map(item => {
                            options += `<option value="${item.id}">${item.name}</option>`
                        })
                        $("#public_profile_id").html(options)
                        $("#public_profile_id").val(dataObj?.parent_id).trigger("change");
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }
        }

        $(document).on("change", "#roles", function() {
            const selectedOptionTexts = $(this).find(':selected').map(function() {
                return $(this).text();
            }).get();
            if (selectedOptionTexts.includes("CEO")) {
                $("#parent_id").prop({
                    required: false,
                    disabled: true,
                })
                return;
                console.log('CEO is selected.');
            } else {
                // Code to execute if "CEO" is not selected
                $("#parent_id").prop({
                    required: false,
                    disabled: false,
                })
                return;
            }
            $("#parent_id").prop({
                required: false,
                disabled: false,
            })
        })

        $(document).on("keyup", "#email", function() {
            checkUnique($(this).val(), "email", "User", $("#id").val())
        })

        $(document).on("blur", "#email", function() {
            checkUnique($(this).val(), "email", "User", $("#id").val())
        })

        $(document).on("keyup", "#mobile", function() {
            checkUnique(mobileInput.getNumber(), "mobile", "User", $("#id").val())
        })

        $(document).on("blur", "#mobile", function() {
            checkUnique(mobileInput.getNumber(), "mobile", "User", $("#id").val())
        })

        $(document).on("click", "#setCredentialsBtn", function() {
            @can('Users Edit')
                $("#credentialsForm").trigger("reset")
                $("#credentialsForm").removeClass("was-validated")
                $("#credentialModal").modal("show")
            @endcan
        })

        $(document).on("click", "#credSaveBtn", function() {
            $("#type").val("save");
        })

        $(document).on("click", "#credSaveAndSendBtn", function() {
            $("#type").val("send");
        })

        $("#closeBtn").click(function() {
            $('#impersonate').addClass('d-none');
            $('#viewActivity').addClass('d-none');
            changeStatus = true;
        })

        $("#newBtn").click(function() {
            selectedUser = null;
            @can('Users Create')
                isUpdate = false;
                dataObj = {};
                $('#mainForm').removeClass('was-validated');
                $("#mainForm").trigger("reset")

                $("#id").val("")
                $(".inputs").prop("disabled", false)
                $(".inputs").val("").trigger("change")
                $("#addEditMode").removeClass("col-8").addClass("col-12")
                $("#viewMode").addClass("d-none")
                $("#imageDiv").html('');
                $('#telegram_chatid').val('');

                $("#saveBtn, #closeBtn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn, #dropdownMenuLink1, #view-edit-mode").addClass(
                    "d-none");
                toggleForm(true)
            @endcan
        });



        function toggleForm(show) {
            if (show == undefined) {
                $("#formSection").toggleClass("show")
                return
            }
            if (show) {
                $("#formSection").addClass("show");
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #deleteBtn, #dropdownMenuLink1").addClass("d-none");
                $("#newBtn, #multiDeleteBtn").removeClass("d-none");
                $("#formSection").removeClass("show");
            }
        }

        $(document).on('click', '.edit-link', function(
            event) { //edit link click event it will fetch single cateogry details and put it on form
            @can('Users Edit')
                let id = $(this).data('id');
                $('#id').val(id);
                editData(id);
            @endcan
        });

        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
            if (this.checked) {
                $("#multiDeleteBtn").removeClass("d-none");
            } else {
                $("#multiDeleteBtn").addClass("d-none");
            }
        })

        // on change of .updatePermission class checkbox get the name and value and update the permission
        $(document).on("change", ".updatePermission", function() {
            let name = $(this).data("name");
            let value = $(this).is(":checked");
            let id = $("#id").val();
            updatePermission(id, name, value);


        })

        function updatePermission(id, name, value) {
            $.ajax({
                url: "{{ route('ajax.updatePermission') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    user_id: id,
                    key: name,
                    value: value
                },
                success: function(result) {
                    // if already alert is there remove it
                    if ($(".toastify-right").length) {
                        $(".toastify-right").remove();
                    }

                    showToastify(result.msg, "success")


                },
                error: function(result) {
                    console.log(result);
                }
            });
        }


        $("#saveBtn").click(function() {
            $("#submitBtn").trigger("click")
        })

        function getPortalQuotas(user_id) {
            $(".portal-quota-label").html("")
            $.ajax({
                url: "{{ route('ajax.portal-quotas') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    user_id
                },
                success: function(result) {
                    result?.data.map(item => {
                        $(`#portal-${item.id}-label`).html(
                            `<span class="text-danger" title="Used Quota: ${item.used}">${item.used}</span>/<span class="text-primary" title="Total Quota: ${item.quota}">${item.quota}</span>(<span class="text-success" title="Available Quota: ${item.available}">${item.available}</span>)`
                        )
                        $(`#portal-${item.id}`).attr("max", item.available)
                    })
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }
        function formatAgent(e) {
            let img = $(e.element).data('image');
            let status = $(e.element).data('status');
            let onlineClass = "online"
            if (!status) {
                onlineClass = "away";
            }
            return e.id ? $('<div class="d-flex align-items-center"><div class="flex-shrink-0 chat-user-img ' +
                onlineClass + ' align-self-center me-2 ms-0"><div class="avatar-xxs mb-2"> <img src="' + img +
                '" class="rounded-circle img-fluid userprofile" alt=""><span class="user-status"></span> </div></div><div class="flex-grow-1 overflow-hidden"><p class="text-truncate mb-0">' +
                e.text + '</p></div></div>') : e.text;
        }
        if ($("#parent_id").hasClass("select2")) {
            $('#parent_id').select2({
                placeholder: 'Select parent user',
                allowClear: true,
                templateResult: formatAgent,
                templateSelection: formatAgent,

            });
        }
        @can('User Traccar Settings')
            $("#traccar_status_switch").change(function() {
                if (document.getElementById('traccar_status_switch').checked) {
                    $("#traccarInputArea").removeClass("d-none");
                    $("#traccarLocationBtn").removeClass("d-none");
                    $("#accordionWithiconLocation").removeClass("d-none");
                } else {
                    $("#traccarInputArea").addClass("d-none");
                    $("#traccarLocationBtn").addClass("d-none");
                    $("#accordionWithiconLocation").addClass("d-none");
                }
            })
            $("#traccarLocationBtn").click(function() {
                $("#traccarLocationModal").modal("show");
                $("#traccarMapForm").trigger("reset");
                $("#traccar_id").val("");
            })

            function getTraccarLocation() {

                if (locationAccordOpen) {
                    return false;
                } {
                    $("#traccarLocationList").html('');
                    $.ajax({
                        url: "{{ route('admin.user.users.traccar.location.get') }}",
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            id: $("#id").val()
                        },
                        success: function(result) {
                            locationAccordOpen = true;
                            $("#traccerLLL").addClass('d-none');
                            if (result.status == '1') {
                                let html = '';
                                result?.data.map(item => {


                                    html +=
                                        `  <div class="d-flex align-items-center py-2 traccarLocation${item.id}">
                                                                                <div class="flex-shrink-0 avatar-xs me-1">
                                                                                    <div class="avatar-title bg-light text-primary rounded-circle">
                                                                                        <i class="ri-map-pin-line"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-grow-1 overflow-hidden">
                                                                                    <h5 class="fs-12 mb-1"> ${item?.title}</h5>
                                                                                    <p class="text-muted mb-0 fs-10"> ${item?.radius/1000 || ""}km | ${item?.stay_time || ""}min</p>
                                                                                </div>
                                                                                <div class="flex-shrink-0 align-self-start ms-2">
                                                                                   <span onclick="editTraccarLocation('` +
                                        item.id +
                                        `')" class="btn  btn-outline-info waves-effect waves-light cursor-pointer btn-sm-loc"><i class=" ri-edit-box-line"></i> </span>
                                                                                    <span onclick="deleteTraccarLocation('` +
                                        item.id + `')" class="btn btn-outline-danger waves-effect waves-light cursor-pointer btn-sm-loc"><i class="ri-delete-bin-line"></i> </span>
                                                                                </div>
                                                                            </div>`;

                                })
                                $("#traccarLocationList").html(html);
                            } else {

                            }

                        },
                        error: function(result) {
                            locationAccordOpen = true;
                            $("#traccerLLL").removeClass('d-none');
                            console.log(result);
                        }
                    });
                }
            }

            function editTraccarLocation(id) {
                $.ajax({
                    url: "{{ route('admin.user.users.traccar.location.view') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: id
                    },
                    success: function(result) {
                        locationAccordOpen = true;
                        $("#traccerLLL").addClass('d-none');
                        if (result.status == '1') {
                            $("#locationModal").modal('show');
                            $("#traccar_id").val(result?.data?.id);
                            $("#traccar_title").val(result?.data?.title);
                            $("#traccar_radius").val(result?.data?.radius);
                            $("#selectedRadius").html(result?.data?.radius + "m");
                            $("#traccar_zoom").val(result?.data?.zoom);
                            $("#traccar_stay_time").val(result?.data?.stay_time);
                            $("#location_latitude").val(result?.data?.lat);
                            $("#location_longitude").val(result?.data?.lng).trigger("input");
                        } else {

                        }

                    },
                    error: function(result) {
                        locationAccordOpen = true;
                        $("#traccerLLL").removeClass('d-none');
                        console.log(result);
                    }
                });
            }

            function deleteTraccarLocation(id) {

                @can('Users Delete')
                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Location for this user ?</p></div></div>',
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
                                    url: "{{ route('admin.user.users.traccar.location.delete') }}",
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        id
                                    },
                                    success: function(result) {
                                        if (result.status == '1') { //if successfully deleted
                                            locationAccordOpen = false;
                                            getTraccarLocation();
                                            showToastify(result.msg, "success")

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


            $(document).on('submit', '#traccarForm', function(event) {
                $("#traccarFormSubmit").prop('disabled', true);
                event.preventDefault();
                let formData = new FormData($(this)[0]);
                formData.append("id", $("#id").val());
                $.ajax({
                    url: "{{ route('admin.user.users.api.traccar.update') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status == '1') {

                            showToastify(result.msg, "success")
                            $("#traccarFormSubmit").prop('disabled', false); //enable submit button

                        } else if (result.status == '2') {
                            $("#traccarFormSubmit").prop('disabled', false);
                            let error_message = '';

                            $.each(result.errors, function(key, value) {
                                error_message += '' + key + ': ' + value + ' | ';
                            });
                            showToastify(error_message, "error")
                        } else {
                            $("#traccarFormSubmit").prop('disabled', false);
                            showToastify(result.msg, "error")
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        $("#traccarFormSubmit").prop('disabled', false);
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
            $(document).on('submit', '#traccarMapForm', function(event) {
                $("#traccarFormBtn").prop('disabled', true);
                event.preventDefault();
                let formData = new FormData($(this)[0]);
                formData.append("user_id", $("#id").val());
                $.ajax({
                    url: "{{ route('admin.user.users.traccar.location.update') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $("#traccarFormBtn").prop('disabled', false); //enable submit button
                            $("#locationModal").modal("hide");
                            $(".clrInput").val("");
                            $('#traccarMapForm').removeClass('was-validated');
                            locationAccordOpen = false;
                            getTraccarLocation();
                        } else if (result.status == '2') {
                            $("#traccarFormBtn").prop('disabled', false);
                            let error_message = '';

                            $.each(result.errors, function(key, value) {
                                error_message += '' + key + ': ' + value + ' | ';
                            });
                            showToastify(error_message, "error")
                        } else {
                            $("#traccarFormBtn").prop('disabled', false);
                            showToastify(result.msg, "error")
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        $("#traccarFormBtn").prop('disabled', false);
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
        @endcan
        $(document).ready(function() {
            @if (request()->id)
                viewUser("{{ request()->id }}")
            @endif
        })


        function viewUser(id) {
            $("#activityLogDiv").find(".acitivity-timeline").html("");
            actCurrentPage = 1;
            @can('Users View')
                getPortalQuotas(id)
                isUpdate = false
                locationAccordOpen = false;
                $("#addEditMode").removeClass("col-12").addClass("col-8")

                $("#traccarLocationList").html("");
                $('#impersonate').addClass('d-none');
                $('#viewActivity').addClass('d-none');
                $("#viewMode").removeClass("d-none");
                $(".inputs").prop("disabled", true)
                $(".pp_enabled_dependant_field").prop("disabled", false);
                $(".pp_enabled_dependant_field_optional").prop("disabled", false);
                $(".inputs").removeClass("is-invalid")
                $("#id").val(id);
                $("#portal_allocation_id").val(id);
                $("#location_allocation_id").val(id);
                $("#public_profile_user_id").val(id);


                $(".inputs").val("").trigger("change")
                $("#mainForm").trigger("reset");
                $("#locationForm").trigger("reset");
                $("#portalForm").trigger("reset");
                $('#mainForm').removeClass('was-validated');

                $("#saveBtn, #newBtn, #multiDeleteBtn").addClass("d-none");
                $("#editBtn, #closeBtn, #deleteBtn, #dropdownMenuLink1").removeClass("d-none");
                changeStatus = true;
                $.ajax({
                    url: "{{ route('admin.user.users.get') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            $('#id').val(result.data.id);

                            @if (auth()->user()->hasRole('Super Admin'))
                                if (result.data.status == 1) {
                                    $('#impersonate').removeClass('d-none');
                                    $('#viewActivity').removeClass('d-none');
                                }
                            @else
                                $('#impersonate').addClass('d-none');
                                $('#viewActivity').addClass('d-none');
                            @endif



                            $("#deleteBtn").attr("data-id", result.data.id);

                            let data = result.data
                            selectedUser = data;
                            if (result.data) {
                                dataObj = data;
                                Object.keys(result.data).map((index) => {
                                    $(`#${index}`).val(result.data[index]).trigger("change")
                                })
                                $('#telegram_chatid').val(result.data.telegram_chatid);
                                let roles = [];
                                data?.roles.map(item => {
                                    roles.push(item.id)
                                })
                                $("#roles").val(roles).trigger("change")
                                data?.portals.map(item => {
                                    $(`#portal-${item.portal_id}`).val(item.allocation);
                                })



                                checkMobileAppStatus(data.id);
                                checkTraccarStatus(data.id);

                                if (data.joining_date) {
                                    $('#joining_date').val(data.joining_date);
                                }
                                if (data.last_deal_date) {
                                    $('#last_deal_date').val(data.last_deal_date);
                                }
                                // data?.sub_communities.map(item => {
                                //     appendSubCommunity(item.sub_community, true)
                                // })
                                // console.log('------------------------')
                                // console.log(result.data);
                                // console.log('------------------------')
                                // let locations = result.data?.locations;
                                // let subCom = [];

                                // $.each(locations, function(key, value) {

                                //         if (value.parentable_type == 'App\\Models\\SubCommunity') {
                                //             let cmbid = value.parentable_id + ':sub_community';
                                //             if ($('#sub_communities').find("option[value='" + cmbid + "']").length) {
                                //                 $('#sub_communities').val(value.parentable_id).trigger('change');
                                //             } else {
                                //                 var newOption = new Option(value?.parentable?.path_name, cmbid, true, true);
                                //                 $('#sub_communities').append(newOption).trigger('change');
                                //             }
                                //             subCom.push(cmbid);
                                //         }

                                //         if (value.parentable_type == 'App\\Models\\Community') {
                                //             let cmbid = value.parentable_id + ':community';
                                //             if ($('#sub_communities').find("option[value='" + cmbid + "']").length) {
                                //                 $('#sub_communities').val(value.parentable_id).trigger('change');
                                //             } else {
                                //                 var newOption = new Option(value?.parentable?.path_name, cmbid, true, true);
                                //                 $('#sub_communities').append(newOption).trigger('change');
                                //             }
                                //             subCom.push(cmbid);
                                //         }

                                //          console.log(subCom);
                                // });
                                let locations = result.data?.locations;
                                let subCom = [];
                                $('#getUserDevices').html('');
                                $('#getUserSuspensions').html('');

                                @can('View User Devices')
                                    getUserDevices(result.data.id);
                                @endcan
                                @can('View User Suspensions')
                                    getUserSuspensions(result.data.id);
                                @endcan

                                // Step 1: Create all options first
                                $.each(locations, function(key, value) {
                                    if (value.parentable_type == 'App\\Models\\SubCommunity') {
                                        let cmbid = value.parentable_id + ':sub_community';
                                        if ($('#sub_communities').find("option[value='" + cmbid + "']")
                                            .length === 0) {
                                            var newOption = new Option(value?.parentable?.path_name,
                                                cmbid, true, true);
                                            $('#sub_communities').append(newOption);
                                        }
                                        subCom.push(cmbid);
                                    }

                                    if (value.parentable_type == 'App\\Models\\Community') {
                                        let cmbid = value.parentable_id + ':community';
                                        if ($('#sub_communities').find("option[value='" + cmbid + "']")
                                            .length === 0) {
                                            var newOption = new Option(value?.parentable?.path_name,
                                                cmbid, true, true);
                                            $('#sub_communities').append(newOption);
                                        }
                                        subCom.push(cmbid);
                                    }
                                });

                                // Step 2: Select all options after the loop
                                $('#sub_communities').val(subCom).trigger('change');

                            }

                            $("#statusCheckbox").prop("checked", result.data.status)
                            $("#callTrackingCheckbox").prop("checked", result.data.call_tracking)
                            $("#commercialCheckbox").prop("checked", result.data.is_commercial)
                            $("#callLeadCheckbox").prop("checked", result.data.call_lead)
                            $("#ApiAccessCheckbox").prop("checked", result.data.api_access)
                            $("#temporaryDisable").prop("checked", result.data.temporary_disable)

                            $("#IsReportExempt").prop("checked", result.data.is_report_exempt)
                            $("#traccar_status_switch").prop("checked", result.data.traccar_status).trigger(
                                "change")
                            $("#micSwitchsizelg").prop("checked", result.data.mic_status).trigger("change")
                            if (result.data.traccar_status) {
                                $("#traccarInputArea").removeClass("d-none");
                                $("#traccarLocationBtn").removeClass("d-none");
                            } else {
                                $("#traccarInputArea").addClass("d-none");
                            }

                            $("#pp_enabled").prop("checked", result.data.pp_enabled)
                            if (result.data.pp_enabled) {
                                $(".pp_enabled_dependant").show();
                                $("#pp_enabled").val(1);

                            } else {
                                $(".pp_enabled_dependant").hide();
                                $("#pp_enabled").val('');
                            }
                            if (result.data.pp_photo != null) {
                                $("#ppImageDiv").html(
                                    `<a href="${result.data.pp_photo}" data-lightbox="public-gallery"><img src="${ result.data.pp_photo }" class="img-fluid" alt="Public image"></a>`
                                    );
                            } else {
                                $("#ppImageDiv").html('');
                            }
                            if (result.data.thumb_url != null) {
                                $("#imageDiv").html(
                                    `<a href="${result.data.image_url}" data-lightbox="user-gallery"><img data-lightbox="listing-gallery" src="${result.data.thumb_url} " class="img-fluid" alt="User image"></a>`
                                    );
                            } else {
                                $("#imageDiv").html('');
                            }
                            mobileInput = initPhoneValidator("mobile")

                            toggleForm(true)
                            scrollTop()

                            $('#parent_id').prop("disabled", true);
                            var url = "{{ route('admin.user.users.index') }}" + "?id=" + result.data.id;
                            let changePermUrl = changePermissionUrl.replace(':id', result.data.id);
                            $("#changePermissionUrl").attr('href', changePermUrl);
                            window.history.pushState('data', '', url);
                        } else {
                            showToastify(result.msg, "error")
                        }
                        getActivityLog(id)
                        getTraccarLocation();
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        }

        $(document).ready(function() {
            $('#impersonate').click(function() {
                @if (auth()->user()->hasRole('Super Admin'))
                    var userId = $('#id').val(); // Get the user ID from the input field
                    if (userId) {
                        window.location.href = "/impersonate/take/" + userId;
                    } else {
                        alert("Please enter a valid user ID");
                    }
                @endif
            });




            $('#viewActivity').click(function() {
                @if (auth()->user()->hasRole('Super Admin'))
                    var userId = $('#id').val(); // Get the user ID from the input field
                    if (userId) {
                        window.location.href = "/user/users/view/activity/" + userId;
                    } else {
                        alert("Please enter a valid user ID");
                    }
                @endif
            });

        });



        $(document).on("click", ".impersonateUser", function() {

            @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                var userId = $(this).data('id'); // Get the user ID from the input field
                if (userId) {
                    window.location.href = "/impersonate/take/" + userId;
                } else {
                    alert("Please enter a valid user ID");
                }
            @endif
        });

        // @canImpersonate($guard = null)
        //                     <a href="{{ route('impersonate', 172) }}">Impersonate this user</a>
        //                         @endCanImpersonate

        function appendSubCommunity(data, selected = false) {
            if (!$('#sub_communities').find("option[value='" + data.id + "']").length) {
                let newOption = new Option(data.name, data.id, selected, selected);
                $('#sub_communities').append(newOption);
            } else {
                if (selected) {
                    let selected = [];
                    $("#sub_communities option:selected").map((ind, el) => {
                        selected.push($(el).val())
                    });
                    const index = selected.indexOf(data.id.toString());
                    if (index == -1) {
                        selected.push(data.id);
                    } else {
                        selected.splice(index, 1);
                    }
                    $("#sub_communities").val(selected).trigger("change")
                }
            }
        }

        $(document).on("change", "#community_id", function() {
            $("#select-all-sub-communities").prop("checked", false)
            let community_id = $(this).val();
            if (community_id) {
                $.ajax({
                    url: "{{ route('ajax.sub-communities') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        community_id
                    },
                    success: function(result) {
                        result?.data.map(item => {
                            // appendSubCommunity(item)
                        })
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }
        })

        $(document).on('click', '#editBtn', function(event) {
            @can('Users Edit')
                let id = $('#id').val();
                editData(id);
            @endcan
        });

        function editData(id) {
            @can('Users Edit')
                isUpdate = true;
                $("#mainForm").trigger("reset");
                $("#mainForm").removeClass("was-validated")
                $(".inputs").prop("disabled", false)
                $(".inputs").removeClass("is-invalid")
                $("#addEditMode").removeClass("col-8").addClass("col-12")
                $("#viewMode").addClass("d-none")
                $(".inputs").prop("disabled", false)
                $("#id").val("");
                $("#roles").val("").trigger("change")
                $("#locationForm").trigger("reset");
                $("#portalForm").trigger("reset");
                $("#saveBtn, #closeBtn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn, #dropdownMenuLink1").addClass("d-none");
                toggleForm(true)
                changeStatus = true;
                $.ajax({
                    url: "{{ route('admin.user.users.get') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            let data = result.data;
                            console.log(data);
                            if (result.data) {
                                dataObj = data;
                                Object.keys(result.data).map((index) => {
                                    $(`#${index}`).val(result.data[index]).trigger("change")
                                })
                                let roles = [];
                                data?.roles.map(item => {
                                    roles.push(item.id)
                                })
                                $("#roles").val(roles).trigger("change")
                                // if ($.inArray(1, roles) !== -1) {
                                //     $('#roles').prop("disabled", false);
                                // }else{
                                //     $('#roles').prop("disabled", true);
                                // }
                                $("#statusCheckbox").prop("checked", result.data.status)
                                $("#statusCheckbox").attr("data-thumb", result.data.thumb_url)
                                $("#statusCheckbox").attr("data-id", result.data.id)


                                $("#IsReportExempt").prop("checked", result.data.is_report_exempt)
                                $("#callTrackingCheckbox").prop("checked", result.data.call_tracking)
                                $("#callLeadCheckbox").prop("checked", result.data.call_lead)
                                $("#commercialCheckbox").prop("checked", result.data.is_commercial)
                                $("#ApiAccessCheckbox").prop("checked", result.data.api_access)
                                $("#temporaryDisable").prop("checked", result.data.temporary_disable)
                                mobileInput = initPhoneValidator("mobile")
                            }
                            scrollTop()
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

        function drawRoles(roles) {
            if (roles.length == 0 || roles == null || roles == undefined) {
                return "";
            }
            let roless = "";
            if (roles) {
                roles.map(item => {
                    roless += `${item.name}, `;
                })
            }
            roless = roless.substring(0, roless.length - 2);
            return roless;
        }
        let dataTable = $('#categoryDatatable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search users"
            },
            'lengthMenu': [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            dom: `<'row'<'col-sm-6 text-left'B> <'col-sm-6 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],

            createdRow: function(row, data, index) {
                $('#categoryDatatable tbody').css('cursor', 'pointer');

            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.user.users.dt') }}",
                type: "post",
                data: function(d) {
                    d.roles = filtersData?.roles;
                    d.parent_id = filtersData?.parent_id;
                    d.first_name = filtersData?.first_name;
                    d.last_name = filtersData?.last_name;
                    d.mobile = filtersData?.search_mobile;
                    d.email = filtersData?.email;
                    d.position = filtersData?.position;
                    d.licence_rera = filtersData?.licence_rera;
                    d.telegram_username = filtersData?.telegram_username;
                    d.telegram_chatid = filtersData?.telegram_chatid;
                    d.public_name = filtersData?.public_name;
                    d.public_license_no = filtersData?.public_license_no;
                    d.status = filtersData?.search_status;
                    d.nationality_id = filtersData?.nationality_id;
                    d.created_from = filtersData?.search_created_from;
                    d.created_to = filtersData?.search_created_to;
                    d.deleted = filtersData?.search_deleted;
                    d.api_access = filtersData?.api_access;
                    d.without_telegram_chatid = filtersData?.search_without_chatid;
                    d.without_parent = filtersData?.search_without_parent;
                    d.logged_in = filtersData?.search_logged_in;
                    d.google_authenticator = filtersData?.search_google_authenticator;
                    d.bitrix_role = filtersData?.search_bitrix_role;
                    d.telelog_entities = filtersData?.telelog_entities;
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
                $(`@includeIf('admin.user.includes.filterButtons')`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [{
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
                    data: "email",
                    name: "email",
                },

                {
                    data: "updated_at",
                    name: "updated_at",
                },

                {
                    data: "created_at",
                    name: "created_at",
                },
                {
                    data: null,
                    orderable: false,
                }, {
                    data: null,
                    orderable: false,
                }

            ],
            "columnDefs": [{
                    "targets": 0,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `<div class="form-check"><input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="${data.id}"></div>`;
                    }
                },
                {
                    "targets": 1,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return data.id;
                    }
                },
                {
                    "targets": 2,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `<div class="d-flex align-items-center"><div class="flex-shrink-0"><img src="` +
                            data.thumb_url + `" alt="" class="avatar-xs rounded-circle"></div><div class="flex-grow-1 ms-2 name">
                            <a href="javascript:void(0)" class="text-primary" onclick="viewUser('` + data.id + `')">` +
                            data.name + `</a></div></div>`;
                    }
                },
                {
                    "targets": 3,
                    orderable: true,
                    visible: false,
                    render: function(type, row, data, meta) {
                        if (data.parent) {
                            return `<div class="d-flex align-items-center"><div class="flex-shrink-0"><img src="` +
                                data?.parent.thumb_url + `" alt="" class="avatar-xs rounded-circle"></div><div class="flex-grow-1 ms-2 name">
                            <a href="javascript:void(0)" class="text-primary" onclick="viewUser('` + data.parent.id +
                                `')">` +
                                data?.parent?.name + `</a></div></div>`;
                        }
                        return "-";
                    }
                },

                {
                    "targets": 4,
                    orderable: false,
                    render: function(type, row, data, meta) {

                        return drawRoles(data.roles);
                    }
                },
                {
                    "targets": 5,
                    orderable: false,
                    render: function(type, row, data, meta) {
                        if (!data.entity) return "-";
                        return data.entity;
                    }
                },
                {
                    "targets": 6,
                    orderable: false,
                    render: function(type, row, data, meta) {

                        return data.email;
                    }
                },
                {
                    "targets": 7,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        let html = `<div class="d-flex justify-content-between gap-2 align-items-center">
                                    <div class="text-center"><br>

                                        <span title="${moment(data.updated_at).format('DD/MM/YYYY hh:mm:ss')}">${moment(data.updated_at).fromNow()}</span>
                                    </div>
                                </div>`
                        if (data.updated_by != null) {
                            let htmll = `<div class="d-flex align-items-center userDetails" data-agent-id="${data.updated_by.id}"  title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.first_name}<p class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>
                                </div>`
                            return htmll;
                        } else {
                            if (data.updated_at != null) {
                                return html;
                            } else {
                                return '-'
                            }

                        }
                    }
                },
                 {
                    "targets": 8,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        let html = `<div class="d-flex justify-content-between gap-2 align-items-center">
                                    <div class="text-center"><br>

                                        <span title="${moment(data.created_at).format('DD/MM/YYYY hh:mm:ss')}">${moment(data.created_at).fromNow()}</span>
                                    </div>
                                </div>`
                        if (data.created_by_id != null) {
                            let htmll = `<div class="d-flex align-items-center userDetails" data-agent-id="${data.created_by.id}"  title="${data.created_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.created_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.created_by.first_name}<p class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
                                </div>`
                            return htmll;
                        } else {
                            if (data.created_by != null) {
                                return html;
                            } else {
                                return '-'
                            }

                        }
                    }
                },
                {
                    targets: 9,
                    orderable: false,
                    visible: true,
                    render: function(type, row, data, meta) {
                        let disabled = true;
                        @can('Users Edit')
                            disabled = false
                        @endcan
                        let suspension = '';
                        let classname = ''
                        if (data.suspensions.length > 0) {
                            suspension = 'This User May Be Suspended';
                            classname = 'suspended-user';
                        }
                        let google2fa_secret = 'google-authenticator-off';
                        if (data.google2fa_secret) {
                            google2fa_secret = 'google-authenticator-on';
                        }


                        if (data.status == '1') {


                            return `<div  class="form-check form-switch   form-switch-lg form-switch-success" dir="ltr">
                                        <input ${disabled ? "disabled" : ""} type="checkbox" title="${suspension}"  data-id="${data.id}"  data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="${suspension}" data-bs-original-title="${suspension}" data-do="0" name="status" class="${classname} form-check-input ${google2fa_secret} deactivate-link remove-item-btn"  data-thumb="${data.thumb_url}"  data-modal="App\\Models\\User" checked></div>`;
                        } else {
                            return `<div  class="form-check form-switch  form-switch-lg form-switch-success" dir="ltr">
                                        <input ${disabled ? "disabled" : ""} data-id="${data.id}"  title="${suspension}" data-do="1" type="checkbox" name="status" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="${suspension}" data-bs-original-title="${suspension}" data-do="0" class="${classname} form-check-input ${google2fa_secret} status-change"></div>`;
                        }
                    }
                },
                {
                    targets: 10,
                    orderable: false,
                    visible: true,
                    render: function(type, row, data, meta) {
                        let viewUrl = "{{ route('admin.user.users.view', ['user' => '1']) }}";
                        viewUrl = viewUrl.slice(0, -1);
                        let impersonateOption = data.status === 1 ?
                            `<li><a data-id="${data.id}" class="impersonateUser dropdown-item"><i class="ri-ghost-line align-bottom me-2 text-muted"></i> Impersonate</a></li>` :
                            '';

                        return `<div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a href="${viewUrl}${data.id}" class="dropdown-item edit-item-btn"><i class="ri-eye-line align-bottom me-2 text-muted"></i> View</a></li>
                                        <li><a data-id="` + data.id + `" class="dropdown-item edit-item-btn edit-link"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>

                                        @can('Users Impersonate')
                                        ${impersonateOption}
                                        @endcan

                                        <input type="hidden" name="id" value="` + data.id + `" id="id_for_delete">
                                        <input type="hidden" name="model_name" value="App//Models//User" id="modal_name">

                                        <li>
                                            <a   data-id="` + data.id + `" data-name="` + data.name +
                            `" data-thumb="${data.thumb_url}" class="dropdown-item remove-item-btn delete-link"  data-modal="App\\Models\\User" id="` +
                            data.id + `">
                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>`
                    }
                }

            ]
        });
        $(document).on('change', 'tbody tr .selectable-checkbox', function() {
            let countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
            if (countCheckedCheckboxes >= 1) {
                $('#multiDeleteBtn').removeClass('d-none');
            } else {
                $('#multiDeleteBtn').addClass('d-none');
            }
        });
        $(document).on('submit', '#mainForm', function(event) {
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();
            $("#mobile").val(mobileInput.getNumber())
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{ route('admin.user.users.update') }}" :
                    "{{ route('admin.user.users.store') }}",
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
                        $('#mainForm').trigger('reset');
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

        $(document).on('submit', '#credentialsForm', function(event) {
            @can('Users Edit')
                $("#credSaveAndSendBtn, #credSaveBtn").prop('disabled', true);
                event.preventDefault();
                var formData = new FormData($(this)[0]);
                formData.append("id", $("#id").val());
                $.ajax({
                    url: "{{ route('admin.user.users.save-and-send-credentials') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        $("#credSaveAndSendBtn, #credSaveBtn").prop('disabled',
                            false); //enable submit button
                        if (result.status == '1') {
                            showToastify(result.msg, "success");
                            $('#credentialsForm').trigger('reset');
                            $("#credentialModal").modal("hide");
                        } else if (result.status == '2') {
                            $("#submitBtn").prop('disabled', false);
                            let error_message = '';

                            $.each(result.errors, function(key, value) {
                                error_message += '' + key + ': ' + value + ' | ';
                            });
                            showToastify(error_message, "error");
                        } else {
                            showToastify(result.msg, "error");
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        $("#credSaveAndSendBtn, #credSaveBtn").prop('disabled', false);
                        let data = jqXhr.responseJSON;
                        if (data.errors) {
                            $.each(data.errors, function(index, item) {
                                showToastify(item[0], "error");
                            })
                        }
                        if (jqXhr.status == 500 || jqXhr.status == 400) {
                            showToastify(data.message, "error");
                        }
                    },
                });
            @endcan
        });

        $(document).on('submit', '#portalForm', function(event) {
            $("#portalSubmitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{ route('admin.user.users.allocate-portals') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#portalSubmitBtn").prop('disabled', false); //enable submit button
                    } else if (result.status == '2') {
                        $("#portalSubmitBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#portalSubmitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#portalSubmitBtn").prop('disabled', false);
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

        $(document).on('submit', '#publicProfileForm', function(event) {
            event.preventDefault();
            $("#publicProfileSubmitBtn").prop('disabled', true);
            if ($("#pp_enabled").checked) {
                $(this).attr('value', 'true');
            } else {
                $(this).attr('value', 'false');
            }
            var formData = new FormData($(this)[0]);
            console.log(formData);
            $.ajax({
                url: "{{ route('admin.user.users.assign-public-profile') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        getActivityLog($("#id").val())
                        $("#publicProfileSubmitBtn").prop('disabled', false); //enable submit button
                    } else if (result.status == '2') {
                        $("#publicProfileSubmitBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#publicProfileSubmitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#publicProfileSubmitBtn").prop('disabled', false);
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
        $(document).on('submit', '#apiAccessForm', function(event) {
            $("#apiAccessFormSubmit").prop('disabled', true);
            event.preventDefault();
            let formData = new FormData($(this)[0]);
            formData.append("id", $("#id").val());
            console.log(formData);
            $.ajax({
                url: "{{ route('admin.user.users.api.access') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {

                        showToastify(result.msg, "success")
                        $("#apiAccessFormSubmit").prop('disabled', false); //enable submit button
                        getActivityLog($("#id").val())
                    } else if (result.status == '2') {
                        $("#apiAccessFormSubmit").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#apiAccessFormSubmit").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#apiAccessFormSubmit").prop('disabled', false);
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

        $(document).on('submit', '#locationForm', function(event) {


            $("#locationSubmitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{ route('admin.user.users.allocate-sub-communities') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        getActivityLog($("#id").val())

                        $("#locationSubmitBtn").prop('disabled', false); //enable submit button
                    } else if (result.status == '2') {
                        $("#locationSubmitBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#locationSubmitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#locationSubmitBtn").prop('disabled', false);
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


        $('#categoryDatatable tbody').on('change', 'input.status-change', function() {
            @can('Users Edit')
                let id = $(this).data('id');
                let action = $(this).data('do');
                console.log(id);
                let formData = {
                    id: id,
                    action: action
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.user.users.status') }}",
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
            @endcan
        });


        $(document).on('click', '#deleteBtn', function(event) {
            @can('Users Delete')
                let id = $(this).data('id');
                requestDataBeforeDeletion(id)

                $("#userDeletionModal").modal("show");
                // deleteData($('#id').val());
            @endcan
        });

        $(document).on('click', '.delete-link', function(event) {
            @can('Users Delete')
                let id = $(this).data('id');
                let name = $(this).data('name');
                let thumb = $(this).data('thumb');
                $("#deleteUserName").html(name);

                $("#dataAssignForm").trigger("reset");

                $(".assignInput").val("").trigger('change');
                $("#assignOnly").val("2")
                $("#assignSourceAgentId").val(id);
                requestDataBeforeDeletion(id)

                $("#userDeletionModal").modal("show");
                //deleteData(id);
            @endcan
        });


        $(document).on('click', '.deactivate-link', function(event) {
            @can('Users Delete')
                if (changeStatus == true && $(this).data('id')) {
                    if ($(this).prop('checked') == false) {
                        let id = $(this).data('id');
                        let name = $(this).data('name');
                        let thumb = $(this).data('thumb');

                        $("#deActivateUserName").html(name);

                        $("#dataAssignFormDeactivation").trigger("reset");

                        $(".assignInput").val("").trigger('change');
                        $("#assignOnly").val("2")
                        $("#assignSourceAgentIdDeactivation").val(id);
                        requestDataBeforeDeActivation(id)

                        $("#userDeactivationModal").modal("show");
                    }
                }

                //deleteData(id);
            @endcan
        });


        function badgeColor(num) {
            num = parseInt(num);
            if (num > 0) {
                return "danger"
            } else {
                return "success"
            }
        }



        function getActivityLog(id) {
            //add loading animation on # $("#loadMoreActivityButton") button
        $("#loadMoreActivityButton").hide();
        $("#loadMoreActivityButtonLoader").show();
            $.ajax({
                url: "{{ route('admin.user.users.activity.log') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    subject_model: "User",
                    subject_id: id,
                    page: actCurrentPage // Pass the current page for pagination
                },
                success: function(result) {
                    $(".act-spinner").hide();

                    let logs = "";
                    result?.data.map(item => {
                        let date = moment(item.created_at).fromNow();
                        let imageUrl = `${item.causer ? item.causer.thumb_url : "{{ URL::asset('assets/images/users/multi-user.jpg') }}"}`;
                        logs += `<div class="accordion-item border-0">
                          <div class="accordion-header" id="heading6">
                          <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapse${item.id}" aria-expanded="false">
                              <div class="d-flex">
                                <div class="flex-shrink-0">
                                  <img src="${imageUrl}" alt="" class="avatar-xs rounded-circle">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <h6 class="fs-14 mb-1"> ${item.causer ? item.causer.full_name : "System"}  </h6>
                                  <small class="text-muted">${item.description} - <small class="mb-0 text-muted">${date}</small> </small>
                                </div>
                              </div>
                            </a>
                          </div>
                          <div id="collapse${item.id}" class="accordion-collapse collapse hide" aria-labelledby="heading6" data-bs-parent="#accordion${item.id}">
                            <div class="accordion-body ms-2 ps-5 pt-2">
                            <div class="code-view" id="hiddenLog${item.id}">
                         <span class="language-markup">
                            <small class="text-muted "><span class="d-none"><strong>New data:</strong> <br>${item.properties}<br></span><strong>New Data:</strong><br>${activityChangeDataFormatv2(item.properties)} </small></span></div> </div>
                                  </div>
                                </div>`;
                    });

                    // Append new logs to the existing content
                    $("#activityLogDiv").find(".acitivity-timeline").append(logs);
                    $("#loadMoreActivityButton").show();
                    $("#loadMoreActivityButtonLoader").hide();
                    // Show "Load More" button if there are more pages
                    if (result && result.current_page < result.last_page) {
                        $("#loadMoreActivityButton").show();
                    } else {
                        $("#loadMoreActivityButton").hide();
                    }
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }

        // Event listener for the "Load More" button
        $(document).on("click", "#loadMoreActivityButton", function() {
            actCurrentPage++;
            getActivityLog(selectedUser.id); // Pass the selected user ID
        });
        {{--function getActivityLog(id) {--}}
        {{--    $("#activityLogDiv").find(".acitivity-timeline").html("");--}}
        {{--    $.ajax({--}}
        {{--        url: "{{ route('admin.user.users.activity.log') }}",--}}
        {{--        type: 'GET',--}}
        {{--        dataType: 'json',--}}
        {{--        data: {--}}
        {{--            subject_model: "User",--}}
        {{--            subject_id: id--}}
        {{--        },--}}
        {{--        success: function(result) {--}}
        {{--            let logs = "";--}}
        {{--            result?.data.map(item => {--}}
        {{--                let date = moment(item.created_at).fromNow()--}}
        {{--                let imageUrl =--}}
        {{--                    `${item.causer ? item.causer.thumb_url : "{{ URL::asset('assets/images/users/multi-user.jpg') }}"}`;--}}
        {{--                logs += `<div class="accordion-item border-0">--}}
        {{--                          <div class="accordion-header" id="heading6">--}}
        {{--                          <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapse${item.id}" aria-expanded="false">--}}

        {{--                              <div class="d-flex">--}}
        {{--                                <div class="flex-shrink-0">--}}
        {{--                                  <img src="${imageUrl}" alt="" class="avatar-xs rounded-circle">--}}
        {{--                                </div>--}}
        {{--                                <div class="flex-grow-1 ms-3">--}}
        {{--                                  <h6 class="fs-14 mb-1"> ${item.causer ? item.causer.full_name :"System"}  </h6>--}}
        {{--                                  <small class="text-muted">${item.description} - <small class="mb-0 text-muted">${date}</small> </small>--}}
        {{--                                </div>--}}
        {{--                              </div>--}}
        {{--                            </a>--}}
        {{--                          </div>--}}
        {{--                          <div id="collapse${item.id}" class="accordion-collapse collapse hide" aria-labelledby="heading6" data-bs-parent="#accordion${item.id}">--}}
        {{--                            <div class="accordion-body ms-2 ps-5 pt-2">--}}
        {{--                            <div class="code-view" id="hiddenLog${item.id}">--}}
        {{--                         <span class="language-markup">--}}
        {{--                            <small class="text-muted "><span class="d-none"><strong>New data:</strong> <br>${item.properties}<br></span><strong>New Data:</strong><br>${activityChangeDataFormatv2(item.properties)} </small></span></div> </div>--}}
        {{--                                  </div>--}}
        {{--                                </div>`;--}}
        {{--            })--}}

        {{--            $("#activityLogDiv").find(".acitivity-timeline").html(logs);--}}
        {{--        },--}}
        {{--        error: function(result) {--}}
        {{--            console.log(result);--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}



        function activityChangeDataFormat(data) {
            let html = '';
            let val = '';
            for (const [key, value] of Object.entries(data)) {
                console.log(key)
                if (key == 'created_at' || key == 'updated_at') {
                    html += key + ' - ' + moment(value).format("DD/MM/YY HH:mm") + '<br>';

                } else {
                    val = value == null ? '' : value;
                    html += key + ' - ' + val + '<br>';

                }
            }
            return html;
        }

        // function activityChangeDataFormatv2(data) {
        //     let html = '';
        //     let parsedData;
        //
        //     try {
        //         parsedData = JSON.parse(data);
        //     } catch (error) {
        //         return 'Invalid JSON format';
        //     }
        //
        //     for (const [key, value] of Object.entries(parsedData)) {
        //         html += `${key} - ${value}<br>`;
        //     }
        //
        //     return html;
        // }
        function activityChangeDataFormatv2(data) {
            data = data.attributes;
            if (!data) return 'No data';

            let html = '';
            let parsedData;

            try {
                parsedData = typeof data === 'string' ? JSON.parse(data) : data;
                if (typeof parsedData !== 'object') return 'Unexpected data type';
            } catch (error) {
                return 'Invalid JSON format';
            }

            for (const [key, value] of Object.entries(parsedData)) {
                html += `${key} - ${value}<br>`;
            }

            return html;
        }



        function getUserDevices(id) {
            $.ajax({
                url: "{{ route('admin.user.users.getUserDevices') }}",
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                },

                success: function(result) {
                    $('#getUserDevices').html(result);
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log(json)
                },
            });
        }

        function getUserSuspensions(id) {
            $.ajax({
                url: "{{ route('admin.user.users.getUserSuspensions') }}",
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                },

                success: function(result) {
                    $('#getUserSuspensions').html(result);
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log(json)
                },
            });
        }


        function requestDataBeforeDeletion(id) {
            $.ajax({
                url: "{{ route('admin.user.users.data.count') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function(result) {
                    if (result.status == '1') {
                        $('#id').val(result.data.id);
                        let data = result.data
                        if (result.data) {
                            let userRoles = drawRoles(result.data?.user?.roles);
                            $("#agentCardAssignModal").html(` <div class="d-flex align-items-center">
                            <div class="flex-shrink-0"><img src="${result.data?.user?.image_url}" alt="" class="avatar-xs rounded"></div>
                            <div class="flex-grow-1 ms-2 name">
                            <a href="javascript:void(0)" class="text-primary" onclick="viewUser('${id}')">${result.data?.user?.first_name+' '+result.data?.user?.last_name}</a>
                                <br><span class="text-muted">${userRoles}</span>
                                                        </div>
                                <div class="flex-grow-1 ms-2 text-muted">
                                Email: ${result.data?.user?.email}<br>
                                Mobile: ${result.data?.user?.mobile}
                                </div>
                                </div>`);
                            let contactAdditional = result.data.contact_additional_agent;
                            let contactCount = parseInt(result.data.contact_additional_agent) + parseInt(result
                                .data.contacts);
                            let leadCount = parseInt(result.data?.lead_additional_agent) + parseInt(result.data
                                .lead);
                            console.log(result.data?.lead_additional_agent + '+' + result.data.lead + '=' +
                                leadCount);

                            result.data.property > 0 ? $("#assignPropertyAgent").prop('disabled', false) : $(
                                "#assignPropertyAgent").prop('disabled', true);
                            leadCount > 0 ? $("#assignLeadAgent").prop('disabled', false) : $(
                                "#assignLeadAgent").prop('disabled', true);
                            contactCount > 0 ? $("#assignContactAgent").prop('disabled', false) : $(
                                "#assignContactAgent").prop('disabled', true);
                            $("#agentPropertiesText").html(
                                `Properties <span class="badge rounded-pill badge-soft-` + badgeColor(result
                                    .data.property) + `">` + result.data.property + `</span>`)
                            $("#agentLeadsText").html(`Leads <span class="badge rounded-pill badge-soft-` +
                                badgeColor(leadCount) + `">` + leadCount + `</span>`)
                            $("#agentContactsText").html(
                                `Contacts <span class="badge rounded-pill badge-soft-` + badgeColor(
                                    contactCount) + `">` + contactCount + `</span>`)
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

        function requestDataBeforeDeActivation(id) {
            $.ajax({
                url: "{{ route('admin.user.users.data.count') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function(result) {
                    if (result.status == '1') {
                        $('#id').val(result.data.id);
                        let data = result.data
                        if (result.data) {
                            let userRoles = drawRoles(result.data?.user?.roles);
                            $("#agentCardAssignModalDeactivate").html(`<div class="d-flex align-items-center">
                                <div class="flex-shrink-0"><img src="${result.data?.user?.image_url}" alt="" class="avatar-xs rounded"></div>
                                <div class="flex-grow-1 ms-2 name">
                                <a href="javascript:void(0)" class="text-primary" onclick="viewUser('${id}')">${result.data?.user?.first_name+' '+result.data?.user?.last_name}</a>
                                    <br><span class="text-muted">${userRoles}</span></div>
                                    <div class="flex-grow-1 ms-2 text-muted">
                                    Email: ${result.data?.user?.email}<br>
                                    Mobile: ${result.data?.user?.mobile}
                                    </div></div>`);
                            let contactAdditional = result.data.contact_additional_agent;
                            let contactCount = parseInt(result.data.contact_additional_agent) + parseInt(result
                                .data.contacts);
                            let leadCount = parseInt(result.data?.lead_additional_agent) + parseInt(result.data
                                .lead);
                            console.log(result.data?.lead_additional_agent + '+' + result.data.lead + '=' +
                                leadCount);

                            result.data.property > 0 ? $("#assignPropertyAgentDeactivate").prop('disabled',
                                false) : $("#assignPropertyAgentDeactivate").prop('disabled', true);
                            leadCount > 0 ? $("#assignLeadAgentDeactivate").prop('disabled', false) : $(
                                "#assignLeadAgentDeactivate").prop('disabled', true);
                            contactCount > 0 ? $("#assignContactAgentDeactivate").prop('disabled', false) : $(
                                "#assignContactAgentDeactivate").prop('disabled', true);
                            $("#agentPropertiesTextDeactivate").html(
                                `Properties <span class="badge rounded-pill badge-soft-` + badgeColor(result
                                    .data.property) + `">` + result.data.property + `</span>`)
                            $("#agentLeadsTextDeactivate").html(
                                `Leads <span class="badge rounded-pill badge-soft-` + badgeColor(
                                    leadCount) + `">` + leadCount + `</span>`)
                            $("#agentContactsTextDeactivate").html(
                                `Contacts <span class="badge rounded-pill badge-soft-` + badgeColor(
                                    contactCount) + `">` + contactCount + `</span>`)
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


        $("#assignDataOnly").click(function() {
            $("#assignOnly").val("1");
            $("#hSubmitBtn").trigger("click");
        });


        $(document).on('submit', '#dataAssignForm', function(event) {
            $("#assignDataOnly").html(`<span class="d-flex align-items-center">
                                                            <span class="spinner-border flex-shrink-0" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </span>
                                                            <span class="flex-grow-1 ms-2">
                                                                Processing...
                                                            </span>
                                                        </span>`);
            $("#assignDataToUser").prop('disabled', true);
            $("#assignDataToUser").html(`<span class="d-flex align-items-center">
                    <span class="spinner-border flex-shrink-0" role="status">
                        <span class="visually-hidden">Loading...</span>
                            </span>
                                <span class="flex-grow-1 ms-2">
                                    Processing...
                                    </span>
                                </span>`);
            $("#assignDataOnly").prop('disabled', true);
            event.preventDefault();
            let formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{ route('admin.user.users.data.assign') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    $("#assignOnly").val("2");
                    if (result.status == '1') {
                        showToastify(result.msg, "success");
                        $("#userDeletionModal").modal("hide");
                        //   deleteData(result.data.id);
                        $("#assignDataToUser").prop('disabled', false); //enable submit button
                        $("#assignDataToUser").html(`Assign & Delete User`);
                        $("#assignDataOnly").prop('disabled', false);
                        $("#assignDataOnly").html(`Assign Only`);
                        dataTable.row().draw();
                    } else if (result.status == '2') {
                        $("#assignDataToUser").prop('disabled', false);
                        $("#assignDataToUser").html(`Assign & Delete User`);
                        $("#assignDataOnly").prop('disabled', false);
                        $("#assignDataOnly").html(`Assign Only`);
                        let error_message = '';

                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")

                    } else {
                        $("#assignDataToUser").prop('disabled', false);
                        $("#assignDataToUser").html(`Assign & Delete User`);
                        $("#assignDataOnly").html(`Assign Only`);
                        $("#assignDataOnly").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#assignDataToUser").prop('disabled', false);
                    $("#assignDataToUser").html(`Assign & Delete User`);
                    $("#assignDataOnly").html(`Assign Only`);
                    $("#assignDataOnly").prop('disabled', false);
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

        $(document).on('submit', '#dataAssignFormDeactivation', function(event) {
            $('#assignDataToUserDeactivate-loader').removeClass('d-none');
            $('#assignDataToUserDeactivate').addClass('d-none');

            $("#assignDataOnly").html(`<span class="d-flex align-items-center">
                                                            <span class="spinner-border flex-shrink-0" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </span>
                                                            <span class="flex-grow-1 ms-2">
                                                                Processing...
                                                            </span>
                                                        </span>`);
            $("#assignDataToUser").prop('disabled', true);
            $("#assignDataToUser").html(`<span class="d-flex align-items-center">
                    <span class="spinner-border flex-shrink-0" role="status">
                        <span class="visually-hidden">Loading...</span>
                            </span>
                                <span class="flex-grow-1 ms-2">
                                    Processing...
                                    </span>
                                </span>`);
            $("#assignDataOnly").prop('disabled', true);

            event.preventDefault();
            let formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{ route('admin.user.users.data.assign.deactivate') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {

                    $('#assignDataToUserDeactivate-loader').addClass('d-none');
                    $('#assignDataToUserDeactivate').removeClass('d-none');

                    $("#assignOnly").val("2");
                    if (result.status == '1') {
                        showToastify(result.msg, "success");
                        $("#dataAssignFormDeactivation").modal("hide");
                        //   deleteData(result.data.id);
                        $("#assignDataToUserDeactivate").prop('disabled', false); //enable submit button
                        $("#assignDataToUserDeactivate").html(`Assign & Deactivate User`);

                        dataTable.row().draw();
                        $("#userDeactivationModal").modal("hide");
                    } else if (result.status == '2') {
                        $("#assignDataToUserDeactivate").prop('disabled', false);
                        $("#assignDataToUserDeactivate").html(`Assign & Deactivate User`);
                        let error_message = '';
                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")

                    } else {
                        $("#assignDataToUserDeactivate").prop('disabled', false);
                        $("#assignDataToUserDeactivate").html(`Assign & Delete User`);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#assignDataToUserDeactivate").prop('disabled', false);
                    $('#assignDataToUserDeactivate-loader').addClass('d-none');
                    $('#assignDataToUserDeactivate').removeClass('d-none');
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

        $(document).ready(function() {
            $('#send_to_leadpool').change(function() {

                // check if checked
                if ($(this).prop("checked") == true) {
                    $('#assignLeadAgentDeactivate').prop('disabled', true);
                    $('#assignLeadAgentDeactivate').prop('required', false);
                } else if ($(this).prop("checked") == false) {
                    $('#assignLeadAgentDeactivate').prop('disabled', false);
                    $('#assignLeadAgentDeactivate').prop('required', true);
                }


            });
        })

        function deleteData(id) {
            @can('Users Delete')
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
                                url: "{{ route('admin.user.users.delete') }}",
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
                    })
            @endcan
        }

        function deleteMultiple() {
            @can('Users Delete')
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    var content = document.createElement('div');
                    content.innerHTML = "You are about to delete <b>" + ids_array.length +
                        "</b> User(s). Once deleted, you will not be able to use them!";
                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                                ids_array.length + '  User(s)?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete " + ids_array.length + "  User(s).",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0


                        })
                        .then(function(t) {

                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "{{ route('admin.user.users.multi.delete') }}",
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


        $("#sidebarAuth").addClass('collapse show');
        $("#sidbarUsers").addClass('active');
        // JS added by Saim
        $('#pp_enabled').change(function() {
            if (this.checked) {
                $(this).val(1);
                $(".pp_enabled_dependant").show();
                $(".pp_enabled_dependant_field").prop("required", true);
            } else {
                $(this).val(0);
                $(".pp_enabled_dependant").hide();
                $(".pp_enabled_dependant_field").prop("required", false);
            }
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
                            let ul = '<ul class="delete_related_dta kt-nav">';
                            $.each(response.error_related_data, function(key, value) {
                                ul += '<li class="kt-nav__item" style="list-style:none">' + key +
                                    ': ' + value + '</li>';
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
            deleteDataGeneral('Delete', $('#modal_name').val(), $('#id_for_delete').val(), $(
                '#kt_table_pt'), null);
        });




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

                    @can('Users Delete')
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


        function checkMobileAppStatus(id) {
            $.ajax({
                url: "{{ route('admin.user.users.checkMobileAppStatus') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    id
                },
                cache: false,
                success: function(result) {
                    if (result.status == '1') {
                        $('#app_status').prop('checked', true);
                        $('#app_status_msg').text(result.msg);
                        $('#logoutFromMobileApp').prop('disabled', false);
                    } else {
                        $('#app_status').prop('checked', false);
                        $('#app_status_msg').text(result.msg);
                        $('#logoutFromMobileApp').prop('disabled', true);
                    }

                },
                error: function(jqXhr, json, errorThrown) {

                },
            });

        }

        function checkTraccarStatus(id) {
            $.ajax({
                url: "{{ route('admin.user.users.checkTraccarStatus') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    id
                },
                cache: false,
                success: function(result) {
                    if (result.status == '1') {
                        $('#traccar_status_switch').prop('disabled', true);
                        $('.traccar_check').prop('disabled', true);
                        $('#traccar_msg').text('Recording in Progress.');
                    } else {
                        $('#traccar_status_switch').prop('disabled', false);
                        $('.traccar_check').prop('disabled', false);

                        $('#traccar_msg').text('');

                    }

                },
                error: function(jqXhr, json, errorThrown) {

                },
            });

        }

        $(document).on('click', '#logoutFromMobileApp', function() {
            let id = $('#id').val();
            $.ajax({
                url: "{{ route('admin.user.users.logoutFromMobileApp') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                },
                cache: false,
                success: function(result) {
                    if (result.status == '1') {
                        $('#app_status').prop('checked', false);
                        $('#app_status').prop('disabled', true);

                        $('#app_status_msg').text('Mobile App Logout Successfully');
                        // checkMobileAppStatus(id);
                        $('#logoutFromMobileApp').prop('disabled', true);
                        showToastify(result.msg, "success")

                    } else {
                        $('#app_status').prop('disabled', true);
                        $('#app_status').prop('checked', true);
                        checkMobileAppStatus(id);
                        $('#logoutFromMobileApp').prop('disabled', false);
                        showToastify(result.msg, "success")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    showToastify(result.msg, "error")
                },
            });

        });

        dataTable.on('click', 'tbody tr', function(e) {
            var $cell = $(e.target).closest('td'),
                msg;
            //console.log('Index ==========='+$cell.index());
            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 2 || $cell.index() === 9 || $cell.index() === 10) {
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

        $('.userSelect2').select2({
            placeholder: 'Select agent',
            allowClear: true,
            dropdownParent: $('#userDeletionModal'),
            ajax: {
                url: '{{ route('ajax.getUsersSelect2') }}',
                dataType: 'json',
                type: 'GET',
                processResults({
                    data
                }) {
                    let options = '';
                    return {
                        results: $.map(data, function(item) {

                            return {
                                text: `${item.text}`,
                                id: item.id

                            }



                        })
                    }
                }
            }
        });

        $('.userSelect3').select2({
            placeholder: 'Select agent',
            allowClear: true,
            dropdownParent: $('#userDeactivationModal'),
            ajax: {
                url: '{{ route('ajax.getUsersSelect2') }}',
                dataType: 'json',
                type: 'GET',
                processResults({
                    data
                }) {
                    let options = '';
                    return {
                        results: $.map(data, function(item) {

                            return {
                                text: `${item.text}`,
                                id: item.id

                            }



                        })
                    }
                }
            }
        });


        $('#userActive').addClass('active');

        $("#parent_id").on("change", function (e){
            let selectedOption = $(this).find('option:selected');
            const parentZone = selectedOption.data("zone");
            $("#zone_id").val(parentZone || (selectedUser?.zone_id || "")).trigger("change");
        })
    </script>
@endsection
