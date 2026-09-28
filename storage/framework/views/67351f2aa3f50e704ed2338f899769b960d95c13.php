<header id="page-topbar">


    <div class="layout-width">

        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="<?php echo e(URL::asset('assets/images/logo-sm.png')); ?>" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="<?php echo e(URL::asset('assets/images/logo/logo.png')); ?>" alt="" height="48">
                        </span>
                    </a>

                    <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="<?php echo e(URL::asset('assets/images/logo-sm.png')); ?>" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="<?php echo e(URL::asset('assets/images/logo/logo.png')); ?>" alt="" height="48">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger "
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- App Search-->

            </div>

            

            <div class="alert alert-danger bg-danger text-center d-none text-white" id="agentReportStatus"
                style="max-width: 50%;">
                <span><b>Account Suspension Alert! </b></span><span id="agentReportStatusBar"></span> <b><span
                        id="liveTime"></span></b>

            </div>

            <?php if (is_impersonating($guard = null)) : ?>
                <a href="<?php echo e(route('impersonate.leave')); ?>" class="btn btn-danger">Leave impersonation (Impersonating as
                    <?php echo e(auth()->user()->full_name); ?>)</a>
            <?php endif; ?>

            <div class="d-flex align-items-center">





                <!-- Static Backdrop -->


                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="globalSearchOpenBtn">

                        <i class='bx bx-search fs-22'></i>

                    </button>
                </div>


                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle "
                        data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>





                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class='bx bx-bell fs-22'></i>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"
                            id="topNotiBadge">0<span class="visually-hidden">unread messages</span></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs d-none">
                                        <span class="badge badge-soft-light fs-13"> 4 New</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-2 pt-2">
                                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true"
                                    id="notificationItemsTab" role="tablist">
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab"
                                            role="tab" aria-selected="true">
                                            Recent (5)
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light d-none">
                                        <a class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab"
                                            aria-selected="false">
                                            Messages
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light d-none">
                                        <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab"
                                            aria-selected="false">
                                            Alerts
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>

                        <div class="tab-content" id="notificationItemsTabContent">
                            <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <div id="notificationTopBarList"></div>







                                    <div class="my-3 text-center">
                                        <a href="<?php echo e(route('admin.message.inbox')); ?>"
                                            class="btn btn-soft-success waves-effect waves-light">View
                                            All Notifications <i class="ri-arrow-right-line align-middle"></i></a>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel"
                                aria-labelledby="messages-tab">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <img src="<?php echo e(URL::asset('assets/images/users/avatar-3.jpg')); ?>"
                                                class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                            <div class="flex-1">
                                                <a href="#!" class="stretched-link">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">James Lemire</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">We talked about a project on linkedin.</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 30 min ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="messages-notification-check01">
                                                    <label class="form-check-label"
                                                        for="messages-notification-check01"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <img src="<?php echo e(URL::asset('assets/images/users/avatar-2.jpg')); ?>"
                                                class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                            <div class="flex-1">
                                                <a href="#!" class="stretched-link">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">Angela Bernier</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">Answered to your comment on the cash flow
                                                        forecast's
                                                        graph 🔔.</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 2 hrs ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="messages-notification-check02">
                                                    <label class="form-check-label"
                                                        for="messages-notification-check02"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <img src="<?php echo e(URL::asset('assets/images/users/avatar-6.jpg')); ?>"
                                                class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                            <div class="flex-1">
                                                <a href="#!" class="stretched-link">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">Kenneth Brown</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">Mentionned you in his comment on 📃 invoice
                                                        #12501.
                                                    </p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 10 hrs ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="messages-notification-check03">
                                                    <label class="form-check-label"
                                                        for="messages-notification-check03"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <img src="<?php echo e(URL::asset('assets/images/users/avatar-8.jpg')); ?>"
                                                class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                            <div class="flex-1">
                                                <a href="#!" class="stretched-link">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">Maureen Gibson</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">We talked about a project on linkedin.</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 3 days ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="messages-notification-check04">
                                                    <label class="form-check-label"
                                                        for="messages-notification-check04"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-3 text-center">
                                        <button type="button"
                                            class="btn btn-soft-success waves-effect waves-light">View
                                            All Messages <i class="ri-arrow-right-line align-middle"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade p-4" id="alerts-tab" role="tabpanel"
                                aria-labelledby="alerts-tab">
                                <div class="w-25 w-sm-50 pt-3 mx-auto">
                                    <img src="<?php echo e(URL::asset('assets/images/svg/bell.svg')); ?>" class="img-fluid"
                                        alt="user-pic">
                                </div>
                                <div class="text-center pb-5 mt-2">
                                    <h6 class="fs-18 fw-semibold lh-base">Hey! You have no any notifications </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
                    'AX Academy Login',
                    'AX Contacts Login',
                    'Bitrix CRM Login',
                    'AX Telelog Login',
                    'AX Spot
                    Login',
                    'AX Deal Login'
                    ])): ?>
                    <div class="dropdown topbar-head-dropdown ms-1 header-item">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle "
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class='bx bx-category-alt fs-22'></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg p-0 dropdown-menu-end">
                            <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fw-semibold fs-15"> AX Apps </h6>
                                    </div>
                                    <div class="col-auto d-none">
                                        <a href="#!" class="btn btn-sm btn-soft-info "> View All Apps
                                            <i class="ri-arrow-right-s-line align-middle"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2">
                                <div class="row g-0">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('AX Academy Login')): ?>
                                        <div class="col-4">
                                            <a class="dropdown-icon-item" href="<?php echo e(axAcademyUrl(auth()->user()->email)); ?>"
                                                target="_blank">
                                                <span
                                                    class=" fs-2 btn btn-icon btn-topbar btn-ghost-secondary rounded-circle bg-soft-primary d-inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-award text-primary">
                                                        <circle cx="12" cy="8" r="7"></circle>
                                                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                                    </svg>
                                                </span>
                                                <span>AX ACADEMY</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(auth()->user()->parent_id != 32 && auth()->user()->id != 32): ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('AX Contacts Login')): ?>
                                            <div class="col-4">
                                                <a class="dropdown-icon-item" id="ax-contacts-login"
                                                    href="<?php echo e(route('contactLogin')); ?>" target="_blank">
                                                    <span
                                                        class=" fs-2 btn btn-icon btn-topbar btn-ghost-secondary rounded-circle bg-soft-warning d-inline-flex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                            class="feather feather-users text-warning">
                                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                            <circle cx="9" cy="7" r="4"></circle>
                                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                        </svg>
                                                    </span>
                                                    <span>AX CONTACTS</span>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Bitrix CRM Login')): ?>
                                        <div class="col-4">
                                            <a class="dropdown-icon-item" href="https://crm.axcap.ae/" target="_blank">
                                                <span
                                                    class=" fs-2 btn btn-icon btn-topbar btn-ghost-secondary rounded-circle bg-soft-danger d-inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-briefcase text-danger">
                                                        <rect x="2" y="7" width="20" height="14" rx="2"
                                                            ry="2"></rect>
                                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                                    </svg>
                                                </span>

                                                <span>Bitrix CRM</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('AX Listings')): ?>
                                        <div class="col-4">
                                            <a class="dropdown-icon-item" id="ax-listings-login"
                                                href="<?php echo e(route('front.auth.listing')); ?>" target="_blank">

                                                <span
                                                    class=" fs-2 btn btn-icon btn-topbar btn-ghost-secondary rounded-circle bg-soft-primary-gold d-inline-flex">
                                                    <i class="ri  ri-map-pin-line text-primary-gold">

                                                    </i>
                                                </span>
                                                <span>AX LISTING</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('AX Telelog Login')): ?>
                                        <div class="col-4">
                                            <a class="dropdown-icon-item" id="ax-telelogs-login"
                                                href="<?php echo e(route('telelogLogin')); ?>" target="_blank">
                                                <span
                                                    class=" fs-2 btn btn-icon btn-topbar btn-ghost-secondary rounded-circle bg-soft-info d-inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-phone-call text-info">
                                                        <path
                                                            d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                                        </path>
                                                    </svg>
                                                </span>
                                                <span>AX TELELOG</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                        <?php /*
                                    @can('AX Spot Login')
                                        <div class="col-4">
                                            <a class="dropdown-icon-item" href="https://spot.axcrm.ae" target="_blank">
                                                <span
                                                    class=" fs-2 btn btn-icon btn-topbar btn-ghost-secondary rounded-circle bg-soft-dark d-inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-map text-dark">
                                                        <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6">
                                                        </polygon>
                                                        <line x1="8" y1="2" x2="8" y2="18">
                                                        </line>
                                                        <line x1="16" y1="6" x2="16" y2="22">
                                                        </line>
                                                    </svg>
                                                </span>
                                                <span>AX SPOT</span>
                                            </a>
                                        </div>
                                    @endcan

                                    */ ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('AX Chat Login')): ?>
                                        <div class="col-4">
                                            <a class="dropdown-icon-item" id="ax-chats-login"
                                                href="<?php echo e(route('chatLogin')); ?>" target="_blank">
                                                <span
                                                    class=" fs-2 btn btn-icon btn-topbar btn-ghost-secondary rounded-circle bg-soft-success d-inline-flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-message-circle text-success ">
                                                        <path
                                                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                                        </path>
                                                    </svg> </span>
                                                <span>AX CHAT</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('AX Deal Login')): ?>
                                            <div class="col-4">
                                                <a class="dropdown-icon-item" id="ax-chats-login"
                                                   href="<?php echo e(route('dealLogin')); ?>" target="_blank">
                                                <span
                                                    class=" fs-2 btn btn-icon btn-topbar btn-ghost-secondary rounded-circle bg-soft-warning d-inline-flex">
                                                   <i class="mdi mdi-handshake-outline ax-text"></i>
                                                    </span>
                                                    <span>AX DEALS</span>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <div class="col"></div>
                                </div>

                                <div class="row g-0 d-none">
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="#!">
                                            <img src="<?php echo e(URL::asset('assets/images/brands/dropbox.png')); ?>"
                                                alt="dropbox">
                                            <span>Dropbox</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="#!">
                                            <img src="<?php echo e(URL::asset('assets/images/brands/mail_chimp.png')); ?>"
                                                alt="mail_chimp">
                                            <span>Mail Chimp</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="#!">
                                            <img src="<?php echo e(URL::asset('assets/images/brands/slack.png')); ?>" alt="slack">
                                            <span>Slack</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn " id="page-header-user-dropdown" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user"
                                src="<?php echo e(auth()->user()->thumb_url ?? URL::asset('assets/images/users/avatar-1.jpg')); ?>"
                                alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span
                                    class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?php echo e(Auth::user()?->name); ?></span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">
                                    <?php echo e(auth()?->user()?->position ?? (auth()?->user()?->roles[0]?->name ?? '-')); ?></span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome <?php echo e(auth()->user()->first_name); ?></h6>
                        <a class="dropdown-item" href="<?php echo e(route('admin.my-account.profile.index')); ?>"><i
                                class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Profile</span></a>

                        <a href="javascript:;" class="dropdown-item" data-bs-toggle="offcanvas"
                            data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas"><i
                                class="mdi mdi-spin mdi-cog-outline  text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Theme Customizer</span></a>



                        

                        <a class="dropdown-item " href="javascript:void();"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="bx bx-power-off font-size-16 align-middle me-1"></i> <span
                                key="t-logout"><?php echo app('translator')->get('translation.logout'); ?></span></a>
                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST"
                            style="display: none;">
                            <?php echo csrf_field(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/layouts/topbar.blade.php ENDPATH**/ ?>