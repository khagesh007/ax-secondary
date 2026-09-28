@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #pf_rank_meter,
        #bayut_rank_meter,
        #dubbizle_rank_meter {
            max-width: 600px;
            max-height: 400px;
            /* Add other styles as needed */
        }
    </style>
    <style>
        .morris-chart {
            font-family: Arial, sans-serif;
        }

        .morris-chart .morris-label {
            font-size: 14px;
            fill: #333333;
        }

        .ax-grad-background {
            color: #5e5e5e !important;
            background: linear-gradient(180deg, rgb(211, 161, 136) 10%, rgb(255, 255, 255) 25%, rgb(211, 161, 136) 40%, rgb(211, 161, 136) 60%, rgb(211 161 135 / 66%) 75%, rgb(211, 161, 136) 93%);
        }
    </style>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <!-- <div class="row d-none" id="notification-request-tab">

                                                    <div class="col-12">
                                                        <div class="alert alert-info alert-dismissible alert-label-icon rounded-label shadow fade show"
                                                             role="alert">
                                                            <i class="ri-airplay-line label-icon"></i><strong>Hi {{ auth()?->user()?->name }}</strong> - You can
                                                            receive
                                                            notification in your web browser, you need to enable this

                                                            <button type="button" class="btn rounded-pill btn-info waves-effect waves-light"
                                                                    id="notification-show-button">Check if enabled
                                                            </button>
                                                            <button class="btn rounded-pill btn-danger waves-effect waves-light" id="permission-request-button">
                                                                Request
                                                                permissions
                                                            </button>
                                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                                                        </div>
                                                    </div>
                                                </div> -->
    @include('admin.dashboard.partials._calender_notification')

    <div class="row">

        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    @include('admin.dashboard.partials._dropdown')
                </div>
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Listings</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value "
                                                data-target="{{ $propertyCount ?? 0 }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.listing.index') }}"
                                            class="text-decoration-underline ax-text">View all listings</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white ax-grad-background rounded fs-3">
                                            <i class="bx bx-home-circle"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Contacts</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $contactCount ?? 0 }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.work.contacts.index') }}"
                                            class="text-decoration-underline ax-text">View all contacts</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white ax-grad-background rounded fs-3">
                                            <i class="bx bx-user-circle"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Leads</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $leadCount ?? 0 }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.work.leads.index') }}"
                                            class="text-decoration-underline ax-text">View all leads</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white ax-grad-background rounded fs-3">
                                            <i class="bx bx-phone-call"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Deals</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $dealCount ?? 0 }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.work.deals.index') }}"
                                            class="text-decoration-underline ax-text">View all deals</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white ax-grad-background rounded fs-3">
                                            <i class="bx bx-wallet"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Listing Overview</h4>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="text-center uk-vertical-align-middle mt-4" id="loader_listingOverviewBarChart">
                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div id="listingOverviewBarChart"
                                    data-colors='["--vz-success","--vz-primary", "--vz-danger",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                                    class="apex-charts" dir="ltr"></div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xl-4">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header align-items-center border-0 d-flex">

                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab"
                                                href="#recent-published-listing" role="tab"
                                                aria-selected="false">Published Listings</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" onclick="recentListingLeads()"
                                                href="#recent-listing-leads" role="tab" aria-selected="false">Listing
                                                Leads</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#recent-leads"
                                                onclick="recentLeads()" role="tab" aria-selected="true">Leads</a>
                                        </li>
                                    </ul><!-- end ul -->
                                </div>
                            </div><!-- end card header -->


                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <div class="tab-pane active" id="recent-published-listing" role="tabpanel">
                                        <div class="text-center uk-vertical-align-middle mt-4"
                                            id="loader_listingNotChangedBody">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div class=" table-card">
                                            <table class="table table-nowrap table-centered align-middle">


                                                <tbody id="listingNotChangedBody">


                                                </tbody><!-- end tbody -->

                                            </table><!-- end table -->
                                        </div>
                                    </div><!-- end tabpane -->

                                    <div class="tab-pane" id="recent-listing-leads" role="tabpanel">
                                        <div class="text-center uk-vertical-align-middle mt-4"
                                            id="loader_recent_listing_lead_loader">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div class=" table-card">
                                            <table class="table table-nowrap table-centered align-middle">


                                                <tbody id="recentListingLeadChangedBody">


                                                </tbody><!-- end tbody -->

                                            </table><!-- end table -->
                                        </div>
                                    </div><!-- end tab pane -->
                                    <div class="tab-pane" id="recent-leads" role="tabpanel">
                                        <div class="text-center uk-vertical-align-middle mt-4"
                                            id="loader_recent_lead_loader">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div class=" table-card">
                                            <table class="table table-nowrap table-centered align-middle">


                                                <tbody id="recentLeadChangedBody">


                                                </tbody><!-- end tbody -->

                                            </table><!-- end table -->
                                        </div>
                                    </div><!-- end tab pane -->
                                </div><!-- end tab pane -->

                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xl-4">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Recent Activities</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn show" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="true">
                                            <span class="text-muted fs-10">View All</span>
                                        </a>

                                    </div>
                                </div>
                            </div>


                            <div class="card-body">
                                <div>
                                    <button type="button" onclick="filterEvents('')"
                                        class="btn btn-soft-secondary btn-sm">
                                        ALL
                                    </button>
                                    @foreach ($event_types as $et)
                                        <button type="button" onclick="filterEvents('{{ $et->name }}')"
                                            class="btn btn-soft-secondary btn-sm">
                                            {{ $et->name }}
                                        </button>
                                    @endforeach


                                </div>
                                <div>

                                    <div class="pe-2 me-n1 mb-3" data-simplebar="init" style="height: 190px">
                                        <div class="simplebar-wrapper" style="margin: 0px -8px 0px 0px;">
                                            <div class="simplebar-height-auto-observer-wrapper">
                                                <div class="simplebar-height-auto-observer"></div>
                                            </div>
                                            <div class="simplebar-mask">
                                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                                    <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                                        aria-label="scrollable content"
                                                        style="height: 100%; overflow: hidden scroll;">
                                                        <div class="simplebar-content" style="padding: 0px 8px 0px 0px;">
                                                            <div id="upcoming-event-list1">


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="simplebar-placeholder" style="width: auto; height: 2511px;"></div>
                                        </div>
                                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                                        </div>
                                        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                                            <div class="simplebar-scrollbar"
                                                style="height: 63px; display: block; transform: translate3d(0px, 24px, 0px);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>

                </div>

                <div class="row">


                    <div class="col-xl-6 ">
                        <div class="card" style="min-height: 423px;">
                            <div class="card-header align-items-center border-0 d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Leads</h4>
                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#lead-status-tab"
                                                role="tab" aria-selected="false">Status</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" onclick="leadSourcePieChart()" data-bs-toggle="tab"
                                                href="#lead-source-tab" role="tab" aria-selected="true">Source</a>
                                        </li>
                                    </ul><!-- end ul -->
                                </div>
                            </div><!-- end card header -->
                            <div class="card-body p-0">
                                <div class="tab-content p-0">
                                    <div class="tab-pane active" id="lead-status-tab" role="tabpanel">
                                        <div class="text-center uk-vertical-align-middle" id="loader_lead_status_pie">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div id="lead-status-pie"
                                            data-colors='["--vz-primary","#a1a1a1",  "--vz-warning", "--vz-info","#a6d8a6eb", "#35c035","--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                            class="e-charts"></div>
                                    </div><!-- end tabpane -->

                                    <div class="tab-pane" id="lead-source-tab" role="tabpanel"
                                        style="
    overflow-y: scroll;
    height: 370px;
