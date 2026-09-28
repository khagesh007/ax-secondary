@extends('admin.layouts.master')
@section('title')
    User Profile
@endsection

@section('style')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('css')
@endsection
@section('content')
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="{{ $user->thumb_url ?? URL::asset('assets/images/profile-bg.jpg') }}" alt=""
                class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    <img src="{{ $user->thumb_url ?? URL::asset('assets/images/users/avatar-1.jpg') }}" alt="user-img"
                        class="img-thumbnail rounded-circle" />
                </div>
            </div>
            <!--end col-->
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1">{{ $user->name ?? '' }}</h3>
                    <p class="text-white-75">{{ $user?->position ?? ($user?->roles[0]?->name ?? '-') }}</p>
                    @if ($user->country)
                        <div class="hstack text-white-50 gap-1">
                            <div class="me-2"><i
                                    class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>{{ $user->country->name ?? '' }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
            <div class="col-12 col-lg-auto order-last order-lg-0 d-none">
                <div class="row text text-white-50 text-center">
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">{{ $rentListingCount ?? 0 }}</h4>
                            <p class="fs-14 mb-0">Rent Listings</p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">{{ $saleListingCount ?? 0 }}</h4>
                            <p class="fs-14 mb-0">Sale Listings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span
                                    class="d-none d-md-inline-block">Overview</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#activities" role="tab">
                                <i class="ri-list-unordered d-inline-block d-md-none"></i> <span
                                    class="d-none d-md-inline-block">Activities</span>
                            </a>
                        </li>
                        <li class="nav-item d-none">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#activities" role="tab">
                                <i class="ri-list-unordered d-inline-block d-md-none"></i> <span
                                    class="d-none d-md-inline-block">Activities</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#projects" role="tab">
                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span
                                    class="d-none d-md-inline-block">Listings</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#documents" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span
                                    class="d-none d-md-inline-block">Contacts</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#settingsTab" role="tab">
                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span
                                    class="d-none d-md-inline-block">Settings</span>
                            </a>
                        </li>
                    </ul>
                    <div class="flex-shrink-0">
                        <a class="btn btn-success action-edit" id="edit-profile-btn"><i
                                class="ri-edit-box-line align-bottom"></i> Edit Profile</a>
                    </div>
                </div>
                <!-- Tab panes -->
                <div class="tab-content pt-4 text-muted">
                    <div class="tab-pane active" id="overview-tab" role="tabpanel">
                        <div class="row">
                            <div class="col-xxl-3">
                                <div class="card">
                                    {{-- <div class="card-body">
                                        <h5 class="card-title mb-5">Complete Your Profile</h5>
                                        <div class="progress animated-progress custom-progress progress-label">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: 30%"
                                                 aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                                                <div class="label">30%</div>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Info</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Full Name :</th>
                                                        <td class="text-muted">{{ $user->name ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Mobile :</th>
                                                        <td class="text-muted">{{ $user->mobile ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">E-mail :</th>
                                                        <td class="text-muted">{{ $user->email ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Location :</th>
                                                        <td class="text-muted">{{ $user->state->name ?? '' }}
                                                            , {{ $user->country->name ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Joining Date</th>
                                                        <td class="text-muted">
                                                            {{ $user->created_at ? $user->created_at->format('d M Y') : '' }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->

                                <div class="card d-none">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Portfolio</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if ($user->website_link)
                                                <div>
                                                    <a href="javascript:void(0);" class="avatar-xs d-block">
                                                        <span
                                                            class="avatar-title rounded-circle fs-16 bg-dark text-light shadow">
                                                            <i class="ri-global-fill"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($user->facebook_link)
                                                <div>
                                                    <a href="javascript:void(0);" class="avatar-xs d-block">
                                                        <span class="avatar-title rounded-circle fs-16 bg-primary shadow">
                                                            <i class="ri-facebook-fill"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($user->instagram_link)
                                                <div>
                                                    <a href="javascript:void(0);" class="avatar-xs d-block">
                                                        <span class="avatar-title rounded-circle fs-16 bg-success shadow">
                                                            <i class="ri-instagram-fill"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($user->youtube_link)
                                                <div>
                                                    <a href="javascript:void(0);" class="avatar-xs d-block">
                                                        <span class="avatar-title rounded-circle fs-16 bg-danger shadow">
                                                            <i class="ri-youtube-fill"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($user->linkedin_link)
                                                <div>
                                                    <a href="javascript:void(0);" class="avatar-xs d-block">
                                                        <span class="avatar-title rounded-circle fs-16 bg-danger shadow">
                                                            <i class="ri-linkedin-fill"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->

                                <div class="card d-none">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Sub Communities</h5>
                                        <div class="d-flex flex-wrap gap-2 fs-15">
                                            @foreach ($subCommunities ?? [] as $subCommunity)
                                                <a href="javascript:void(0);"
                                                    class="badge badge-soft-primary">{{ $subCommunity->name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="card d-none">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0">Portals</h5>
                                            </div>
                                        </div>
                                        <div>
                                            @foreach ($user->allocated_portals ?? [] as $portalsQuota)
                                                <div class="d-flex align-items-center py-3">
                                                    <div class="avatar-xs flex-shrink-0 me-3">
                                                        @if (!empty($portalsQuota['thumbnail']))
                                                            <img src="{{ $portalsQuota['thumbnail'] }}" alt=""
                                                                class="img-fluid rounded-circle shadow" />
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div>
                                                            <h5 class="fs-14 mb-1">{{ $portalsQuota['name'] }}</h5>
                                                            <p class="fs-13 text-muted mb-0">
                                                                {{ $portalsQuota['allocation'] }}
                                                                / {{ $portalsQuota['used'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-9" id="edit_profile" style="display: none;">
                                <div class="card">
                                    <div class="card-header">
                                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                            role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#changePassword"
                                                    role="tab">
                                                    <i class="far fa-user"></i>
                                                    Change Password
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="changePassword" role="tabpanel">
                                                <form id="change_user_password" action="javascript:void(0);">
                                                    <div class="row g-2">
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <label for="oldpasswordInput" class="form-label">Old
                                                                    Password*</label>
                                                                <input type="password"
                                                                    class="form-control password_feilds"
                                                                    id="current_password" name="current_password"
                                                                    placeholder="Enter current password" required="true">
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <label for="newpasswordInput" class="form-label">New
                                                                    Password*</label>
                                                                <input type="password"
                                                                    class="form-control password_feilds" id="password"
                                                                    name="password" placeholder="Enter new password"
                                                                    required="true">
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <label for="confirmpasswordInput"
                                                                    class="form-label">Confirm
                                                                    Password*</label>
                                                                <input type="password"
                                                                    class="form-control password_feilds"
                                                                    id="password_confirm" name="confirm_password"
                                                                    placeholder="Confirm password" required="true">
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        {{-- <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <a href="javascript:void(0);"
                                                                    class="link-primary text-decoration-underline">Forgot
                                                                    Password ?</a>
                                                            </div>
                                                        </div> --}}
                                                        <!--end col-->
                                                        <div class="col-lg-12">
                                                            <div class="text-end">
                                                                <button type="submit" class="btn btn-success">Change
                                                                    Password</button>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                    </div>
                                                    <!--end row-->
                                                </form>

                                            </div>
                                            <!--end tab-pane-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-9" id="bio_section">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Bio</h5>
                                        <p>{{ $user->bio }}</p>
                                        <div class="row">
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex mt-4">
                                                    <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                                        <div
                                                            class="avatar-title bg-light rounded-circle fs-16 text-primary shadow">
                                                            <i class="ri-user-2-fill"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="mb-1">Designation :</p>
                                                        <h6 class="text-truncate mb-0">
                                                            {{ collect($user?->roles)->implode('name', ' / ') }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex mt-4">
                                                    <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                                                        <div
                                                            class="avatar-title bg-light rounded-circle fs-16 text-primary shadow">
                                                            <i class="ri-global-line"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="mb-1">Website :</p>
                                                        <a href="#"
                                                            class="fw-semibold">{{ $user->website_link ?? '' }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </div>
                                    <!--end card-body-->
                                </div><!-- end card -->

                                <div class="card">
                                    <div class="card-body">
                                        <form id="bulkLanguage">
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <input type="hidden" name="tag" value="language">
                                            <div id="append">

                                            </div>

                                            <div id="appendHere"></div>

                                            <div class="col-12 col-md-12 mt-4 text-end">
                                                <button class="btn action-publish" id="addLanguage" type="button">Add
                                                    Language</button>
                                                <button class="btn btn-success btn-primary" type="button"
                                                    id="save_languages">Save</button>
                                            </div>
                                        </form>
                                        <!--end row-->
                                    </div>
                                    <!--end card-body-->
                                </div><!-- end card -->

                                <div class="row d-none">
                                    <div class="col-lg-12">
                                        <div class="card">
                                            <div class="card-header align-items-center d-flex">
                                                <h4 class="card-title mb-0  me-2">Recent Activity</h4>
                                                <div class="flex-shrink-0 ms-auto">
                                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                                        role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" data-bs-toggle="tab"
                                                                href="#today" role="tab">
                                                                Today
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-bs-toggle="tab" href="#weekly"
                                                                role="tab">
                                                                Weekly
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-bs-toggle="tab" href="#monthly"
                                                                role="tab">
                                                                Monthly
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="tab-content text-muted">
                                                    <div class="tab-pane active" id="today" role="tabpanel">
                                                        <div class="profile-timeline">
                                                            <div class="accordion accordion-flush" id="todayExample">
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="headingOne">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapseOne"
                                                                            aria-expanded="true">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-2.jpg') }}"
                                                                                        alt=""
                                                                                        class="avatar-xs rounded-circle shadow" />
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Jacqueline Steve
                                                                                    </h6>
                                                                                    <small class="text-muted">We
                                                                                        has changed 2
                                                                                        attributes on
                                                                                        05:16PM</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapseOne"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="headingOne"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5">
                                                                            In an awareness campaign, it
                                                                            is vital for people to begin
                                                                            put 2 and 2 together and
                                                                            begin to recognize your
                                                                            cause. Too much or too
                                                                            little spacing, as in the
                                                                            example below, can make
                                                                            things unpleasant for the
                                                                            reader. The goal is to make
                                                                            your text as comfortable to
                                                                            read as possible. A
                                                                            wonderful serenity has taken
                                                                            possession of my entire
                                                                            soul, like these sweet
                                                                            mornings of spring which I
                                                                            enjoy with my whole heart.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="headingTwo">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapseTwo"
                                                                            aria-expanded="false">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0 avatar-xs">
                                                                                    <div
                                                                                        class="avatar-title bg-light text-success rounded-circle shadow">
                                                                                        M
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Megan Elmore
                                                                                    </h6>
                                                                                    <small class="text-muted">Adding
                                                                                        a new event with
                                                                                        attachments -
                                                                                        04:45PM</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapseTwo"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="headingTwo"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5">
                                                                            <div class="row g-2">
                                                                                <div class="col-auto">
                                                                                    <div
                                                                                        class="d-flex border border-dashed p-2 rounded position-relative shadow">
                                                                                        <div class="flex-shrink-0">
                                                                                            <i
                                                                                                class="ri-image-2-line fs-17 text-danger"></i>
                                                                                        </div>
                                                                                        <div class="flex-grow-1 ms-2">
                                                                                            <h6 class="mb-0">
                                                                                                <a href="javascript:void(0);"
                                                                                                    class="stretched-link">Business
                                                                                                    Template
                                                                                                    -
                                                                                                    UI/UX
                                                                                                    design</a>
                                                                                            </h6>
                                                                                            <small>685
                                                                                                KB</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-auto">
                                                                                    <div
                                                                                        class="d-flex border border-dashed p-2 rounded position-relative shadow">
                                                                                        <div class="flex-shrink-0">
                                                                                            <i
                                                                                                class="ri-file-zip-line fs-17 text-info"></i>
                                                                                        </div>
                                                                                        <div class="flex-grow-1 ms-2">
                                                                                            <h6 class="mb-0">
                                                                                                <a href="javascript:void(0);"
                                                                                                    class="stretched-link">Bank
                                                                                                    Management
                                                                                                    System
                                                                                                    -
                                                                                                    PSD</a>
                                                                                            </h6>
                                                                                            <small>8.78
                                                                                                MB</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="headingThree">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse"
                                                                            href="#collapsethree" aria-expanded="false">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-5.jpg') }}"
                                                                                        alt=""
                                                                                        class="avatar-xs rounded-circle shadow" />
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        New ticket
                                                                                        received</h6>
                                                                                    <small class="text-muted mb-2">User
                                                                                        <span
                                                                                            class="text-secondary">Erica245</span>
                                                                                        submitted a
                                                                                        ticket -
                                                                                        02:33PM</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="headingFour">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapseFour"
                                                                            aria-expanded="true">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0 avatar-xs">
                                                                                    <div
                                                                                        class="avatar-title bg-light text-muted rounded-circle shadow">
                                                                                        <i class="ri-user-3-fill"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Nancy Martino
                                                                                    </h6>
                                                                                    <small class="text-muted">Commented
                                                                                        on
                                                                                        12:57PM</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapseFour"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="headingFour"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5 fst-italic">
                                                                            " A wonderful serenity has
                                                                            taken possession of my
                                                                            entire soul, like these
                                                                            sweet mornings of spring
                                                                            which I enjoy with my whole
                                                                            heart. Each design is a new,
                                                                            unique piece of art birthed
                                                                            into this world, and while
                                                                            you have the opportunity to
                                                                            be creative and make your
                                                                            own style choices. "
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="headingFive">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapseFive"
                                                                            aria-expanded="true">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-7.jpg') }}"
                                                                                        alt=""
                                                                                        class="avatar-xs rounded-circle shadow" />
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Lewis Arnold
                                                                                    </h6>
                                                                                    <small class="text-muted">Create
                                                                                        new project
                                                                                        buildng product
                                                                                        -
                                                                                        10:05AM</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapseFive"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="headingFive"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5">
                                                                            <p class="text-muted mb-2">
                                                                                Every team project can
                                                                                have a velzon. Use the
                                                                                velzon to share
                                                                                information with your
                                                                                team to understand and
                                                                                contribute to your
                                                                                project.</p>
                                                                            <div class="avatar-group">
                                                                                <a href="javascript: void(0);"
                                                                                    class="avatar-group-item shadow"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-trigger="hover"
                                                                                    data-bs-placement="top" title=""
                                                                                    data-bs-original-title="Christi">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-4.jpg') }}"
                                                                                        alt=""
                                                                                        class="rounded-circle avatar-xs">
                                                                                </a>
                                                                                <a href="javascript: void(0);"
                                                                                    class="avatar-group-item shadow"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-trigger="hover"
                                                                                    data-bs-placement="top" title=""
                                                                                    data-bs-original-title="Frank Hook">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-3.jpg') }}"
                                                                                        alt=""
                                                                                        class="rounded-circle avatar-xs">
                                                                                </a>
                                                                                <a href="javascript: void(0);"
                                                                                    class="avatar-group-item shadow"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-trigger="hover"
                                                                                    data-bs-placement="top" title=""
                                                                                    data-bs-original-title=" Ruby">
                                                                                    <div class="avatar-xs">
                                                                                        <div
                                                                                            class="avatar-title rounded-circle bg-light text-primary">
                                                                                            R
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="javascript: void(0);"
                                                                                    class="avatar-group-item shadow"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-trigger="hover"
                                                                                    data-bs-placement="top" title=""
                                                                                    data-bs-original-title="more">
                                                                                    <div class="avatar-xs">
                                                                                        <div
                                                                                            class="avatar-title rounded-circle">
                                                                                            2+
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end accordion-->
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="weekly" role="tabpanel">
                                                        <div class="profile-timeline">
                                                            <div class="accordion accordion-flush" id="weeklyExample">
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="heading6">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapse6"
                                                                            aria-expanded="true">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-3.jpg') }}"
                                                                                        alt=""
                                                                                        class="avatar-xs rounded-circle shadow" />
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Joseph Parker
                                                                                    </h6>
                                                                                    <small class="text-muted">New
                                                                                        people joined
                                                                                        with our company
                                                                                        -
                                                                                        Yesterday</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapse6"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="heading6"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5">
                                                                            It makes a statement, it’s
                                                                            impressive graphic design.
                                                                            Increase or decrease the
                                                                            letter spacing depending on
                                                                            the situation and try, try
                                                                            again until it looks right,
                                                                            and each letter has the
                                                                            perfect spot of its own.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="heading7">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapse7"
                                                                            aria-expanded="false">
                                                                            <div class="d-flex">
                                                                                <div class="avatar-xs">
                                                                                    <div
                                                                                        class="avatar-title rounded-circle bg-light text-danger shadow">
                                                                                        <i
                                                                                            class="ri-shopping-bag-line"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Your order is
                                                                                        placed <span
                                                                                            class="badge bg-soft-success text-success align-middle">Completed</span>
                                                                                    </h6>
                                                                                    <small class="text-muted">These
                                                                                        customers can
                                                                                        rest assured
                                                                                        their order has
                                                                                        been placed - 1
                                                                                        week Ago</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="heading8">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapse8"
                                                                            aria-expanded="true">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0 avatar-xs">
                                                                                    <div
                                                                                        class="avatar-title bg-light text-success rounded-circle shadow">
                                                                                        <i class="ri-home-3-line"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Velzon admin
                                                                                        dashboard
                                                                                        templates layout
                                                                                        upload</h6>
                                                                                    <small class="text-muted">We
                                                                                        talked about a
                                                                                        project on
                                                                                        linkedin - 1
                                                                                        week Ago</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapse8"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="heading8"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5 fst-italic">
                                                                            Powerful, clean & modern
                                                                            responsive bootstrap 5 admin
                                                                            template. The maximum file
                                                                            size for uploads in this
                                                                            demo :
                                                                            <div class="row mt-2">
                                                                                <div class="col-xxl-6">
                                                                                    <div
                                                                                        class="row border border-dashed gx-2 p-2 shadow">
                                                                                        <div class="col-3">
                                                                                            <img src="{{ URL::asset('assets/images/small/img-3.jpg') }}"
                                                                                                alt=""
                                                                                                class="img-fluid rounded shadow" />
                                                                                        </div>
                                                                                        <!--end col-->
                                                                                        <div class="col-3">
                                                                                            <img src="{{ URL::asset('assets/images/small/img-5.jpg') }}"
                                                                                                alt=""
                                                                                                class="img-fluid rounded shadow" />
                                                                                        </div>
                                                                                        <!--end col-->
                                                                                        <div class="col-3">
                                                                                            <img src="{{ URL::asset('assets/images/small/img-7.jpg') }}"
                                                                                                alt=""
                                                                                                class="img-fluid rounded shadow" />
                                                                                        </div>
                                                                                        <!--end col-->
                                                                                        <div class="col-3">
                                                                                            <img src="{{ URL::asset('assets/images/small/img-9.jpg') }}"
                                                                                                alt=""
                                                                                                class="img-fluid rounded shadow" />
                                                                                        </div>
                                                                                        <!--end col-->
                                                                                    </div>
                                                                                    <!--end row-->
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="heading9">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapse9"
                                                                            aria-expanded="false">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-6.jpg') }}"
                                                                                        alt=""
                                                                                        class="avatar-xs rounded-circle shadow" />
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        New ticket
                                                                                        created <span
                                                                                            class="badge bg-soft-info text-info align-middle">Inprogress</span>
                                                                                    </h6>
                                                                                    <small class="text-muted mb-2">User
                                                                                        <span
                                                                                            class="text-secondary">Jack365</span>
                                                                                        submitted a
                                                                                        ticket - 2 week
                                                                                        Ago</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="heading10">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapse10"
                                                                            aria-expanded="true">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-5.jpg') }}"
                                                                                        alt=""
                                                                                        class="avatar-xs rounded-circle shadow" />
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Jennifer Carter
                                                                                    </h6>
                                                                                    <small class="text-muted">Commented
                                                                                        - 4 week
                                                                                        Ago</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapse10"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="heading10"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5">
                                                                            <p class="text-muted fst-italic mb-2">
                                                                                " This is an awesome
                                                                                admin dashboard
                                                                                template. It is
                                                                                extremely well
                                                                                structured and uses
                                                                                state of the art
                                                                                components (e.g. one of
                                                                                the only templates using
                                                                                boostrap 5.1.3 so far).
                                                                                I integrated it into a
                                                                                Rails 6 project. Needs
                                                                                manual integration work
                                                                                of course but the
                                                                                template structure made
                                                                                it easy. "</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end accordion-->
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="monthly" role="tabpanel">
                                                        <div class="profile-timeline">
                                                            <div class="accordion accordion-flush" id="monthlyExample">
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="heading11">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapse11"
                                                                            aria-expanded="false">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0 avatar-xs">
                                                                                    <div
                                                                                        class="avatar-title bg-light text-success rounded-circle shadow">
                                                                                        M
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Megan Elmore
                                                                                    </h6>
                                                                                    <small class="text-muted">Adding
                                                                                        a new event with
                                                                                        attachments - 1
                                                                                        month
                                                                                        Ago.</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapse11"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="heading11"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5">
                                                                            <div class="row g-2">
                                                                                <div class="col-auto">
                                                                                    <div
                                                                                        class="d-flex border border-dashed p-2 rounded position-relative shadow">
                                                                                        <div class="flex-shrink-0">
                                                                                            <i
                                                                                                class="ri-image-2-line fs-17 text-danger"></i>
                                                                                        </div>
                                                                                        <div class="flex-grow-1 ms-2">
                                                                                            <h6 class="mb-0">
                                                                                                <a href="javascript:void(0);"
                                                                                                    class="stretched-link">Business
                                                                                                    Template
                                                                                                    -
                                                                                                    UI/UX
                                                                                                    design</a>
                                                                                            </h6>
                                                                                            <small>685
                                                                                                KB</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-auto">
                                                                                    <div
                                                                                        class="d-flex border border-dashed p-2 rounded position-relative shadow">
                                                                                        <div class="flex-shrink-0">
                                                                                            <i
                                                                                                class="ri-file-zip-line fs-17 text-info"></i>
                                                                                        </div>
                                                                                        <div class="flex-grow-1 ms-2">
                                                                                            <h6 class="mb-0">
                                                                                                <a href="javascript:void(0);"
                                                                                                    class="stretched-link">Bank
                                                                                                    Management
                                                                                                    System
                                                                                                    -
                                                                                                    PSD</a>
                                                                                            </h6>
                                                                                            <small>8.78
                                                                                                MB</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-auto">
                                                                                    <div
                                                                                        class="d-flex border border-dashed p-2 rounded position-relative shadow">
                                                                                        <div class="flex-shrink-0">
                                                                                            <i
                                                                                                class="ri-file-zip-line fs-17 text-info"></i>
                                                                                        </div>
                                                                                        <div class="flex-grow-1 ms-2">
                                                                                            <h6 class="mb-0">
                                                                                                <a href="javascript:void(0);"
                                                                                                    class="stretched-link">Bank
                                                                                                    Management
                                                                                                    System
                                                                                                    -
                                                                                                    PSD</a>
                                                                                            </h6>
                                                                                            <small>8.78
                                                                                                MB</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="heading12">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapse12"
                                                                            aria-expanded="true">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-2.jpg') }}"
                                                                                        alt=""
                                                                                        class="avatar-xs rounded-circle shadow" />
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Jacqueline Steve
                                                                                    </h6>
                                                                                    <small class="text-muted">We
                                                                                        has changed 2
                                                                                        attributes on 3
                                                                                        month
                                                                                        Ago</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapse12"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="heading12"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5">
                                                                            In an awareness campaign, it
                                                                            is vital for people to begin
                                                                            put 2 and 2 together and
                                                                            begin to recognize your
                                                                            cause. Too much or too
                                                                            little spacing, as in the
                                                                            example below, can make
                                                                            things unpleasant for the
                                                                            reader. The goal is to make
                                                                            your text as comfortable to
                                                                            read as possible. A
                                                                            wonderful serenity has taken
                                                                            possession of my entire
                                                                            soul, like these sweet
                                                                            mornings of spring which I
                                                                            enjoy with my whole heart.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="heading13">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapse13"
                                                                            aria-expanded="false">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-5.jpg') }}"
                                                                                        alt=""
                                                                                        class="avatar-xs rounded-circle shadow" />
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        New ticket
                                                                                        received</h6>
                                                                                    <small class="text-muted mb-2">User
                                                                                        <span
                                                                                            class="text-secondary">Erica245</span>
                                                                                        submitted a
                                                                                        ticket - 5 month
                                                                                        Ago</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="heading14">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapse14"
                                                                            aria-expanded="true">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0 avatar-xs">
                                                                                    <div
                                                                                        class="avatar-title bg-light text-muted rounded-circle shadow">
                                                                                        <i class="ri-user-3-fill"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Nancy Martino
                                                                                    </h6>
                                                                                    <small class="text-muted">Commented
                                                                                        on 24 Nov,
                                                                                        2021.</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapse14"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="heading14"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5 fst-italic">
                                                                            " A wonderful serenity has
                                                                            taken possession of my
                                                                            entire soul, like these
                                                                            sweet mornings of spring
                                                                            which I enjoy with my whole
                                                                            heart. Each design is a new,
                                                                            unique piece of art birthed
                                                                            into this world, and while
                                                                            you have the opportunity to
                                                                            be creative and make your
                                                                            own style choices. "
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="accordion-item border-0">
                                                                    <div class="accordion-header" id="heading15">
                                                                        <a class="accordion-button p-2 shadow-none"
                                                                            data-bs-toggle="collapse" href="#collapse15"
                                                                            aria-expanded="true">
                                                                            <div class="d-flex">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-7.jpg') }}"
                                                                                        alt=""
                                                                                        class="avatar-xs rounded-circle shadow" />
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-3">
                                                                                    <h6 class="fs-14 mb-1">
                                                                                        Lewis Arnold
                                                                                    </h6>
                                                                                    <small class="text-muted">Create
                                                                                        new project
                                                                                        buildng product
                                                                                        - 8 month
                                                                                        Ago</small>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapse15"
                                                                        class="accordion-collapse collapse show"
                                                                        aria-labelledby="heading15"
                                                                        data-bs-parent="#accordionExample">
                                                                        <div class="accordion-body ms-2 ps-5">
                                                                            <p class="text-muted mb-2">
                                                                                Every team project can
                                                                                have a velzon. Use the
                                                                                velzon to share
                                                                                information with your
                                                                                team to understand and
                                                                                contribute to your
                                                                                project.</p>
                                                                            <div class="avatar-group">
                                                                                <a href="javascript: void(0);"
                                                                                    class="avatar-group-item shadow"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-trigger="hover"
                                                                                    data-bs-placement="top" title=""
                                                                                    data-bs-original-title="Christi">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-4.jpg') }}"
                                                                                        alt=""
                                                                                        class="rounded-circle avatar-xs">
                                                                                </a>
                                                                                <a href="javascript: void(0);"
                                                                                    class="avatar-group-item shadow"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-trigger="hover"
                                                                                    data-bs-placement="top" title=""
                                                                                    data-bs-original-title="Frank Hook">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-3.jpg') }}"
                                                                                        alt=""
                                                                                        class="rounded-circle avatar-xs">
                                                                                </a>
                                                                                <a href="javascript: void(0);"
                                                                                    class="avatar-group-item shadow"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-trigger="hover"
                                                                                    data-bs-placement="top" title=""
                                                                                    data-bs-original-title=" Ruby">
                                                                                    <div class="avatar-xs">
                                                                                        <div
                                                                                            class="avatar-title rounded-circle bg-light text-primary">
                                                                                            R
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="javascript: void(0);"
                                                                                    class="avatar-group-item shadow"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-trigger="hover"
                                                                                    data-bs-placement="top" title=""
                                                                                    data-bs-original-title="more">
                                                                                    <div class="avatar-xs">
                                                                                        <div
                                                                                            class="avatar-title rounded-circle">
                                                                                            2+
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end accordion-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->
                                </div><!-- end row -->

                                <div class="card d-none">
                                    <div class="card-body">
                                        <h5 class="card-title">Recent Leads</h5>
                                        <!-- Swiper -->
                                        <div class="swiper project-swiper mt-n4">
                                            <div class="d-flex justify-content-end gap-2 mb-2">
                                                <div class="slider-button-prev">
                                                    <div class="avatar-title fs-18 rounded px-1 shadow">
                                                        <i class="ri-arrow-left-s-line"></i>
                                                    </div>
                                                </div>
                                                <div class="slider-button-next">
                                                    <div class="avatar-title fs-18 rounded px-1 shadow">
                                                        <i class="ri-arrow-right-s-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="swiper-wrapper">
                                                @foreach ($leads ?? [] as $lead)
                                                    <div class="swiper-slide">
                                                        <div
                                                            class="card profile-project-card profile-project-success mb-0">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex">
                                                                    <div class="flex-grow-1 text-muted overflow-hidden">
                                                                        <h5 class="fs-14 text-truncate mb-1">
                                                                            <a href="#"
                                                                                class="text-dark">{{ $lead->first_name }}
                                                                                {{ $lead->last_name }}</a>
                                                                        </h5>
                                                                        <p class="text-muted text-truncate mb-0">
                                                                            Last Update : <span
                                                                                class="fw-semibold text-dark">{{ $lead->updated_at ? $lead->updated_at->diffForHumans() : '' }}</span>
                                                                        </p>
                                                                    </div>
                                                                    <div class="flex-shrink-0 ms-2">
                                                                        <div class="badge badge-soft-info fs-10">
                                                                            {{ $lead->leadStatus->name ?? '' }}</div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex mt-4">
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <div>
                                                                                <h5 class="fs-12 text-muted mb-0">
                                                                                    Add. Agents :</h5>
                                                                            </div>
                                                                            <div class="avatar-group">
                                                                                @foreach ($lead->leadAgents ?? [] as $leadAgent)
                                                                                    <div class="avatar-group-item shadow">
                                                                                        <div class="avatar-xs">
                                                                                            <img src="{{ $leadAgent->agent->thumb_url }}"
                                                                                                alt=""
                                                                                                class="rounded-circle img-fluid" />
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end card body -->
                                                        </div>
                                                        <!-- end card -->
                                                    </div>
                                                @endforeach
                                                <!-- end slide item -->
                                                <div class="swiper-slide">
                                                    <div class="card profile-project-card profile-project-danger mb-0">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex">
                                                                <div class="flex-grow-1 text-muted overflow-hidden">
                                                                    <h5 class="fs-14 text-truncate mb-1">
                                                                        <a href="#" class="text-dark">Client -
                                                                            John</a>
                                                                    </h5>
                                                                    <p class="text-muted text-truncate mb-0">
                                                                        Last Update : <span class="fw-semibold text-dark">1
                                                                            hr Ago</span></p>
                                                                </div>
                                                                <div class="flex-shrink-0 ms-2">
                                                                    <div class="badge badge-soft-success fs-10">
                                                                        Completed
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex mt-4">
                                                                <div class="flex-grow-1">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <div>
                                                                            <h5 class="fs-12 text-muted mb-0">
                                                                                Members :</h5>
                                                                        </div>
                                                                        <div class="avatar-group">
                                                                            <div class="avatar-group-item shadow">
                                                                                <div class="avatar-xs">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-2.jpg') }}"
                                                                                        alt=""
                                                                                        class="rounded-circle img-fluid" />
                                                                                </div>
                                                                            </div>
                                                                            <div class="avatar-group-item shadow">
                                                                                <div class="avatar-xs">
                                                                                    <div
                                                                                        class="avatar-title rounded-circle bg-light text-primary">
                                                                                        C
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!-- end card body -->
                                                    </div><!-- end card -->
                                                </div><!-- end slide item -->
                                                <div class="swiper-slide">
                                                    <div class="card profile-project-card profile-project-info mb-0">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex">
                                                                <div class="flex-grow-1 text-muted overflow-hidden">
                                                                    <h5 class="fs-14 text-truncate mb-1">
                                                                        <a href="#" class="text-dark">Brand logo
                                                                            Design</a>
                                                                    </h5>
                                                                    <p class="text-muted text-truncate mb-0">
                                                                        Last Update : <span class="fw-semibold text-dark">2
                                                                            hr Ago</span></p>
                                                                </div>
                                                                <div class="flex-shrink-0 ms-2">
                                                                    <div class="badge badge-soft-warning fs-10">
                                                                        Inprogress
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex mt-4">
                                                                <div class="flex-grow-1">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <div>
                                                                            <h5 class="fs-12 text-muted mb-0">
                                                                                Members :</h5>
                                                                        </div>
                                                                        <div class="avatar-group">
                                                                            <div class="avatar-group-item shadow">
                                                                                <div class="avatar-xs">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-5.jpg') }}"
                                                                                        alt=""
                                                                                        class="rounded-circle img-fluid" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!-- end card body -->
                                                    </div><!-- end card -->
                                                </div><!-- end slide item -->
                                                <div class="swiper-slide">
                                                    <div class="card profile-project-card profile-project-danger mb-0">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex">
                                                                <div class="flex-grow-1 text-muted overflow-hidden">
                                                                    <h5 class="fs-14 text-truncate mb-1">
                                                                        <a href="#" class="text-dark">Project
                                                                            update</a>
                                                                    </h5>
                                                                    <p class="text-muted text-truncate mb-0">
                                                                        Last Update : <span class="fw-semibold text-dark">4
                                                                            hr Ago</span></p>
                                                                </div>
                                                                <div class="flex-shrink-0 ms-2">
                                                                    <div class="badge badge-soft-success fs-10">
                                                                        Completed
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="d-flex mt-4">
                                                                <div class="flex-grow-1">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <div>
                                                                            <h5 class="fs-12 text-muted mb-0">
                                                                                Members :</h5>
                                                                        </div>
                                                                        <div class="avatar-group">
                                                                            <div class="avatar-group-item shadow">
                                                                                <div class="avatar-xs">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-4.jpg') }}"
                                                                                        alt=""
                                                                                        class="rounded-circle img-fluid" />
                                                                                </div>
                                                                            </div>
                                                                            <div class="avatar-group-item shadow">
                                                                                <div class="avatar-xs">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-5.jpg') }}"
                                                                                        alt=""
                                                                                        class="rounded-circle img-fluid" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end card body -->
                                                    </div>
                                                    <!-- end card -->
                                                </div>
                                                <!-- end slide item -->
                                                <div class="swiper-slide">
                                                    <div class="card profile-project-card profile-project-warning mb-0">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex">
                                                                <div class="flex-grow-1 text-muted overflow-hidden">
                                                                    <h5 class="fs-14 text-truncate mb-1">
                                                                        <a href="#" class="text-dark">Chat
                                                                            App</a>
                                                                    </h5>
                                                                    <p class="text-muted text-truncate mb-0">
                                                                        Last Update : <span class="fw-semibold text-dark">1
                                                                            hr Ago</span></p>
                                                                </div>
                                                                <div class="flex-shrink-0 ms-2">
                                                                    <div class="badge badge-soft-warning fs-10">
                                                                        Inprogress
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="d-flex mt-4">
                                                                <div class="flex-grow-1">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <div>
                                                                            <h5 class="fs-12 text-muted mb-0">
                                                                                Members :</h5>
                                                                        </div>
                                                                        <div class="avatar-group">
                                                                            <div class="avatar-group-item shadow">
                                                                                <div class="avatar-xs">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-4.jpg') }}"
                                                                                        alt=""
                                                                                        class="rounded-circle img-fluid" />
                                                                                </div>
                                                                            </div>
                                                                            <div class="avatar-group-item shadow">
                                                                                <div class="avatar-xs">
                                                                                    <img src="{{ URL::asset('assets/images/users/avatar-5.jpg') }}"
                                                                                        alt=""
                                                                                        class="rounded-circle img-fluid" />
                                                                                </div>
                                                                            </div>
                                                                            <div class="avatar-group-item shadow">
                                                                                <div class="avatar-xs">
                                                                                    <div
                                                                                        class="avatar-title rounded-circle bg-light text-primary">
                                                                                        A
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end card body -->
                                                    </div>
                                                    <!-- end card -->
                                                </div>
                                                <!-- end slide item -->
                                            </div>

                                        </div>

                                    </div>
                                    <!-- end card body -->
                                </div><!-- end card -->

                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <div class="tab-pane fade" id="activities" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Activities</h5>
                                <div class="acitivity-timeline" id="activities-log"></div>
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <div class="tab-pane fade" id="activities" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Activities</h5>
                                <div class="acitivity-timeline">
                                    <div class="acitivity-item d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="{{ URL::asset('assets/images/users/avatar-1.jpg') }}"
                                                alt="" class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Oliver Phillips <span
                                                    class="badge bg-soft-primary text-primary align-middle">New</span>
                                            </h6>
                                            <p class="text-muted mb-2">We talked about a project on
                                                linkedin.</p>
                                            <small class="mb-0 text-muted">Today</small>
                                        </div>
                                    </div>
                                    <div class="acitivity-item py-3 d-flex">
                                        <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                            <div class="avatar-title bg-soft-success text-success rounded-circle shadow">
                                                N
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Nancy Martino <span
                                                    class="badge bg-soft-secondary text-secondary align-middle">In
                                                    Progress</span></h6>
                                            <p class="text-muted mb-2"><i class="ri-file-text-line align-middle ms-2"></i>
                                                Create new project Buildng product</p>
                                            <div class="avatar-group mb-2">
                                                <a href="javascript: void(0);" class="avatar-group-item shadow"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                    data-bs-original-title="Christi">
                                                    <img src="{{ URL::asset('assets/images/users/avatar-4.jpg') }}"
                                                        alt="" class="rounded-circle avatar-xs" />
                                                </a>
                                                <a href="javascript: void(0);" class="avatar-group-item shadow"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                    data-bs-original-title="Frank Hook">
                                                    <img src="{{ URL::asset('assets/images/users/avatar-3.jpg') }}"
                                                        alt="" class="rounded-circle avatar-xs" />
                                                </a>
                                                <a href="javascript: void(0);" class="avatar-group-item shadow"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                    data-bs-original-title=" Ruby">
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title rounded-circle bg-light text-primary">
                                                            R
                                                        </div>
                                                    </div>
                                                </a>
                                                <a href="javascript: void(0);" class="avatar-group-item shadow"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                    data-bs-original-title="more">
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title rounded-circle">
                                                            2+
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <small class="mb-0 text-muted">Yesterday</small>
                                        </div>
                                    </div>
                                    <div class="acitivity-item py-3 d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="{{ URL::asset('assets/images/users/avatar-2.jpg') }}"
                                                alt="" class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Natasha Carey <span
                                                    class="badge bg-soft-success text-success align-middle">Completed</span>
                                            </h6>
                                            <p class="text-muted mb-2">Adding a new event with
                                                attachments</p>
                                            <div class="row">
                                                <div class="col-xxl-4">
                                                    <div class="row border border-dashed gx-2 p-2 mb-2">
                                                        <div class="col-4">
                                                            <img src="{{ URL::asset('assets/images/small/img-2.jpg') }}"
                                                                alt="" class="img-fluid rounded shadow" />
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-4">
                                                            <img src="{{ URL::asset('assets/images/small/img-3.jpg') }}"
                                                                alt="" class="img-fluid rounded shadow" />
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-4">
                                                            <img src="{{ URL::asset('assets/images/small/img-4.jpg') }}"
                                                                alt="" class="img-fluid rounded shadow" />
                                                        </div>
                                                        <!--end col-->
                                                    </div>
                                                    <!--end row-->
                                                </div>
                                            </div>
                                            <small class="mb-0 text-muted">25 Nov</small>
                                        </div>
                                    </div>
                                    <div class="acitivity-item py-3 d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="{{ URL::asset('assets/images/users/avatar-6.jpg') }}"
                                                alt="" class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Bethany Johnson</h6>
                                            <p class="text-muted mb-2">added a new member to velzon
                                                dashboard</p>
                                            <small class="mb-0 text-muted">19 Nov</small>
                                        </div>
                                    </div>
                                    <div class="acitivity-item py-3 d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs acitivity-avatar">
                                                <div class="avatar-title rounded-circle bg-soft-danger text-danger shadow">
                                                    <i class="ri-shopping-bag-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Your order is placed <span
                                                    class="badge bg-soft-danger text-danger align-middle ms-1">Out
                                                    of Delivery</span></h6>
                                            <p class="text-muted mb-2">These customers can rest assured
                                                their order has been placed.</p>
                                            <small class="mb-0 text-muted">16 Nov</small>
                                        </div>
                                    </div>
                                    <div class="acitivity-item py-3 d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="{{ URL::asset('assets/images/users/avatar-7.jpg') }}"
                                                alt="" class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Lewis Pratt</h6>
                                            <p class="text-muted mb-2">They all have something to say
                                                beyond the words on the page. They can come across as
                                                casual or neutral, exotic or graphic. </p>
                                            <small class="mb-0 text-muted">22 Oct</small>
                                        </div>
                                    </div>
                                    <div class="acitivity-item py-3 d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs acitivity-avatar">
                                                <div class="avatar-title rounded-circle bg-soft-info text-info shadow">
                                                    <i class="ri-line-chart-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Monthly sales report</h6>
                                            <p class="text-muted mb-2"><span class="text-danger">2 days
                                                    left</span> notification to submit the monthly sales
                                                report. <a href="javascript:void(0);"
                                                    class="link-warning text-decoration-underline">Reports
                                                    Builder</a></p>
                                            <small class="mb-0 text-muted">15 Oct</small>
                                        </div>
                                    </div>
                                    <div class="acitivity-item d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="{{ URL::asset('assets/images/users/avatar-8.jpg') }}"
                                                alt="" class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">New ticket received <span
                                                    class="badge bg-soft-success text-success align-middle">Completed</span>
                                            </h6>
                                            <p class="text-muted mb-2">User <span class="text-secondary">Erica245</span>
                                                submitted a
                                                ticket.</p>
                                            <small class="mb-0 text-muted">26 Aug</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane fade" id="projects" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="row" id="listings-list"></div>
                                <!--end row-->
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane fade" id="documents" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <h5 class="card-title flex-grow-1 mb-0">Contacts</h5>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table id="datatable" class="table align-middle" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" style="width: 50px;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="checkAll" value="option"
                                                                    data-com.bitwarden.browser.user-edited="yes">
                                                            </div>
                                                        </th>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Company</th>
                                                        <th>Email ID</th>
                                                        <th>Mobile</th>
                                                        <th>Gender</th>
                                                        <th>Type</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="settingsTab" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <h5 class="card-title flex-grow-1 mb-0">Telegram Setting</h5>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        @if (!empty($user->telegram_username))
                                            <p>Your telegram user name is {{ $user->telegram_username }}</p>
                                            <p>Telegram Notification : @if ($user->telegram_chatid)
                                                    <span class="badge badge-outline-success">Active</span>
                                                @else
                                                    <span class="badge badge-outline-danger">Disabled</span> <a
                                                        href="javascript:void(0)" id="generateTelegramOtp">Enable
                                                        Now</a>
                                                @endif
                                            </p>
                                            <p id="telegramSetupInstruction" class="d-none">

                                                Send this <b id="telegramOtp"></b> otp to our telegram bot <b><a
                                                        href="https://t.me/ax_secondary_crm_bot" target="_blank">AX
                                                        Capital
                                                        Bot</a></b> to verify your account. <br>
                                                <br> After sending the message to our bot. click this button. <br>
                                                <button class="btn btn-primary" id="verifyTelegramOtp">Verify</button>
                                            </p>
                                        @else
                                            <div class="alert alert-borderless shadow alert-warning" role="alert">
                                                <strong>Attention</strong> Please Set Your telegram user name first!
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end tab-pane-->
                </div>
                <!--end tab-content-->
            </div>
        </div>
        <!--end col-->

    </div>


    <div class=" d-none" id="cloneCode">
        <div class="row mb-3 ">
            <div class="col-6 col-md-4">
                <select name="language_name[]" id="" class="select2_ form-control languageSelect2">
                    @foreach (config('languages') as $key => $value)
                        <option value="{{ $key }}"> {{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 col-md-4">
                <input type="range" name="language_level[]" id="language_level" class="ranger language_level" value="0" class="" step="10"> <span class="level_value_text">0</span> %
            </div>
            <div class="col-2 col-md-2">
                <span class="cursur-pointer removeRowAdded">Remove</span>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/swiper/swiper.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="{{ URL::asset('assets/js/pages/profile.init.js') }}"></script>
    {{-- <script src=" "></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js" type="text/javascript">
    </script>



    <script>
        $(function() {
            getLanguageData();
        })

        function getActivityLog() {
            $("#activityLogDiv").find(".acitivity-timeline").html("");
            $.ajax({
                url: "{{ route('ajax.activity-log') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    causer_id: "{{ $user->id }}"
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
                    $("#activities-log").html(logs);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }
        // getActivityLog()

        $(document).ready(function() {
            $("#edit-profile-btn").click(function(e) {
                e.preventDefault();
                $("#bio_section").toggle();
                $("#edit_profile").toggle();

            });


            $(document).on('change', '.language_level', function() {
                var level_value = $(this).val();
                $(this).closest(".row").find(".level_value_text").text(level_value);
            });

            $(document).on('click', '#addLanguage', function() {
                var clonedContent = $("#cloneCode .row").clone();
                clonedContent.find(".languageSelect2").each(function() {
                    var languageSelect = $(this);
                    languageSelect.find('option').each(function() {
                        var optionValue = $(this).val();
                        $(".language_name").each(function() {
                            if ($(this).text() == optionValue) {
                                languageSelect.find('option[value="' + optionValue +
                                    '"]').remove();
                            }
                        });
                    });
                });
                // initiate select2 for this cloned row
                clonedContent.find(".select2_").select2();
                $("#appendHere").append(clonedContent);

            });

            $(document).on('click', '.removeRowAdded', function() {
                $(this).closest(".row").remove();
            });


            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Language ?</p></div></div>',
                    showCancelButton: !0,
                    confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                    confirmButtonText: "Yes, Delete It!",
                    cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                    buttonsStyling: !1,
                    showCloseButton: !0
                }) .then(function(t) {
                        if (t.isConfirmed) {
                            $.ajax({
                                url: "{{ route('admin.my-account.profile.deleteLanguages') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    id: id,
                                },
                                success: function(response) {
                                    console.table(response.status);
                                    if (response.status == 1) {
                                        showToastify(response.msg, "success")
                                        getLanguageData();
                                    }

                                }
                            });
                    }
                })
            });

            $(document).on('click', '.updatebtn', function() {
                var currentbtn = $(this).closest(".currentbtn");
                if (currentbtn.length > 0) {
                    var languageLevelElement = currentbtn.find(".language_level");
                    var languageNameElement = currentbtn.find(".language_name");
                    if (languageLevelElement.length > 0 && languageNameElement.length > 0) {
                        var language_level = languageLevelElement.val();
                        var language_name = languageNameElement.text();
                        console.log("Language Level:", language_level);
                        console.log("Language Name:", language_name);
                        $.ajax({
                            url: "{{ route('admin.my-account.profile.saveLanguages') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: "{{ $user->id }}",
                                key: language_name,
                                'value': language_level,
                                'tag': 'language'
                            },
                            success: function(response) {
                                console.table(response.status);
                                if (response.status == 1) {
                                    showToastify(response.msg, "success")
                                    getLanguageData();
                                }

                            }
                        });
                    }
                }
            });


            $('#save_language').click(function() {
                let language = $('#language_id').val();
                let level = $('#language_level').val();
                $.ajax({
                    url: "{{ route('admin.my-account.profile.saveLanguages') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: "{{ $user->id }}",
                        key: language,
                        'value': level,
                        'tag': 'language'
                    },
                    success: function(response) {
                        console.table(response.status);
                        if (response.status == 1) {
                            showToastify(response.msg, "success")
                            getLanguageData();
                        }

                    }
                });
            })





            $(document).on('submit', '#change_user_password', function(event) {
                $("#change_password_btn").prop('disabled', true);
                event.preventDefault();
                // $('#change_user_password').validate({
                //     rules: {
                //         password: {
                //             minlength: 6,
                //         },
                //         password_confirm: {
                //             minlength: 6,
                //             equalTo: "#password"
                //         }
                //     }
                // });
                var formData = new FormData($(this)[0]);
                $.ajax({
                    url: "{{ route('admin.user.change_user_password') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $(".password_feilds").val("");
                            $("#edit_profile").toggle();
                            $("#bio_section").toggle();
                        } else if (result.status == '2') {
                            showToastify(result.msg, "error")
                        } else {
                            showToastify(result.msg, "error")
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
                            showToastify(data.message, "error")
                        }
                    },
                });
            });


            $.ajax({
                url: "{{ route('ajax.properties') }}",
                type: "get",
                data: {
                    agent_id: "{{ $user->id }}"
                },
                success: function(result) {
                    let data = result.data;
                    let listings = "";
                    data?.map(item => {
                        let portals = "";
                        item.portals?.map(portal => {
                            portals += `<div class="avatar-group-item shadow">
                                            <div class="avatar-xs">
                                                <img
                                                    src="${portal.portal.thumbnail}"
                                                    alt=""
                                                    class="rounded-circle img-fluid"/>
                                            </div>
                                        </div>`
                        })
                        listings += `<div class="col-xxl-3 col-sm-6" title="${item.for}">
                                        <div class="card profile-project-card profile-project-${item.for == "SALE" ? "warning" : "success"}">
                                                <div class="card-body p-4">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1 text-muted overflow-hidden">
                                                        <h5 class="fs-14 text-truncate">
                                                            <a href="javascript:void(0)" class="text-dark">${item.ref_no}</a>
                                                        </h5>
<!--                                                        // <p class="text-muted text-truncate mb-0">Last Update : <span class="fw-semibold text-dark">2 year-->
<!--                                                        //         Ago</span></p>-->
                                                    </div>
                                                    <div class="flex-shrink-0 ms-2">
                                                        <div class="badge badge-soft-warning fs-10">
                                                            ${item.publish_status}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex mt-4">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div>
                                                                <h5 class="fs-12 text-muted mb-0">
                                                                    Portals :</h5>
                                                            </div>
                                                            <div class="avatar-group">
                                                                ${portals}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`
                    })
                    $("#listings-list").html(listings)
                }
            });
        })
        let dataTable = $('#datatable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search contacts"
            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.work.contacts.datatable-for-profile-view') }}",
                type: "post",
                data: function(d) {
                    d.agent_id = "{{ $user->id }}";
                },
                error: function() {
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
            }, {
                data: 'id',
                name: 'id',
            }, {
                data: 'first_name',
                name: 'first_name',
            }, {
                data: 'company',
                name: 'company',
                orderable: false,
            }, {
                data: 'email',
                name: 'email',
                orderable: false,
            }, {
                data: 'mobile',
                name: 'mobile',
                orderable: false,
            }, {
                data: 'gender',
                name: 'gender',
                orderable: false,
            }, {
                data: 'contact_type_id',
                name: 'contact_type_id',
                orderable: false,
            }, ],
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
                        return `<a href="javascript:void(0)" class="text-primary">` + data.first_name +
                            ` ` + data.last_name + `</a>`;
                    }
                },
                {
                    "targets": 7,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return data.contact_type ? data.contact_type.name : "-";
                    }
                },
            ]
        });

        $("#generateTelegramOtp").click(function() {
            console.log('testsr');
            $("#telegramSetupInstruction").removeClass("d-none");
            $.ajax({
                url: "{{ route('admin.user.users.telegram.send.otp') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: "{{ $user->id }}",
                },
                success: function(response) {
                    if (response.status == 1) {
                        $("#telegramOtp").html(response.otp);
                    } else {

                    }
                }
            });
        })
        $("#verifyTelegramOtp").click(function() {
            $.ajax({
                url: "{{ route('admin.user.users.telegram.verify.otp') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: "{{ $user->id }}",
                },
                success: function(response) {
                    if (response.status == 1) {
                        showToastify(response.msg, "success")
                    } else {
                        showToastify(response.msg, "error")
                    }
                }
            });
        });

        function getLanguageData() {
            $.ajax({
                url: "{{ route('admin.my-account.profile.getLanguages') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: "{{ $user->id }}",
                },
                success: function(response) {
                    var responseHTML = $.parseHTML(response);

                    // Get an array of language_name values
                    var languageNameValues = $(".language_name").map(function() {
                        return $(this).text();
                    }).get();

                    // Loop through each option in the select2 language_id select box
                    $("#language_id option").each(function() {
                        var optionValue = $(this).val();
                        if (languageNameValues.includes(optionValue)) {
                            // Remove the option if its value matches a language_name value
                            $(this).remove();
                        }
                    });

                    $('#append').html(response);
                }
            });
        }

        $(document).ready(function() {
            $('#save_languages').click(function() {
                //prvent default action
                event.preventDefault();
                var formData = new FormData($('#bulkLanguage')[0]);
                $.ajax({
                    url: "{{ route('admin.my-account.profile.saveLanguages') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $('#appendHere').html("");
                            getLanguageData();

                        } else if (result.status == '2') {
                            showToastify(result.msg, "error")
                        } else {
                            showToastify(result.msg, "error")
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
                            showToastify(data.message, "error")
                        }
                    },
                });
            });
        });
    </script>
@endsection
