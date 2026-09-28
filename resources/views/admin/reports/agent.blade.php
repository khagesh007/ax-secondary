@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection
@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />



    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style>
        #pf_rank_meter,
        #bayut_rank_meter,
        #dubbizle_rank_meter {
            max-width: 600px;
            max-height: 400px;
            /* Add other styles as needed */
        }

        .language .progress {
            height: 8px !important;
            padding: 1px !important;
        }

        td.highlight {
            background-color: whitesmoke !important;
        }

        html.dark td.highlight {
            background-color: rgba(var(--dt-row-hover), 0.082) !important;
        }

        table tbody tr.blue>.dtfc-fixed-left,
        table tbody tr.blue>.dtfc-fixed-right {
            background-color: blue !important;
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

        .selection .select2-selection--single {
            width: 300px !important;
        }
    </style>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
@endsection
@section('content')
    <div class="row">

        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">{{ timeGreetings() }}, {{ auth()->user()->name }}!</h4>
                                <p class="text-muted mb-0">Here's what's happening with your properties
                                    today.</p>
                            </div>
                            <div class="mt-3 mt-lg-0 ">
                                <form method="GET">
                                    <div class="row g-4 mb-0 align-items-center">
                                        <div class="col-sm-auto">
                                            <select name="agent" class="select2" id="report_agent">
                                                @if (request()->agent == null || request()->agent == '')
                                                    <option value="" selected>Select Agent</option>
                                                @endif

                                                @foreach ($agents ?? [] as $agent)
                                                    <option data-image="{{ $agent->thumbnail }}"
                                                        data-image="{{ $agent->thumbnail }}"
                                                        data-status="{{ $agent->status }}"
                                                        @if (request()->agent && request()->agent == $agent->id) selected @endif
                                                        value="{{ $agent->id }}">{{ $agent->first_name }}
                                                        {{ $agent->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <div class="col-sm-auto">
                                            <div class="input-group">
                                                <input type="text" id="report_time_filter" name="dates"
                                                    class="form-control border-0 dash-filter-picker shadow"
                                                    data-provider="flatpickr" data-range-date="true"
                                                    data-date-format="d M, Y"
                                                    @if (request()->dates) data-default-date="{{ request()->dates }}"
                                                       value="{{ request()->dates }}"
                                                         @else
                                                   <?php //   data-deafult-date="01 Jan 2022 to 31 Jan 2022"
                                                   ?> @endif>
                                                <div class="input-group-text bg-primary border-primary text-white">
                                                    <i class="ri-calendar-2-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-auto">
                                            <button type="submit" class="btn  action-add">Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card overflow-hidden">
                            <div class="card-body bg-marketplace d-flex">


                                <div class="flex-grow-0"> <img src="{{ $user->image_url }}" alt=""
                                        style="height: 125px;" class="img-fluid">
                                </div>

                                <div class="flex-grow-1" style="margin-left: 30px;">
                                    <div class="d-flex">


                                        <div class="flex-grow-1">
                                            <h4 class="fs-18 lh-base mb-0 agent-name">{{ $user->name }}</h4>
                                        </div>

                                        <div class="flex-grow-0" style="margin-left: 10px;">
                                            <div class="d-flex">
                                                <div class="flex-grow-0">
                                                    <div>


                                                        <p class="mb-0 pt-1 text-muted">{{ $user->position }}</p>
                                                        @if ($user->status)
                                                            <i class="bx bxs-badge-check text-success"
                                                                title="Active Account"></i>
                                                        @else
                                                            <i class="ri ri-close-circle-line text-danger"
                                                                title="Deactive Account"></i>
                                                        @endif

                                                        @if ($user->telegram_chatid != null)
                                                            <i class="bx bxl-telegram text-success"
                                                                title="Telegram Notification Enabled"></i>
                                                        @else
                                                            <i class="bx bxl-telegram text-danger"
                                                                title="Telegram Notification Disabled"></i>
                                                        @endif
                                                        @if ($user->call_tracking)
                                                            <i class="bx bx-phone-call text-success"
                                                                title="Call Tracking Enabled"></i>
                                                        @else
                                                            <i class="bx bx-phone-call text-danger"
                                                                title="Call Tracking Disabled"></i>
                                                        @endif
                                                        <i class="  bx bxl-whatsapp text-success"
                                                            title="WhatsApp Response Time:{{ getSecondsTohumanReadable($wa_average_diff, false) }}"></i>

                                                        {{-- <i class="  bx bx-time-five text-success"
                                                        title="Response Time:{{ getSecondsTohumanReadable($wa_average_diff, false) }}"></i> --}}
                                                    </div>

                                                </div>

                                            </div>




                                        </div>

                                    </div>

                                    <div class="row mt-2">
                                        @foreach ($languages as $lang)
                                            {{-- <div class="col-md-6">
                                            <span>{{ $lang->meta_key }}<br>
                                               <div style=""  data-bs-toggle="tooltip" data-bs-placement="top"
                                               title="Proficiency in {{$lang->meta_key}} is {{$lang->meta_value}}%.">
                                                <input type="range"  disabled value="{{$lang->meta_value}}" style=""  class="ranger">
                                               </div>
                                                </span>
                                        </div> --}}
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center pb-2">
                                                    <div class="flex-grow-1 language">
                                                        {{ $lang->meta_key }}
                                                        <div
                                                            class="progress animated-progress custom-progress progress-label">
                                                            <div class="progress-bar bg-primary" role="progressbar"
                                                                style="width: {{ $lang->meta_value }}%"
                                                                aria-valuenow="{{ $lang->meta_value }}" aria-valuemin="0"
                                                                aria-valuemax="100">
                                                                <div class="label">{{ $lang->meta_value }}%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- <div class="col-md-6">
                                            <span>{{ $lang->meta_key }}<br>
                                        <div class="progress animated-progress custom-progress">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{$lang->meta_value}}%" aria-valuenow="{{$lang->meta_value}}" aria-valuemin="0" aria-valuemax="{{$lang->meta_value}}"></div>
                                        </div>
                                        </div> --}}
                                        @endforeach

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div class="text-muted">
                                                Published Listings


                                            </div>
                                            <div>
                                                <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                        data-target="{{ $propertyCount }}">0</span></h4>
                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div class="text-muted">
                                                Leads

                                            </div>
                                            <div>
                                                <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                        data-target="{{ $leadCount }}">0</span></h4>

                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div class="text-muted">
                                                Contacts

                                            </div>
                                            <div>
                                                <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                        data-target="{{ $contactCount }}">0</span></h4>

                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div class="text-muted">
                                                Deals

                                            </div>
                                            <div>
                                                <h4 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                        data-target="{{ $dealCount }}">0</span></h4>

                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div> <!-- end card-->
                            </div> <!-- end col-->



                        </div>
                    </div>
                </div>
                {{-- @if ($languages->count() > 0)
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                @foreach ($languages as $lang)
                                    <div class="col-2">
                                        <span>{{ $lang->meta_key }}</span> {!! languagelevel($lang->meta_value) !!}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif --}}




                <div class="row">
                    <div class="col-xl-4">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Listing Overview</h4>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="text-center uk-vertical-align-middle mt-4"
                                    id="loader_listingOverviewBarChart">
                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div id="listingOverviewBarChart"
                                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
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
                                            <a class="nav-link" data-bs-toggle="tab" href="#recent-leads" role="tab"
                                                aria-selected="true">Leads <small>(Portals)</small></a>
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

                            <div class="card-header align-items-center border-0 d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Recent Activities</h4>
                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#evnt-tab"
                                                role="tab" aria-selected="false">Events</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" id="loadAgentReport"
                                                href="#activity-log-tab" role="tab" aria-selected="true">Activity
                                                Log</a>
                                        </li>
                                    </ul><!-- end ul -->
                                </div>
                            </div><!-- end card header -->


                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <div class="tab-pane active" id="evnt-tab" role="tabpanel">
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
                                                            <div class="simplebar-content-wrapper" tabindex="0"
                                                                role="region" aria-label="scrollable content"
                                                                style="height: 100%; overflow: hidden scroll;">
                                                                <div class="simplebar-content"
                                                                    style="padding: 0px 8px 0px 0px;">
                                                                    <div id="upcoming-event-list1">


                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="simplebar-placeholder"
                                                        style="width: auto; height: 2511px;"></div>
                                                </div>
                                                <div class="simplebar-track simplebar-horizontal"
                                                    style="visibility: hidden;">
                                                    <div class="simplebar-scrollbar" style="width: 0px; display: none;">
                                                    </div>
                                                </div>
                                                <div class="simplebar-track simplebar-vertical"
                                                    style="visibility: visible;">
                                                    <div class="simplebar-scrollbar"
                                                        style="height: 63px; display: block; transform: translate3d(0px, 24px, 0px);">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="tab-pane" id="activity-log-tab" role="tabpanel">
                                        <div id="activity-log-div_Placeholder">
                                            @include('admin.listing.includes.partials._activitylog_placeholder')
                                        </div>

                                        <div class="profile-timeline acitivity-timeline scrollable-notes"
                                            id="scrollableActiviyLog2">
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
                                            <a class="nav-link active" data-bs-toggle="tab" href="#buy-tab"
                                                role="tab" aria-selected="false">Status</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" onclick="leadSourcePieChart()" data-bs-toggle="tab"
                                                href="#sell-tab" role="tab" aria-selected="true">Source</a>
                                        </li>
                                    </ul><!-- end ul -->
                                </div>
                            </div><!-- end card header -->
                            <div class="card-body p-0">
                                <div class="tab-content p-0">
                                    <div class="tab-pane active" id="buy-tab" role="tabpanel">
                                        <div class="text-center uk-vertical-align-middle mt-5"
                                            id="loader_lead-status-pie">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div id="lead-status-pie"
                                            data-colors='["--vz-primary","#a1a1a1",  "--vz-warning", "--vz-info","#a6d8a6eb", "#35c035","--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                            class="e-charts"></div>
                                    </div><!-- end tabpane -->

                                    <div class="tab-pane" id="sell-tab" role="tabpanel"
                                        style="overflow-y: scroll; height: 370px; ">
                                        <div class="text-center uk-vertical-align-middle mt-5"
                                            id="loader_lead-source-pie">
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
                                <h4 class="card-title mb-0 flex-grow-1">Lead Status (Last 6 Months)</h4>
                            </div>
                            <div class="card-body" style=" max-height: 370px;">
                                <div class="text-center uk-vertical-align-middle mt-5" id="loader_leadStatusLineChart">
                                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
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
                                            <a class="nav-link" onclick="pfStatusPieChart()" data-bs-toggle="tab"
                                                href="#pf-verfied-tab" role="tab" aria-selected="true">Verified</a>
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
                                                    <span class="text-muted pf_premium">0 Listings</span>

                                                </div>
                                            </div><!-- end -->

                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>
                                                    Featured</p>
                                                <div>
                                                    <span class="text-muted pf_featured">0 Listings</span>

                                                </div>
                                            </div><!-- end -->

                                            <div
                                                class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                                <p class="fw-medium mb-0"><i
                                                        class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                                    Standard</p>
                                                <div>
                                                    <span class="text-muted pf_standard">0 Listings</span>

                                                </div>
                                            </div><!-- end -->

                                        </div>
                                    </div>

                                    <div class="tab-pane" id="pf-verfied-tab" role="tabpanel">


                                        <div id="pf-verified-pie"
                                            data-colors='["--vz-success", "--vz-danger","--vz-primary", "--vz-warning"]'
                                            class="apex-charts" dir="ltr"></div>


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
                                                        <a data-bs-toggle="tab" href="#pf-status-tab" role="tab"
                                                            aria-selected="false" onclick="pfRankBarChart()"
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
                                            <div class="tab-pane" id="pf-status-tab" role="tabpanel">


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
                                            data-colors='["--vz-success", "--vz-primary", "--vz-warning","--vz-danger"]'
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



                <div class="col-xxl-12 col-lg-12 d-none">
                    <div class="card card-height-100 mb-3">
                        <div class="card-header align-items-center border-0 d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Agent Charts</h4>
                            <div class="flex-shrink-0">
                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#agents-overview-tab"
                                            role="tab" aria-selected="false">Overview</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" onclick="pfRankMeterChart()" data-bs-toggle="tab"
                                            href="#pf-rank-tab" role="tab" aria-selected="true">Quality</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" onclick="pfStatusPieChart()" data-bs-toggle="tab"
                                            href="#pf-status-tab" role="tab" aria-selected="true">Status</a>
                                    </li>
                                </ul><!-- end ul -->
                            </div>
                        </div>

                        <div class="card-body" style="max-height: 400px;">
                            <div class="tab-content p-0" style="width: auto;">
                                <div class="tab-pane active" id="agents-overview-tab" role="tabpanel"
                                    style="width: auto;">
                                    <div class="" style="width:4000px; height:500px; overflow-x: scroll;">
                                        <div id="agent_listing_stacked_bar"
                                            data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                            class="apex-charts" dir="ltr"></div>
                                    </div>

                                </div>
                                <div class="tab-pane" id="agent-status-tab" role="tabpanel">


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
                                <div class="tab-pane" id="agent-rank-tab" role="tabpanel">
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

                                    </div>


                                </div>
                            </div><!-- end tab pane -->
                        </div><!-- end cardbody -->
                    </div><!-- end card -->
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


                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-11">
                                        <h4>Properties by Area</h4>

                                    </div>
                                    <div class="col-md-1">
                                        <div class=" text-start" id="exportBTNs"></div>
                                    </div>

                                    {{--  <div class="col-md-3">
                                      <select name="portal_id" id="portal_id" class="form-control select2 inputs">
                                            <option value="">Select Portal</option>
                                            @foreach ($portals as $portal)
                                                <option value="{{ $portal->id }}">{{ $portal->name }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}


                                </div>


                            </div>
                            <div class="card-body">
                                <div class="row" id="divLoader">
                                    <div class="col-md-12 text-center">
                                        <div class="spinner-grow text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </div>

                                <div id="tableAppend"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row d-none" id="location-loading-div">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row" id="">
                                    <div class="col-md-12 text-center">
                                        <div class="spinner-grow text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-2 text-center">
                                        <h4 class="text-muted"> Loading insights of <span
                                                id="location_community_name"></span></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row" id="tableAppendTwo">

                </div>


            </div> <!-- end .h-100-->

        </div> <!-- end col -->


    </div>


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
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/event.js') }}?v={{ time() }}"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>


    <script>
        let isEventPage = false;
        let currentAgentObj = {};
        let dates = "{{ request()->get('dates') ?? '' }}";
        @if ($user)
            currentAgentObj = {
                id: {{ $user->id }},
            };
        @endif
    </script>
    <script src="{{ URL::asset('/assets/js/pages/report-agent.js') }}?v=3"></script>
    <script>
        @if ($user)
            propertyFinderData(currentAgentObj.id, formatDateRange(dates));
            bayutData(currentAgentObj.id, formatDateRange(dates));
            dubizzleData(currentAgentObj.id, formatDateRange(dates));
            getDashboardPieData(currentAgentObj.id, formatDateRange(dates));
            getDashboardLeads(currentAgentObj.id, formatDateRange(dates));
            recentLeads(currentAgentObj.id);
        @else
            propertyFinderData();
            getDashboardPieData();
        @endif
        function viewEvent(id) {
            showEventViewModal(id);
        }

        function formatDateRange(dateRange) {
            if (dateRange == '' || dateRange == null || dateRange == undefined) {
                return '';
            } else {
                const [startDateStr, endDateStr] = dateRange.split(' to ');

                const formattedStartDate = moment(startDateStr, 'DD MMM, YYYY').format('YYYY-MM-DD');
                const formattedEndDate = moment(endDateStr, 'DD MMM, YYYY').format('YYYY-MM-DD');

                return {
                    startDate: formattedStartDate,
                    endDate: formattedEndDate
                };
            }
        }
        $(document).ready(function() {
            // $("#report_agent").getAgentSelect2();
        });

        function getDashboardPieData(user_id = null, dates = null) {
            $.ajax({
                url: "{{ route('admin.reports.agent.report.portal') }}",
                type: "GET",
                dataType: "JSON",
                data: {
                    'dates': dates,
                    'user_id': user_id
                },
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
        $('#scrollableActiviyLog2').html('');
        $('#loadAgentReport').click(function() {
            $('#activity-log-div_Placeholder').removeClass('d-none')
            getActivityLog(currentAgentObj.id, formatDateRange(dates));
        })

        dashboardDecider();

        function dashboardDecider() {
            $.ajax({
                url: "{{ route('admin.dashboard.dashboard.decider') }}",
                type: "GET",
                success: function(result) {
                    if (result.status == '1') {
                        let values = [];
                        let labels = [];
                        jQuery.each(result.data.meta.dubizzle.datasets.dubizzle_pie_chart?.label, (index,
                            item) => {
                            labels.push(item);
                        });
                        jQuery.each(result.data.meta.dubizzle.datasets.dubizzle_pie_chart?.data, (index,
                            item) => {
                            values.push(item);
                        });
                        //dubizzelePieChart(labels, values);


                    }
                }
            });
        }

        areaWiseListing()




        $(document).on("click", ".getlocationdetails", function() {
            let location_name = $(this).data('location-name');
            let agent_id = currentAgentObj.id;
            $('#tableAppendTwo').html('');
            $('#location_community_name').text(location_name);
            $('#location-loading-div').removeClass('d-none');
            $('html, body').animate({
                scrollTop: $(document).height()
            }, 1000);


            $.ajax({
                url: "{{ route('admin.reports.agent.listing.report.sub.community') }}",
                type: 'POST',
                dataType: 'html',
                data: {
                    location_name: location_name,
                    agent_id: agent_id,
                },
                success: function(result) {
                    $('#location-loading-div').addClass('d-none');

                    $('#tableAppendTwo').html(result);


                    $('.showCountSubCommunity').each(function() {
                        let sum = 0;
                        $('#buttons-datatables-community').find('td:nth-child(' + ($(this)
                                .index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(sum);
                    });

                    // at .showPercentage show percentage of each column as total is in last column
                    $('.showPercentageSubCommunity').each(function() {
                        let sum = 0;
                        $('#buttons-datatables-community').find('td:nth-child(' + ($(this)
                                .index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(((sum / $('.showCountSubCommunity').last().text()) * 100)
                            .toFixed(2) + '%');
                    });

                    $('.showPercentageSubCommunity').each(function() {
                        var columnIndex = $(this).index();
                        if ($(this).text() === '0.00%') {
                            $('#buttons-datatables-community tr').find('td:eq(' + columnIndex +
                                '), th:eq(' +
                                columnIndex + ')').hide();
                        }
                    });



                    const table = new DataTable('#buttons-datatables-community', {
                        dom: 'Bfrtip',
                        buttons: [
                            'csv',
                        ],
                        fixedColumns: {
                            left: 1,
                            right: 1
                        },
                        "scrollY": "500px",
                        "scrollX": false,
                        "scrollCollapse": false, // Changed to true
                        "paging": false,
                        "searching": true,
                        "sorting": false,
                        "responsive": false, // Added responsive option
                    });

                    $('#exportBTNs').html('');
                    table.buttons().container().appendTo('#exportBTNs');

                    table.page.len(1000).draw();
                    $('.dataTables_length').hide();
                    $('.dataTables_info').hide();

                    table.on('mouseenter', 'td', function() {
                        let colIdx = table.cell(this).index().column;
                        table
                            .cells()
                            .nodes()
                            .each((el) => el.classList.remove('highlight'));

                        table
                            .column(colIdx)
                            .nodes()
                            .each((el) => el.classList.add('highlight'));
                    });

                    $('.process_header').each(function() {
                        let text = $(this).text();
                        let newText = text.substring(text.indexOf('_') + 1);
                        $(this).text(newText);
                    });

                    communityWiseGraph('tableAppendTwoGraphTwo', 'Sub Community Wise Listing',
                        'location-names-sub', 'location-counts-sub', 'location-percentage-sub');



                },
                error: function(result) {
                    console.log(result);
                }
            });

        })

        $(document).on("click", ".listingStatus", function() {
            $('.listingStatus').removeClass('active');
            $(this).addClass('active');
            areaWiseListing();
        })

        $(document).on("change", "#portal_id", function() {
            areaWiseListing();
        })



        function areaWiseListing() {

            let status = $('.listingStatus.active').data('status');
            let is_live = $('.listingStatus.active').data('is_live');
            let portal = $('#portal_id').val();
            if (status == undefined) {
                status = null;
            }
            if (is_live == undefined) {
                is_live = null;
            }
            $('#tableAppend').html('');
            $('#divLoader').removeClass('d-none')
            $.ajax({
                url: "{{ route('admin.reports.agent.listing.report') }}",
                type: "POST",
                dataType: 'html',
                data: {
                    'dates': dates,
                    'user_id': currentAgentObj.id,
                    'status': status,
                    'is_live': is_live,
                    'portal': portal,

                },
                success: function(result) {
                    $('#divLoader').addClass('d-none')
                    $('#tableAppend').html(result);



                    $('.showCount').each(function() {
                        let sum = 0;
                        $('#buttons-datatables').find('td:nth-child(' + ($(this).index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(sum);
                    });

                    // at .showPercentage show percentage of each column as total is in last column
                    $('.showPercentage').each(function() {
                        let sum = 0;
                        $('#buttons-datatables').find('td:nth-child(' + ($(this).index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(((sum / $('.showCount').last().text()) * 100).toFixed(2) + '%');
                    });



                    const table = new DataTable('#buttons-datatables', {
                        dom: 'Bfrtip',
                        buttons: [
                            'csv',
                        ],
                        fixedColumns: {
                            left: 1,
                            right: 1
                        },
                        "scrollY": "500px",
                        "scrollX": true,
                        "scrollCollapse": false, // Changed to true
                        "paging": false,
                        "searching": true,
                        "sorting": false,
                        "responsive": false, // Added responsive option

                    });

                    // add export buttons in #exportBTNs
                    $('#exportBTNs').html('');
                    table.buttons().container().appendTo('#exportBTNs');



                    table.page.len(1000).draw();
                    $('.dataTables_length').hide();
                    $('.dataTables_info').hide();

                    table.on('mouseenter', 'td', function() {
                        let colIdx = table.cell(this).index().column;
                        table
                            .cells()
                            .nodes()
                            .each((el) => el.classList.remove('highlight'));

                        table
                            .column(colIdx)
                            .nodes()
                            .each((el) => el.classList.add('highlight'));
                    });
                    // add export buttons

                    $('#portal_id').select2({
                        placeholder: 'Select Portal',
                        allowClear: true,
                        focusOnOpen: false,
                    });

                    $('.showPercentage').each(function() {
                        var columnIndex = $(this).index();
                        if ($(this).text() === '0.00%') {
                            $('table tr').find('td:eq(' + columnIndex + '), th:eq(' +
                                columnIndex + ')').hide();
                        }
                    });


                    communityWiseGraph('tableAppendTwoGraph', 'Area Wise Listing', 'location-names',
                        'location-counts', 'location-percentage');



                }
            });
        }



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
        }

        $("#sidebarReports").addClass('collapse show');
        $('#sidebarAgentReports').addClass('active');
        $("#reportsActive").addClass('active');

        function getActivityLog(user_id = null, dates = null) {

            $("#scrollableActiviyLog2").html("");
            $('#activity-log-div_Placeholder').removeClass('d-none')

            $.ajax({
                url: route('admin.reports.agent.report.activity.log'),
                type: 'GET',
                dataType: 'html',
                data: {
                    subject_model: "User",
                    subject_id: user_id,
                    dates: dates,
                },
                success: function(result) {


                    $('#activity-log-div_Placeholder').addClass('d-none')

                    $("#scrollableActiviyLog2").html(result);
                },
                error: function(result) {
                    $('#activity-log-div_Placeholder').addClass('d-none')

                    console.log(result);
                }
            });
        }

        if ($("#report_agent").hasClass("select2")) {
            $('#report_agent').select2({
                placeholder: 'Select agent',
                allowClear: true,
                focusOnOpen: false,
                templateResult: formatAgent,
                templateSelection: formatAgent
            });
        }

        if ($("#portal_id").hasClass("select2")) {
            $('#portal_id').select2({
                placeholder: 'Select Portal',
                allowClear: true,
                focusOnOpen: false,
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

        function formatAgentView(e) {
            let img = $(e.element).data('image');
            let status = $(e.element).data('status');
            let onlineClass = "online"
            if (!status) {
                onlineClass = "away";
            }
            return (e.text ?? "No Name")
        }



        function communityWiseGraph(divid = 'tableAppendTwoGraph', title = 'Area Wise Listing', location_names_class =
            'location-names', location_counts_class = 'location-counts', location_percentage_class = 'location-percentage'
        ) {
            var chartDom = $('#' + divid)[0];
            var myChart = echarts.init(chartDom);
            var agent_name = $('.agent-name').text();
            var option;
            var locations = [];
            var locations_counts = [];
            var locations_percentage = [];

            $('.' + location_names_class).each(function() {
                let custom_name = $(this).data('title');
                locations.push(custom_name);
            });

            $('.' + location_counts_class).each(function() {
                locations_counts.push($(this).text());
            });

            $('.' + location_percentage_class).each(function() {
                locations_percentage.push($(this).text());
            });
            console.log(locations);
            console.log(locations_counts);
            console.log(locations_percentage);
            var graphData = [];
            for (var i = 0; i < locations_counts.length; i++) {
                if (locations_counts[i] > 0) { // Check if count is not equal to 0
                    // make percentage to 1 decimal point
                    locations_percentage[i] = parseFloat(locations_percentage[i]).toFixed(1);

                    graphData.push({
                        value: locations_counts[i],
                        name: locations[i]
                    });
                }
            }
            graphData.sort(function(a, b) {
                return b.value - a.value;
            });
            var xValues = graphData.map(item => item.name);
            console.log(graphData);

            option = {
                title: {
                    text: title,
                    subtext: agent_name,
                    left: 'center'
                },
                tooltip: {
                    trigger: 'axis', // Use axis trigger for bar chart
                    axisPointer: {
                        type: 'shadow' // Show shadow for better visualization
                    }
                },
                legend: {
                    show: false // Hide legend for bar chart
                },
                xAxis: {
                    type: 'category',
                    data: xValues,
                    axisLabel: {
                        rotate: 45 // Rotate the labels by 45 degrees
                    }
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    name: 'Listings',
                    type: 'bar',
                    data: graphData.map(item => item.value),
                    label: { // Adding label configuration
                        show: true, // Show labels
                        position: 'top', // Position labels on top of the bars
                        color: '#333', // Label text color
                        fontSize: 12, // Label font size
                        formatter: '{c}', // Format label text to display the value
                        emphasis: { // Emphasis style when hovering
                            show: true,
                            textStyle: {
                                color: '#fff', // Label text color on emphasis
                                fontSize: 14 // Label font size on emphasis
                            }
                        }
                    },
                    emphasis: {
                        focus: 'series' // Emphasize on series for better interaction
                    }
                }],
                dataZoom: [{ // Enable data zoom
                    type: 'slider',
                    start: 0,
                    end: 19, // Display only the first 10 records initially
                    showDataShadow: false,
                    showDetail: false,
                }]
            };

            option && myChart.setOption(option);
        }
    </script>

    @yield('pageDT')
@endsection