">
                                        <div class="text-center uk-vertical-align-middle" id="loader_lead_source_pie">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div id="lead-source-pie"
                                            data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                            class="e-charts"></div>
                                    </div><!-- end tab pane -->
                                </div><!-- end tab pane -->
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xxl-6 col-lg-6 ">
                        <div class="card card-height-100 mb-3" style="min-height: 423px;">
                            <div class="card-header align-items-center border-0 d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Lead Status (Last 6 months)</h4>
                            </div>
                            <div class="card-body" style=" max-height: 370px;">
                                <div class="text-center uk-vertical-align-middle" id="loader_leadStatusLineChart">
                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div id="leadStatusLineCharts"
                                    data-colors="[&quot;--vz-success&quot;, &quot;--vz-danger&quot;]" class="apex-charts"
                                    dir="ltr" style="min-height: 305px;">
                                </div>
                                <canvas id="leadStatusLineChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-lg-4">
                        <div class="card card-height-100 mb-3">
                            <div class="card-header align-items-center border-0 d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Property Finder</h4>
                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#pf-product-tab"
                                                role="tab" aria-selected="false">Product</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" onclick="pfRankMeterChart()" data-bs-toggle="tab"
                                                href="#pf-rank-tab" role="tab" aria-selected="true">Quality</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#pf-verified-tab"
                                                role="tab" aria-selected="true">Verified</a>
                                        </li>
                                    </ul><!-- end ul -->
                                </div>
                            </div>

                            <div class="card-body" style="max-height: 400px;">
                                <div class="tab-content p-0">
                                    <div class="tab-pane active" id="pf-product-tab" role="tabpanel">


                                        <div id="pf-status"
                                            data-colors='["--vz-success", "--vz-primary","--vz-danger", "--vz-warning"]'
                                            class="apex-charts" dir="ltr"></div>
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-center align-items-center mb-4">
                                                <h2 class="me-3 ff-secondary mb-0 pf_count">0</h2>
                                                <div>
                                                    <p class="text-muted mb-0">Total Listings</p>
                                                    <p class="text-success fw-medium mb-0 d-none">
                                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                                class="ri-arrow-right-up-line"></i></span> +0 New
                                                    </p>
                                                </div>
                                            </div>
                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed  py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-danger align-middle me-2"></i>
                                                    Premium</p>
                                                <div>
                                                    <span class="text-muted pf_premium">0</span>

                                                </div>
                                            </div><!-- end -->
                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>
                                                    Featured</p>
                                                <div>
                                                    <span class="text-muted pf_featured">0</span>

                                                </div>
                                            </div><!-- end -->
                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                                    Standard</p>
                                                <div>
                                                    <span class="text-muted  pf_standard">0</span>

                                                </div>
                                            </div><!-- end -->




                                        </div>
                                    </div>
                                    <div class="tab-pane" id="pf-verified-tab" role="tabpanel">

                                        <div id="pfVerifiedPieChart" data-colors="[&quot;--vz-success&quot;]"
                                            class="apex-charts" dir="ltr">
                                        </div>

                                    </div>
                                    <div class="tab-pane" id="pf-rank-tab" role="tabpanel">
                                        <div class="d-flex mt-2">
                                            <h4 class=" flex-grow-1"></h4>
                                            <div class="flex-shrink-0">
                                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                                    role="tablist">
                                                    <li class="nav-item">

                                                        <a data-bs-toggle="tab" href="#pf-rank-meter-tab" role="tab"
                                                            aria-selected="false" onclick="pfRankMeterChart()"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            <i class=" bx bx-tachometer"></i></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#pf-rank-bar-tab" role="tab"
                                                            aria-selected="false" onclick="pfRankBarChart()"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            <i class="ri-bar-chart-2-fill"></i>
                                                        </a>

                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#pf-status-tab-child"
                                                            role="tab" aria-selected="false"
                                                            onclick="pfStatusPieChart()"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            Status
                                                        </a>

                                                    </li>
                                                </ul><!-- end ul -->
                                            </div>
                                        </div>
                                        <div class="tab-content p-0">
                                            <div class="tab-pane active" id="pf-rank-meter-tab" role="tabpanel">
                                                <div id="pf_rank_meter"
                                                    style="position: relative;
                                              height: 100vh;
                                              overflow: hidden;">

                                                </div>
                                            </div>
                                            <div class="tab-pane" id="pf-rank-bar-tab" role="tabpanel">
                                                <div id="pf_bar_chart"
                                                    style="position: relative;
                                              height: 100vh;
                                              overflow: hidden;">

                                                </div>
                                            </div>
                                            <div class="tab-pane" id="pf-status-tab-child" role="tabpanel">
                                                <div id="pf-status-pie"
                                                    data-colors='["--vz-success", "--vz-danger","--vz-primary", "--vz-warning"]'
                                                    class="apex-charts" dir="ltr"></div>
                                                <div class="mt-3">

                                                    <div
                                                        class="d-flex justify-content-between border-bottom border-bottom-dashed border-top border-top-dashed py-2">
                                                        <p class="fw-medium mb-0"><i
                                                                class="ri-whatsapp-fill text-success align-middle me-2"></i>
                                                            Whatsapp AVG Response Time</p>
                                                        <div>
                                                            <span class="text-muted pe-5 pf_whatssapp_avg_time">0</span>

                                                        </div>
                                                    </div><!-- end -->

                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div><!-- end tab pane -->
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xxl-4 col-lg-4">
                        <div class="card card-height-100 mb-3">
                            <div class="card-header align-items-center border-0 d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Bayut</h4>
                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#bayut-status-tab"
                                                role="tab" aria-selected="false">Product</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" onclick="bayutRankMeterChart()" data-bs-toggle="tab"
                                                href="#bayut-quality-tab" role="tab" aria-selected="true">Quality</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" onclick="bayutVerfiedChart()" data-bs-toggle="tab"
                                                href="#bayut-verified-tab" role="tab"
                                                aria-selected="true">Verified</a>
                                        </li>


                                    </ul><!-- end ul -->
                                </div>
                            </div>

                            <div class="card-body" style="max-height: 400px;">
                                <div class="tab-content p-0">
                                    <div class="tab-pane active" id="bayut-status-tab" role="tabpanel">
                                        <div id="bayut-pie-chart"
                                            data-colors="[&quot;--vz-success&quot;, &quot;--vz-primary&quot;,  &quot;--vz-danger&quot;, &quot;--vz-warning&quot;]"
                                            class="apex-charts" dir="ltr" style="min-height: 212.8px;">

                                        </div>
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-center align-items-center mb-4">
                                                <h2 class="me-3 ff-secondary mb-0 bayut_pie_count">0</h2>
                                                <div>
                                                    <p class="text-muted mb-0">Total Listings</p>
                                                    <p class="text-success fw-medium mb-0 d-none">
                                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                                class="ri-arrow-right-up-line"></i></span> +0 New
                                                    </p>
                                                </div>
                                            </div>

                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>
                                                    Signature</p>
                                                <div>
                                                    <span class="text-muted bayut_signature">0 Listings</span>
                                                    {{-- <span class="text-success fw-medium fs-12">243hrs</span> --}}
                                                </div>
                                            </div><!-- end -->
                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-danger align-middle me-2"></i>Hot
                                                </p>
                                                <div>
                                                    <span class="text-muted bayut_hot">0 Listings</span>
                                                    {{-- <span class="text-success fw-medium fs-12">~2050hrs</span> --}}
                                                </div>
                                            </div><!-- end -->
                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                                    Standard</p>
                                                <div>
                                                    <span class="text-muted bayut_standard">0 Listings</span>
                                                    {{-- <span class="text-success fw-medium fs-12">15870hrs</span> --}}
                                                </div>
                                            </div><!-- end -->



                                        </div>
                                    </div>
                                    <div class="tab-pane" id="bayut-quality-tab" role="tabpanel"
                                        style="    max-height: 400px;">
                                        <div class="d-flex mt-2">
                                            <h4 class=" flex-grow-1"></h4>
                                            <div class="flex-shrink-0">
                                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                                    role="tablist">
                                                    <li class="nav-item">

                                                        <a data-bs-toggle="tab" href="#bayut-rank-meter-tab"
                                                            role="tab" aria-selected="false"
                                                            onclick="bayutRankMeterChart()"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            <i class=" bx bx-tachometer"></i></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#bayut-rank-bar-tab" role="tab"
                                                            aria-selected="false" onclick="bayutQualityBarChart()"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            <i class="ri-bar-chart-2-fill"></i>
                                                        </a>

                                                    </li>
                                                </ul><!-- end ul -->
                                            </div>
                                        </div>
                                        <div class="tab-content p-0">
                                            <div class="tab-pane active" id="bayut-rank-meter-tab" role="tabpanel">
                                                <div id="bayut_rank_meter"
                                                    style="position: relative;
                                              height: 100vh;
                                              overflow: hidden;">

                                                </div>
                                            </div>
                                            <div class="tab-pane" id="bayut-rank-bar-tab" role="tabpanel">
                                                <div id="bayut_quality_bar_chart"
                                                    style="position: relative;
                                              height: 100vh;
                                              overflow: hidden;">

                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                    <div class="tab-pane" id="bayut-verified-tab" role="tabpanel">
                                        <div id="bayut-verified-chart"
                                            data-colors="[&quot;--vz-success&quot;, &quot;--vz-primary&quot;,&quot;--vz-warning&quot;]"
                                            class="apex-charts" dir="ltr" style="min-height: 212.8px;">
                                        </div>
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-center align-items-center mb-4">
                                                <h2 class="me-3 ff-secondary mb-0 bayut_pie_count">0</h2>
                                                <div>
                                                    <p class="text-muted mb-0">Total Listings</p>
                                                    <p class="text-success fw-medium mb-0">
                                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                                class="ri-check-double-line"></i></span> <span
                                                            class="bayut_verfied_count"></span> Verified
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xxl-4 col-lg-4">
                        <div class="card card-height-100 mb-3">
                            <div class="card-header align-items-center border-0 d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Dubizzle</h4>
                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#dubizzle-status-tab"
                                                role="tab" aria-selected="false">Product</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#dubizzle_quality_tab"
                                                role="tab" aria-selected="true"
                                                onclick="dubizzleRankMeterChart()">Quality</a>
                                        </li>

                                    </ul><!-- end ul -->
                                </div>
                            </div>

                            <div class="card-body" style="max-height: 400px;">
                                <div class="tab-content p-0">
                                    <div class="tab-pane active" id="dubizzle-status-tab" role="tabpanel">
                                        <div id="dubizzle-doghnout-chart"
                                            data-colors='["--vz-danger", "--vz-primary", "--vz-success","--vz-danger"]'
                                            class="apex-charts" dir="ltr">

                                        </div>
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-center align-items-center mb-4">
                                                <h2 class="me-3 ff-secondary mb-0 dubizzle_total_count">0</h2>
                                                <div>
                                                    <p class="text-muted mb-0">Total Listings</p>
                                                    <p class="text-success fw-medium mb-0 d-none">
                                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                                class="ri-arrow-right-up-line"></i></span> +0 New
                                                    </p>
                                                </div>
                                            </div>
                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-danger align-middle me-2"></i>
                                                    Premium</p>
                                                <div>
                                                    <span class="text-muted dubizzle_premium">0 Listings</span>
                                                    {{-- <span class="text-success fw-medium fs-12">15870hrs</span> --}}
                                                </div>
                                            </div><!-- end -->
                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>Featured
                                                </p>
                                                <div>
                                                    <span class="text-muted dubizzle_featured">0 Listings</span>
                                                    {{-- <span class="text-success fw-medium fs-12">~2050hrs</span> --}}
                                                </div>
                                            </div><!-- end -->
                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                                    Standard</p>
                                                <div>
                                                    <span class="text-muted dubizzle_standard">0 Listings</span>
                                                    {{-- <span class="text-success fw-medium fs-12">15870hrs</span> --}}
                                                </div>
                                            </div><!-- end -->



                                        </div>
                                    </div>

                                    <div class="tab-pane" id="dubizzle_quality_tab" role="tabpanel">

                                        <div class="d-flex mt-2">
                                            <h4 class=" flex-grow-1"></h4>
                                            <div class="flex-shrink-0">
                                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                                    role="tablist">
                                                    <li class="nav-item">

                                                        <a data-bs-toggle="tab" href="#dubizzle-rank-meter-tab"
                                                            role="tab" aria-selected="false"
                                                            onclick="dubizzleRankMeterChart()"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            <i class=" bx bx-tachometer"></i></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#dubizzle-rank-bar-tab"
                                                            role="tab" aria-selected="false"
                                                            onclick="dubizzleQualityBarChart()"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            <i class="ri-bar-chart-2-fill"></i>
                                                        </a>

                                                    </li>
                                                </ul><!-- end ul -->
                                            </div>
                                        </div>
                                        <div class="tab-content p-0">
                                            <div class="tab-pane active" id="dubizzle-rank-meter-tab" role="tabpanel">
                                                <div id="dubbizle_rank_meter"
                                                    style="position: relative;
                                              height: 100vh;
                                              overflow: hidden;">

                                                </div>
                                            </div>
                                            <div class="tab-pane" id="dubizzle-rank-bar-tab" role="tabpanel">

                                                <div id="dubizzle_quality_bar_chart"
                                                    style="position: relative;
                                              height: 100vh;
                                              overflow: hidden;">

                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div>

                </div>
                <div class="row">
                    <div class="col-xxl-12 col-lg-12">
                        <div class="card card-height-100 mb-3">
                            <div class="card-header align-items-center border-0 d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Agent Overview</h4>
                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#agents-overview-tab"
                                                role="tab" aria-selected="false">Listing Overview</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#agent-status-tab"
                                                role="tab"
                                                onclick="getAgentMetaData('agent_pf_premium_listings','Property Finder Premium','pf_level','Premium')"
                                                aria-selected="true">Portal Status</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" onclick="getAgentsLead('New','agent_new_leads')"
                                                data-bs-toggle="tab" href="#agent-leads-tab" role="tab"
                                                aria-selected="true">Leads</a>
                                        </li>

                                    </ul><!-- end ul -->
                                </div>
                            </div>

                            <div class="card-body" style="max-height: 400px;">
                                <div class="tab-content p-0" style="width: auto;">
                                    <div class="tab-pane active tasks-board" id="agents-overview-tab" role="tabpanel"
                                        style="width: auto;">
                                        <div class="text-center" id="loader_agent_listing_overview">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div class="" style="width:6500px; height:500px; overflow-x: scroll;">
                                            <div id="agent_listing_stacked_bar"
                                                data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                class="apex-charts" dir="ltr"></div>
                                        </div>

                                    </div>

                                    <div class="tab-pane" id="agent-status-tab" role="tabpanel">
                                        <div class="d-flex mt-2">
                                            <h4 class=" flex-grow-1"></h4>
                                            <div class="flex-shrink-0">
                                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                                    role="tablist">
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#agentPfPremiumTab" role="tab"
                                                            aria-selected="false"
                                                            onclick="getAgentMetaData('agent_pf_premium_listings','Property Finder Premium','pf_level','Premium')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            PF Premium</a>
                                                    </li>
                                                    <li class="nav-item">

                                                        <a data-bs-toggle="tab" href="#agentPfFeaturedTab" role="tab"
                                                            aria-selected="false"
                                                            onclick="getAgentMetaData('agent_pf_featured_listings','Property Finder Featured','pf_level','Featured')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            PF Featured</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#agentPfStandardTab" role="tab"
                                                            aria-selected="false"
                                                            onclick="getAgentMetaData('agent_pf_standard_listings','Property Finder Standard','pf_level','Standard')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            PF Standard</a>
                                                    </li>



                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#agentBayutSignatureTab"
                                                            role="tab" aria-selected="false"
                                                            onclick="getAgentMetaData('agent_bayut_signature_listings','Bayut Signature','bayut_level','Signature')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            Bayut Signature</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#agentBayutHotTab" role="tab"
                                                            aria-selected="false"
                                                            onclick="getAgentMetaData('agent_bayut_hot_listings','Bayut Hot','bayut_level','Hot')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            Bayut Hot</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#agentBayutStandardTab"
                                                            role="tab" aria-selected="false"
                                                            onclick="getAgentMetaData('agent_bayut_standard_listings','Bayut Standard','bayut_level','Standard')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            Bayut Standard</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#agentBayutTrucheckTab"
                                                            role="tab" aria-selected="false"
                                                            onclick="getAgentMetaData('agent_bayut_truchecked_listings','Bayut Trucheck','bayut_trucheck_status','checked')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            Bayut TruCheck
                                                        </a>

                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#agentDubbizleFeaturedTab"
                                                            role="tab" aria-selected="false"
                                                            onclick="getAgentMetaData('agent_dubizzle_featured_listings','Dubizzle Featured','dubizzle_level','Featured')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            Dubbizle Featured
                                                        </a>

                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#agentDubbizleStandardTab"
                                                            role="tab" aria-selected="false"
                                                            onclick="getAgentMetaData('agent_dubizzle_standard_listings','Dubizzle Standard','dubizzle_level','Standard')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            Dubbizle Standard
                                                        </a>

                                                    </li>


                                                </ul><!-- end ul -->
                                            </div>
                                        </div>
                                        <div class="tab-content p-0">
                                            <div class="tab-pane active  tasks-board" id="agentPfPremiumTab"
                                                role="tabpanel">
                                                <div class="text-center" id="loader_agent_pf_premium_listings">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_pf_premium_listings"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                            <div class="tab-pane  tasks-board" id="agentPfStandardTab" role="tabpanel">
                                                <div class="text-center" id="loader_agent_pf_standard_listings">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_pf_standard_listings"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                            <div class="tab-pane tasks-board" id="agentPfFeaturedTab" role="tabpanel">
                                                <div class="text-center" id="loader_agent_pf_featured_listings">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_pf_featured_listings"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                            <div class="tab-pane tasks-board" id="agentBayutStandardTab" role="tabpanel">
                                                <div class="text-center" id="loader_agent_bayut_standard_listings">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_bayut_standard_listings"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                            <div class="tab-pane tasks-board" id="agentBayutSignatureTab"
                                                role="tabpanel">
                                                <div class="text-center" id="loader_agent_bayut_signature_listings">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_bayut_signature_listings"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                            <div class="tab-pane tasks-board" id="agentBayutHotTab" role="tabpanel">
                                                <div class="text-center" id="loader_agent_bayut_hot_listings">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_bayut_hot_listings"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                            <div class="tab-pane tasks-board" id="agentBayutTrucheckTab" role="tabpanel">
                                                <div class="text-center" id="loader_agent_bayut_truchecked_listings">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_bayut_truchecked_listings"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                            <div class="tab-pane tasks-board" id="agentDubbizleStandardTab"
                                                role="tabpanel">
                                                <div class="text-center" id="loader_agent_dubizzle_standard_listings">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_dubizzle_standard_listings"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                            <div class="tab-pane tasks-board" id="agentDubbizleFeaturedTab"
                                                role="tabpanel">
                                                <div class="text-center" id="loader_agent_dubizzle_featured_listings">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_dubizzle_featured_listings"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="tab-pane" id="agent-leads-tab" role="tabpanel">
                                        <div class="d-flex mt-2">
                                            <h4 class=" flex-grow-1"></h4>
                                            <div class="flex-shrink-0">
                                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                                    role="tablist">
                                                    <li class="nav-item">

                                                        <a data-bs-toggle="tab" href="#agentNewLeadTab" role="tab"
                                                            aria-selected="false"
                                                            onclick="getAgentsLead('New','agent_new_leads')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            New</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#agentSuccessfulLeadTab"
                                                            role="tab" aria-selected="false"
                                                            onclick="getAgentsLead('Successful','agent_successful_leads')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            Successful
                                                        </a>

                                                    </li>
                                                    <li class="nav-item">
                                                        <a data-bs-toggle="tab" href="#agentUnSuccessfulLeadTab"
                                                            role="tab" aria-selected="false"
                                                            onclick="getAgentsLead('Unsuccessful','agent_unsuccessful_leads')"
                                                            class="btn btn-soft-secondary btn-sm">
                                                            Unsuccessful
                                                        </a>

                                                    </li>
                                                </ul><!-- end ul -->
                                            </div>
                                        </div>
                                        <div class="tab-content p-0">
                                            <div class="tab-pane active tasks-board" id="agentNewLeadTab"
                                                role="tabpanel">
                                                <div class="text-center" id="loader_agent_new_leads">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_new_leads"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                            <div class="tab-pane tasks-board" id="agentSuccessfulLeadTab"
                                                role="tabpanel">
                                                <div class="text-center" id="loader_agent_successful_leads">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_successful_leads"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                            <div class="tab-pane tasks-board" id="agentUnSuccessfulLeadTab"
                                                role="tabpanel">
                                                <div class="text-center" id="loader_agent_unsuccessful_leads">
                                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class=" "
                                                    style="width:5000px; height:500px; overflow-x: scroll;">
                                                    <div id="agent_unsuccessful_leads"
                                                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                        class="apex-charts" dir="ltr"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>


                            </div>

                        </div><!-- end tab pane -->
                    </div><!-- end cardbody -->
                </div>

                <div class="row d-none">
                    <div class="col-xl-4">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Rent listing on portals</h4>
                                <div class="flex-shrink-0">

                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="text-center uk-vertical-align-middle mt-4" id="loader_rent_listing_source">
                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div id="rent_listing_source"
                                    data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                    class="apex-charts" dir="ltr"></div>
                            </div>
                        </div> <!-- .card-->
                    </div>
                    <div class="col-xl-4">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Sale listing on portals</h4>
                                <div class="flex-shrink-0">

                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="text-center uk-vertical-align-middle mt-4" id="loader_sale_listing_source">
                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div id="sale_listing_source"
                                    data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                    class="apex-charts" dir="ltr"></div>
                            </div>
                        </div> <!-- .card-->
                    </div>

                    <div class="col-xl-4">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Listed on Portals</h4>
                            </div>
                            <div class="card-body">
                                <div class="mt-3 pt-2">
                                    <div class="progress progress-lg rounded-pill">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 25%"
                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 18%"
                                            aria-valuenow="18" aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 22%"
                                            aria-valuenow="22" aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 16%"
                                            aria-valuenow="16" aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 19%"
                                            aria-valuenow="19" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <ul class="px-0" style="list-style-type: none;">
                                    @foreach ($portalWiseCounts ?? [] as $portal)
                                        <li class="py-2">
                                            <div class="d-flex justify-content-start align-items-center gap-2">
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    @if (!empty($portal['thumbnail']))
                                                        <img src="{{ $portal['thumbnail'] }}" alt=""
                                                            width="20px">
                                                    @else
                                                        <div style="width: 20px">&nbsp;</div>
                                                    @endif
                                                    <strong>{{ $portal['name'] ?? '' }}:</strong>
                                                </div>
                                                <div>
                                                    <span
                                                        class="badge badge-soft-dark">{{ $portal['properties_count'] ?? 0 }}</span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row my-5 d-none" id="group-reporting-loader">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="group-reporting">
                </div>

                <div class="row d-none">
                    <div class="col-xl-4">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Store Visits by Source</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">Report<i
                                                    class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">Download Report</a>
                                            <a class="dropdown-item" href="#">Export</a>
                                            <a class="dropdown-item" href="#">Import</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div id="store-visits-source"
                                    data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                    class="apex-charts" dir="ltr"></div>
                            </div>
                        </div> <!-- .card-->
                    </div> <!-- .col-->
                    <div class="col-xl-4">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Sale listing on websites</h4>
                                <div class="flex-shrink-0">

                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">

                            </div>
                        </div> <!-- .card-->
                    </div> <!-- .col-->
                    <div class="col-xl-4 col-md-6">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Lead Source</h4>
                                <div class="flex-shrink-0">

                                </div>
                            </div>

                            <div class="card-body">

                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h6 class="text-muted text-uppercase fw-semibold text-truncate fs-12 mb-3">
                                            Total Leads</h6>
                                        <h4 class="fs- mb-0">{{ $leadCount ?? 0 }}</h4>
                                        <p class="mb-0 mt-2 text-muted d-none"><span
                                                class="badge badge-soft-success mb-0">
                                                <i class="ri-arrow-up-line align-middle"></i> 15.72 %
                                            </span> vs. previous month</p>
                                    </div><!-- end col -->
                                    <div class="col-6">
                                        <div class="text-center">
                                            <img src="{{ asset('assets/images/illustrator-1.png') }}" class="img-fluid"
                                                alt="">
                                        </div>
                                    </div><!-- end col -->
                                </div><!-- end row -->
                                <div class="mt-3 pt-2">
                                    <div class="progress progress-lg rounded-pill">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 25%"
                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 18%"
                                            aria-valuenow="18" aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 22%"
                                            aria-valuenow="22" aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 16%"
                                            aria-valuenow="16" aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 19%"
                                            aria-valuenow="19" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div><!-- end -->

                                <div class="mt-3 pt-2">
                                    <div class="d-flex mb-2">
                                        <div class="flex-grow-1">
                                            <p class="text-truncate text-muted fs-14 mb-0">
                                                <i class="mdi mdi-circle align-middle text-primary me-2"></i>Calls
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <p class="mb-0">24.58%</p>
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex mb-2">
                                        <div class="flex-grow-1">
                                            <p class="text-truncate text-muted fs-14 mb-0">
                                                <i class="mdi mdi-circle align-middle text-info me-2"></i>Email
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <p class="mb-0">17.51%</p>
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex mb-2">
                                        <div class="flex-grow-1">
                                            <p class="text-truncate text-muted fs-14 mb-0">
                                                <i class="mdi mdi-circle align-middle text-success me-2"></i>Agents
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <p class="mb-0">23.05%</p>
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex mb-2">
                                        <div class="flex-grow-1">
                                            <p class="text-truncate text-muted fs-14 mb-0">
                                                <i class="mdi mdi-circle align-middle text-warning me-2"></i>Property
                                                Finder
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <p class="mb-0">12.22%</p>
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-truncate text-muted fs-14 mb-0">
                                                <i class="mdi mdi-circle align-middle text-danger me-2"></i>Bayut
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <p class="mb-0">17.58%</p>
                                        </div>
                                    </div><!-- end -->
                                </div><!-- end -->

                                <div class="mt-2 text-center">
                                    <a href="javascript:void(0);" class="text-muted text-decoration-underline">Show
                                        All</a>
                                </div>

                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>


                    <!-- end col -->
                </div>


            </div> <!-- end .h-100-->

        </div> <!-- end col -->


    </div>

    <!-- Default Modals -->
    <div id="annoucementModel" class="modal fade" tabindex="-1" aria-labelledby="annoucementModel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="annoucementModel">📣 Announcement</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button> --}}
                </div>

                <div class="modal-body">
                    <p>Dear Colleagues, <br>Sign up now for this Training on Wednesday.</p>


                    <p><b>💻''Mastering Data Analysis''</b></p>
                    <p> Discover how to utilize insights from Open Data Platforms to expand your Real Estate knowledge and
                        improve the customer service experience.

                    </p>
                    <p><b> Wednesday, 11:00 - 13:00 <br>Concord, Boardroom <br>With Brian Witbooi</b></p>


                    <hr>
                    <h6>
                        HOW TO SIGN UP?

                    </h6>
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="text-muted mb-0">Go to the <a href="https://axcapital.talentlms.com/dashboard" target="_blank">AX ACADEMY</a>  in the CRM</p>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="text-muted mb-0">Click on the "Desired Session" in the "Live Sessions" category

                            </p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="text-muted mb-0">Register for the training

                            </p>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class=" col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="acknowledged">
                                <label class="form-check-label" for="acknowledged">
                                    Do not show this message again.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>







                <div class="modal-footer border-0 justify-content-end">
                    {{-- <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button> --}}
                    <button type="button" class="btn btn-primary" id="acknowledge-btn">Yes, I Acknowledge</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    @include('admin.events.include.modal')
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/echarts/echarts.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="{{ URL::asset('/assets/libs/chart.js/chart.js.min.js') }}"></script>
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/swiper/swiper.min.js') }}"></script>
    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/event.js') }}?v={{ time() }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/dashboard.js') }}?v=5"></script>



    <script>
        let isEventPage = false;

        function viewEvent(id) {
            showEventViewModal(id);
        }
        $('#dashboard-selector').select2({
            placeholder: 'Select Dashboard',
        });

        function getDashboardPieData() {
            $.ajax({
                url: "{{ route('admin.dashboard.pie.data') }}",
                type: "GET",
                success: function(result) {
                    if (result.status == '1') {
                        let labels = [];
                        let values = [];
                        jQuery.each(result.data.datasets.rent_portal_pie_chart?.label, (index, item) => {
                            labels.push(item);
                        });
                        jQuery.each(result.data.datasets.rent_portal_pie_chart?.data, (index, item) => {
                            values.push(item);
                        });

                        rentListSourcePieChart(labels, values);
                        labels = [];
                        values = [];
                        jQuery.each(result.data.datasets.sale_portal_pie_chart?.label, (index, item) => {
                            labels.push(item);
                        });
                        jQuery.each(result.data.datasets.sale_portal_pie_chart?.data, (index, item) => {
                            values.push(item);
                        });
                        saleListSourcePieChart(labels, values);

                    }
                }
            });
        }

        getDashboardPieData();
        // {{-- dashboardDecider(); --}}

        // {{-- function dashboardDecider() { --}}
        // {{--    $.ajax({ --}}
        // {{--        url: "{{ route('admin.dashboard.dashboard.decider') }}", --}}
        // {{--        type: "GET", --}}
        // {{--        success: function (result) { --}}
        // {{--            if (result.status == '1') { --}}
        // {{--                let values = []; --}}
        // {{--                let labels = []; --}}
        // {{--                jQuery.each(result.data.meta.dubizzle.datasets.dubizzle_pie_chart?.label, (index, item) => { --}}
        // {{--                    labels.push(item); --}}
        // {{--                }); --}}
        // {{--                jQuery.each(result.data.meta.dubizzle.datasets.dubizzle_pie_chart?.data, (index, item) => { --}}
        // {{--                    values.push(item); --}}
        // {{--                }); --}}
        // {{--                //dubizzelePieChart(labels, values); --}}


        // {{--            } --}}
        // {{--        } --}}
        // {{--    }); --}}
        // {{-- } --}}


        function rentListSourcePieChart(label, values) {


            var options = {
                series: values,
                chart: {
                    height: 300,
                    type: 'donut',
                },
                labels: label,
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    dropShadow: {
                        enabled: false,
                    }
                },

            };
            var chart = new ApexCharts(document.querySelector("#rent_listing_source"), options);
            chart.render();
            $("#loader_rent_listing_source").addClass("d-none");
        }


        function saleListSourcePieChart(label, values) {
            var options = {
                series: values,
                chart: {
                    height: 300,
                    type: 'donut',
                },
                labels: label,
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    dropShadow: {
                        enabled: false,
                    }
                },

            };
            var chart = new ApexCharts(document.querySelector("#sale_listing_source"), options);
            chart.render();
            $("#loader_sale_listing_source").addClass("d-none");
        }


        $('#DashActive').addClass('active');

        reportLaunch()

        function reportLaunch() {
            @can('See Agents Reports')
                $('#group-reporting-loader').removeClass('d-none')
                let dashboard = $('#dashboard-selector option:selected').val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('agentGroupReporting') }}",
                    dataType: 'html',
                    data: {
                        group: dashboard,
                    },
                    success: function(response) {
                        $('#group-reporting-loader').addClass('d-none')

                        $('#group-reporting').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            @endcan
        }

        $(document).on("click", ".expand-report", function() {
            var parentCol = $(this).closest(".col-xl-4");

            var dFlexItems = parentCol.find(".list-content");

            var expandedContent = $('<div class="row"></div>');
            dFlexItems.each(function() {
                var colMd3 = $('<div class="col-md-3 p-2"></div>');
                colMd3.append($(this).clone());
                expandedContent.append(colMd3);
            });
            $('#report-title').html($(this).data('title'));
            // add .list-content-style to the expanded content where .list-content is present
            expandedContent.find(".list-content").addClass("list-content-style");
            $("#put-data-here").html(expandedContent);
            $("#fullscreeexampleModal").modal("show");
            animateIcons();
        });

        function animateIcons() {
            var icons = $('.animate-to-right i');
            var totalIcons = icons.length;
            var fadeInDuration = 200;
            var fadeOutDuration = 200;
            var delayBetweenIcons = 700;

            function showIconsSequentially(index) {
                if (index < totalIcons) {
                    icons.eq(index).fadeIn(fadeInDuration).delay(delayBetweenIcons).fadeOut(fadeOutDuration, function() {
                        showIconsSequentially(index + 1); // Recursive call for the next icon
                    });
                } else {
                    // All icons displayed, hide all and repeat
                    setTimeout(function() {
                        icons.fadeOut(fadeOutDuration, function() {
                            showIconsSequentially(0); // Start from the first icon
                        });
                    }, delayBetweenIcons);
                }
            }

            showIconsSequentially(0);
        }

        $(document).ready(function() {
            // localStorage.setItem('announcementAcknowledged', 'false');

            if (localStorage.getItem('announcementAcknowledged') === 'true') {
                $('#annoucementModel').modal('hide');
            } else {
                $('#annoucementModel').modal('show');
            }


            $('#acknowledge-btn').on('click', function() {

                $('#annoucementModel').modal('hide');

                if ($('#acknowledged').is(':checked')) {
                    localStorage.setItem('announcementAcknowledged', 'true');
                } else {
                    localStorage.setItem('announcementAcknowledged', 'false');
                }

            });
        });

        // Call the animateIcons function
    </script>
@endsection
