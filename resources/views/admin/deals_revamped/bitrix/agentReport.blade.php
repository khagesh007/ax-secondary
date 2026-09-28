@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection
@section('style')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
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
        .btn-outline-secondary {
            border-color: #c48667 !important;
            color: #c48667 !important;
        }

        .btn-check:active+.btn-outline-secondary,
        .btn-check:checked+.btn-outline-secondary,
        .btn-outline-secondary.active,
        .btn-outline-secondary.dropdown-toggle.show,
        .btn-outline-secondary:active {
            background-color: #c48667 !important;
            border-color: #c48667 !important;
            color: #fff !important;
        }

        .btn-outline-secondary:hover {
            background-color: #c48667 !important;
            border-color: #c48667 !important;
            color: #fff !important;
        }
    </style>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">

        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-8">
                        <div class="d-flex align-items-center userDetails" data-agent-id="1165" title="Sam Ayache">
                            <div class="flex-shrink-0 me-2">
                                <img src="{{$agent->image_url}}" alt="" class="avatar-xs rounded">
                            </div>
                            <div class="flex-grow-1">{{$agent->first_name}} {{$agent->last_name}}<p class="mb-0"><small>Deal Reports</small></p></div>
                        </div>
                    </div>
                    <div class="col-4 ">
                        <select name="agent" class="select2" id="report_agent">
                            @if (request()->agent == null || request()->agent == '')
                                <option value="" selected>Select Agent</option>
                            @endif

                            @foreach ($agents ?? [] as $agnt)
                                <option data-image="{{ $agnt->thumbnail }}"
                                    data-image="{{ $agnt->thumbnail }}"
                                    data-status="{{ $agnt->status }}"
                                    @if (request()->agent_id && request()->agent_id == $agnt->id) selected @endif
                                    value="{{ $agnt->id }}">{{ $agnt->first_name }}
                                    {{ $agnt->last_name }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="row">

                    <div class="col-xl-6">

                        <div class="d-flex flex-column h-100">
                            <div class="row">
                                <div class="col-xl-6 col-md-6">
                                    <div class="card card-animate overflow-hidden">
                                        <div class="position-absolute start-0" style="z-index: 0;">
                                            <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 120" width="200" height="120">
                                                <style>
                                                    .s0 {
                                                        opacity: .05;
                                                        fill: var(--vz-success)
                                                    }
                                                </style>
                                                <path id="Shape 8" class="s0" d="m189.5-25.8c0 0 20.1 46.2-26.7 71.4 0 0-60 15.4-62.3 65.3-2.2 49.8-50.6 59.3-57.8 61.5-7.2 2.3-60.8 0-60.8 0l-11.9-199.4z"></path>
                                            </svg>
                                        </div>
                                        <div class="card-body" style="z-index:1 ;">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-3"> Total Deals</p>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                                        <span class="counter-value bitrix_total_deals" data-target="0">0</span></h4>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div id="bitrix_total_deals_radial" data-colors="[&quot;--vz-success&quot;]"
                                                    class="apex-charts" dir="ltr" style="min-height: 88.5px;">
                                                   </div>
                                                </div>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div><!--end col-->
                                <div class="col-xl-6 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate overflow-hidden">
                                        <div class="position-absolute start-0" style="z-index: 0;">
                                            <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 120" width="200" height="120">
                                                <style>
                                                    .s0 {
                                                        opacity: .05;
                                                        fill: var(--vz-success)
                                                    }
                                                </style>
                                                <path id="Shape 8" class="s0" d="m189.5-25.8c0 0 20.1 46.2-26.7 71.4 0 0-60 15.4-62.3 65.3-2.2 49.8-50.6 59.3-57.8 61.5-7.2 2.3-60.8 0-60.8 0l-11.9-199.4z"></path>
                                            </svg>
                                        </div>
                                        <div class="card-body" style="z-index:1 ;">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-3"> Completed Deals</p>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                                        <span class="counter-value bitrix_completed_deals" data-target="0">0</span></h4>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div id="bitrix_completed_deals_radial" data-colors="[&quot;--vz-success&quot;]" class="apex-charts" dir="ltr" style="min-height: 88.5px;">
                                                    </div>
                                                  </div>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div><!-- end col -->
                                <div class="col-xl-6 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate overflow-hidden">
                                        <div class="position-absolute start-0" style="z-index: 0;">
                                            <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 120" width="200" height="120">
                                                <style>
                                                    .s0 {
                                                        opacity: .05;
                                                        fill: var(--vz-success)
                                                    }
                                                </style>
                                                <path id="Shape 8" class="s0" d="m189.5-25.8c0 0 20.1 46.2-26.7 71.4 0 0-60 15.4-62.3 65.3-2.2 49.8-50.6 59.3-57.8 61.5-7.2 2.3-60.8 0-60.8 0l-11.9-199.4z"></path>
                                            </svg>
                                        </div>
                                        <div class="card-body" style="z-index:1 ;">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-3">In Porgress</p>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                                        <span class="counter-value bitrix_in_progress_deals" data-target="0">0</span></h4>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div id="bitrix_in_progress_deals_radial" data-colors="[&quot;--vz-success&quot;]" class="apex-charts" dir="ltr" style="min-height: 88.5px;">
                                                        </div>
                                                        </div>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div><!-- end col -->
                                <div class="col-xl-6 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate overflow-hidden">
                                        <div class="position-absolute start-0" style="z-index: 0;">
                                            <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 120" width="200" height="120">
                                                <style>
                                                    .s0 {
                                                        opacity: .05;
                                                        fill: var(--vz-success)
                                                    }
                                                </style>
                                                <path id="Shape 8" class="s0" d="m189.5-25.8c0 0 20.1 46.2-26.7 71.4 0 0-60 15.4-62.3 65.3-2.2 49.8-50.6 59.3-57.8 61.5-7.2 2.3-60.8 0-60.8 0l-11.9-199.4z"></path>
                                            </svg>
                                        </div>
                                        <div class="card-body" style="z-index:1 ;">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-3"> Rejected/Lost Deals</p>
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                                        <span class="counter-value bitrix_rejected_deals" data-target="0">0</span></h4>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div id="bitrix_rejected_deals_radial" data-colors="[&quot;--vz-danger&quot;]" class="apex-charts" dir="ltr" style="min-height: 88.5px;">
                                                        </div>
                                                        </div>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div><!-- end col -->
                                <!-- end col -->
                            </div><!--end row-->
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="row">
                    <div class="col-xl-6 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Total Deals</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                                                              id="total_deals"
                                                                                              data-target="{{ $total_deals ?? 0 }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.listing.index') }}"
                                           class="text-decoration-underline ax-text">View all deals</a>
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

                    <div class="col-xl-6 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Completed Deals
                                        </p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                                                              id="completed_deals"
                                                                                              data-target="{{ $completed_deals ?? 0 }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.work.contacts.index') }}"
                                           class="text-decoration-underline ax-text">View all Deals</a>
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

                    <div class="col-xl-6 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            In Progress Deals</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                                                              id="rejected_deals"
                                                                                              data-target="{{ $in_progress_deals ?? 0 }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.work.leads.index') }}"
                                           class="text-decoration-underline ax-text">View all deals</a>
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

                    <div class="col-xl-6 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Rejected/Lost Deals</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                                                              id="in_progress_deals"
                                                                                              data-target="{{ $rejected_deals ?? 0 }}">0</span>
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
            </div>
                </div>


                <div class="row">


                    <div class="col-xl-6 ">
                        <div class="card" style="min-height: 423px;">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Bitrix Deal's Lead Summary</h4>
                                <div class="flex-shrink-0">
                                 <!-- end ul -->
                                </div>
                            </div><!-- end card header -->
                            <div class="card-body p-0">
                               <table class="table table-nowrap table-centered align-middle">
                                <thead>
                                    <tr>
                                        <th style="max-width:100px;">Case</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                            <tbody id="bitrixLeadSummary"></tbody>
                            </table><!-- end tab pane -->
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xl-6 ">
                        <div class="card" style="min-height: 423px;">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Recent Deals</h4>
                                <div class="flex-shrink-0">
                                 <!-- end ul -->
                                </div>
                            </div><!-- end card header -->
                            <div class="card-body p-0">
                               <table class="table table-nowrap table-centered align-middle">
                                <thead>
                                    <tr>
                                        <th style="max-width:100px;">Duration</th>
                                        <th>Bitrix</th>
                                        <th>AX CRM</th>

                                    </tr>
                                </thead>
                            <tbody id="recentDealTable"></tbody>
                            </table><!-- end tab pane -->
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-6 ">
                        <div class="card" style="min-height: 423px;">
                            <div class="card-header align-items-center border-0 d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Deals Status</h4>
                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#lead-ax-status-tab"
                                               role="tab" aria-selected="false">AX CRM</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" onclick="getBitrixDealStatusData()" data-bs-toggle="tab"
                                               href="#deal-bitrix-status-tab" role="tab" aria-selected="true">Bitrix</a>
                                        </li>
                                    </ul><!-- end ul -->
                                </div>
                            </div><!-- end card header -->
                            <div class="card-body p-0">
                                <div class="tab-content p-0">
                                    <div class="tab-pane active" id="lead-ax-status-tab" role="tabpanel">
                                        <div class="text-center uk-vertical-align-middle" id="loader_lead_status_pie">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div id="lead-status-pie"
                                             data-colors='["--vz-primary","#a1a1a1",  "--vz-warning", "--vz-info","#a6d8a6eb", "#35c035","--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                             class="e-charts"></div>
                                    </div><!-- end tabpane -->

                                    <div class="tab-pane" id="deal-bitrix-status-tab" role="tabpanel"
                                         style="
                                                    overflow-y: scroll;
                                                    height: 370px;
                                                ">
                                        <div class="text-center uk-vertical-align-middle" id="loader_bitrix_deal_status">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div id="bitrix-deal-status-doughnut"
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
                                <h4 class="card-title mb-0 flex-grow-1">Deal Status (Last 6 months)</h4>
                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#deal-ax-status-tab"
                                               role="tab" aria-selected="false">AX CRM</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" onclick="getBitrixDealStatusData()" data-bs-toggle="tab"
                                               href="#deal-bitrix-status-tab" role="tab" aria-selected="true">Bitrix</a>
                                        </li>
                                    </ul><!-- end ul -->
                                </div>
                            </div>
                            <div class="card-body" >
                                <div class="tab-content p-0">
                                    <div class="tab-pane active" id="deal-ax-status-tab" role="tabpanel" style=" max-height: 370px;">
                                        <div class="text-center uk-vertical-align-middle" id="loader_leadStatusLineChart">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div id="dealStatusLineCharts"
                                             data-colors="[&quot;--vz-success&quot;, &quot;--vz-danger&quot;]" class="apex-charts"
                                             dir="ltr" style="min-height: 305px;">
                                        </div>
                                        <canvas id="dealStatusLineChart"></canvas>

                                    </div>
                                    <div class="tab-pane " id="deal-bitrix-6month-status-tab" role="tabpanel" style=" max-height: 370px;">
                                        <div class="text-center uk-vertical-align-middle" id="loader_leadStatusLineChartBitrix">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div id="bitrix-deal-status-6month"
                                             data-colors="[&quot;--vz-success&quot;, &quot;--vz-danger&quot;]" class="apex-charts"
                                             dir="ltr" style="min-height: 305px;">
                                        </div>
                                        <canvas id="dealStatusBitrixLineChart"></canvas>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-xxl-12 col-lg-12">
                        <div class="card card-height-100 mb-3">
                            <div class="card-header align-items-center border-0 d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Bitrix Deals</h4>
                                <div class="flex-shrink-0">
                    <!-- end ul -->
                                </div>
                            </div>

                            <div class="card-body">
                                <table id="dealDataTable" class="table align-middle" style="width:100%">
                                    <thead class="">
                                        <tr class="table-light2 text-uppercase">
                                        <th scope="col" style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                                       data-com.bitwarden.browser.user-edited="yes">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Bitrix Deal ID</th>
                                        <th>Bitrix Lead</th>
                                        <th>AX CRM Lead</th>
                                        <th>Lead Source</th>
                                        <th>Deal Price</th>
                                        <th>Created By</th>
                                        <th>Created at</th>
                                        <th>Stages</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                </table>


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
    @include('admin.deals_revamped.partials.modal.bitrixDealModal')
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>
    <script src="{{ URL::asset('/assets//deal/js/dealAgentReport.js') }}"></script>
    <script src="{{ URL::asset('/assets//deal/js/bitrixDeal.js') }}"></script>




    <script>

        let total_deal = "{{ $total_deals }}";
        let completed_deal = "{{ $completed_deals }}";
        let rejected_deal = "{{ $rejected_deals }}";
        let in_progress_deal = "{{ $in_progress_deals }}";
        let propertyView = "{{route('admin.listing.index')}}";
        let getBitrixDealUrl = "{{ route('admin.deals_revamped.bitrix.get.deal') }}";
        let bitrixDealFormUrl = "{{route('admin.deals_revamped.bitrix.deal.create')}}";
        let recentDealUrl = "{{route('admin.deals_revamped.report.agent.recent.deals')}}";
        let isBitrixDataFetchedOnce = false;
        let isBitrixDoughnutDrawn = false;
        let agent_id = {{ $agent->id }};
        @php $deal_stages = getBitrixDealStage(); @endphp
        let deal_stages = @json($deal_stages);
        let ax_data = {
            'total_deals': "{{ $total_deals }}",
            'completed_deals': "{{ $completed_deals }}",
            'rejected_deals': "{{ $rejected_deals }}",
            'in_progress_deals': "{{ $in_progress_deals }}"
        }
        let bitrix_data = {
            'total_deals': "",
            'completed_deals': "",
            'rejected_deals': "",
            'in_progress_deals': ""
        }

        const updateDashboardTitleData = (data) => {
            $('#total_deals').text(data.total_deals);
            $('#completed_deals').text(data.completed_deals);
            $('#rejected_deals').text(data.rejected_deals);
            $('#in_progress_deals').text(data.in_progress_deals);
        }




        $('#dashboard-selector').select2({
            placeholder: 'Select Dashboard',
        });

        const getAgentDealSummary = () => {

            $.ajax({
                url: "{{ route('admin.deals_revamped.report.agent.summary') }}?agent_id={{ $agent->id }}",
                type: "GET",
                success: function(result) {
                    if (result.status == '1') {

                        let bitrix_total_deals = result.data.bitrix_total_deals;
                        let bitrix_completed_deals = result.data.bitrix_completed_deals;
                        let bitrix_rejected_deals = result.data.bitrix_rejected_deals;
                        let bitrix_in_progress_deals = result.data.bitrix_in_progress_deals;

                        let axcrm_total_deals = result.data.axcrm_total_deals;
                        let axcrm_completed_deals = result.data.axcrm_completed_deals;
                        let axcrm_rejected_deals = result.data.axcrm_rejected_deals;
                        let axcrm_in_progress_deals = result.data.axcrm_in_progress_deals;

                        let total_deals = parseInt(bitrix_total_deals) + parseInt(axcrm_total_deals);

                        let bitrix_total_deal_percentage = (bitrix_total_deals / total_deals) * 100;
                        drawRadialBar('bitrix_total_deals_radial', bitrix_total_deal_percentage);
                        $(".bitrix_total_deals").text(bitrix_total_deals);


                        let bitrix_completed_deal_percentage = (bitrix_completed_deals / total_deals) * 100;
                        drawRadialBar('bitrix_completed_deals_radial',bitrix_completed_deals);
                        $(".bitrix_completed_deals").text(bitrix_completed_deals);

                        let bitrix_in_progress_deals_percentage = (bitrix_in_progress_deals / total_deals) * 100;
                        drawRadialBar('bitrix_in_progress_deals_radial',bitrix_in_progress_deals);
                        $(".bitrix_in_progress_deals").text(bitrix_in_progress_deals);

                        let bitrix_rejected_deals_percentage = (bitrix_rejected_deals / total_deals) * 100;
                        drawRadialBar('bitrix_rejected_deals_radial',bitrix_rejected_deals);
                        $(".bitrix_rejected_deals").text(bitrix_rejected_deals);
                    }
                }
            });
        }
        const getDealLeadSummary = () =>{
            $.ajax({
             url: "{{ route('admin.deals_revamped.report.agent.lead.summary') }}?agent_id={{ $agent->id }}",
                type: "GET",
                success: function(result) {
                    if (result.status == '1') {
                        let total_leads = result.data.total_leads;
                        let total_deals = result.data.total_deals;
                        let deal_with_leads = result.data.deal_with_leads;
                        let deal_without_leads = result.data.deal_without_leads;
                        let leads_matched = result.data.leads_matched;
                        let leads_not_matched = result.data.leads_not_matched;
                        let html = `<tr>
                                                      <td class="fw-medium">Deal with leads</td>
                                                      <td>${deal_with_leads}/${total_deals}</td>

                                                  </tr>`;
                                                  html += `<tr>
                                                      <td class="fw-medium">Deal without lead</td>
                                                     <td>${deal_without_leads}/${total_deals}</td>

                                                  </tr>`;
                                                  html += `<tr>
                                                      <td class="fw-medium">Leads matched in AX CRM</td>
                                                       <td>${leads_matched}/${total_deals}</td>

                                                  </tr>`;
                                                  html += `<tr>
                                                      <td class="fw-medium">Leads having different sources as compare to AXCRM</td>
                                                     <td>${leads_not_matched}/${total_deals}</td>
                                                  </tr>`;
                                                  html += `<tr>
                                                      <td class="fw-medium">Leads with unknown source</td>
                                                     <td>0/${total_deals}</td>

                                                  </tr>`;
                        $('#bitrixLeadSummary').html(html);
                    }
                }

        });
        }

        getAgentDealSummary();
        getDealLeadSummary();
        getRecentDeals();
        function getDashboardPieData() {
            return '';
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


                    }
                }
            });
        }

        getDashboardPieData();



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


        function saleListSourceDoughnutChart(label, values,elemnt,loader) {
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
            var chart = new ApexCharts(document.querySelector("#"+elemnt), options);
            chart.render();
            $("#"+loader).addClass("d-none");
        }


        $('#sidebarDeals').addClass('active');

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




        // Call the animateIcons function


        ///deal dashboard charts

        function axDealPieChart(data = {}) {


            console.log(data)
            let chartData = [];
            let chartLabel = [];
            jQuery.each(data, function (index, item) {
                console.log(item, index)
                chartData.push(item);
                chartLabel.push(index);
            });
            saleListSourceDoughnutChart(chartLabel, chartData);


            $("#loader_lead_status_pie").addClass("d-none");

        }
        function dealLineChart(data, element = "dealStatusLineChart", loader = "loader_leadStatusLineChart") {
            let months = data?.months;
            let newLeads = data?.new_deals;
            let completed_deals = data?.completed_deals;
            let rejected_deals = data?.rejected_deals;

            var ctx = document.getElementById(element).getContext('2d');


            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'All', // Name the series
                            data: newLeads, // Specify the data values array
                            fill: false,
                            borderColor: '#2196f3', // Add custom color border (Line)
                            backgroundColor: '#2196f3', // Add custom color background (Points and Fill)
                            borderWidth: 1 // Specify bar border width
                        }, {
                            label: 'Completed', // Name the series
                            data: completed_deals, // Specify the data values array
                            fill: false,
                            borderColor: '#45cb85', // Add custom color border (Line)
                            backgroundColor: '#45cb85', // Add custom color background (Points and Fill)
                            borderWidth: 1 // Specify bar border width
                        }, {
                            label: 'Rejected', // Name the series
                            data: rejected_deals, // Specify the data values array
                            fill: false,
                            borderColor: '#f06548', // Add custom color border (Line)
                            backgroundColor: '#f06548', // Add custom color background (Points and Fill)
                            borderWidth: 1 // Specify bar border width
                        }
                    ]
                },
                options: {
                    responsive: true, // Instruct chart js to respond nicely.
                    maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height
                }
            });
            $("#"+loader).addClass("d-none");
        }
        function getChartColorsArray(chartId) {
            if (document.getElementById(chartId) !== null) {
                var colors = document.getElementById(chartId).getAttribute("data-colors");
                colors = JSON.parse(colors);
                return colors.map(function (value) {
                    var newValue = value.replace(" ", "");
                    if (newValue.indexOf(",") === -1) {
                        var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
                        if (color) return color;
                        else return newValue;
                        ;
                    } else {
                        var val = value.split(',');
                        if (val.length == 2) {
                            var rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(val[0]);
                            rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                            return rgbaColor;
                        } else {
                            return newValue;
                        }
                    }
                });
            }
        }
        function getDealStatusData() {

            $.ajax({
                url: "{{ route('admin.work.deals.dashboard.status.data') }}?agent_id="+agent_id,
                type: "GET",
                success: function(result) {

                    if (result.status == 'success') {
                        let labels = [];
                        let values = [];


                        let chartData = [];
                        let chartLabel = [];
                        jQuery.each(result.data.deal_status, function (index, item) {
                            console.log(item, index)
                            chartData.push(item);
                            //make it lowercase and replace _ with space
                            let label = index.replace(/_/g, ' ');
                            label = label.toLowerCase();
                            label = label.charAt(0).toUpperCase() + label.slice(1);
                            chartLabel.push(label);
                        });
                        saleListSourceDoughnutChart(chartLabel, chartData,'lead-status-pie','loader_lead_status_pie');
                        $("#lead-bitrix-status-last6month-tab").removeClass("active");
                        $("#lead-ax-status-last6month-tab").addClass("active");
                        dealLineChart(result.data.deals6month);


                    }
                }
            });


        }
        function getBitrixDealStatusData() {

$.ajax({
    url: "{{ route('admin.deals_revamped.report.agent.bitrix.deal.status') }}?agent_id="+agent_id,
    type: "GET",
    success: function(result) {

        if (result.status == '1') {


            let chartData = [];
            let chartLabel = [];
            jQuery.each(result.data.status, function (index, item) {

                chartData.push(item);
                //make it lowercase and replace _ with space
                let label = index.replace(/_/g, ' ');
                label = label.toLowerCase();
                label = label.charAt(0).toUpperCase() + label.slice(1);
                chartLabel.push(label);
            });

            if(isBitrixDoughnutDrawn == false){
            saleListSourceDoughnutChart(chartLabel, chartData,'bitrix-deal-status-doughnut','loader_bitrix_deal_status');
            }
            isBitrixDoughnutDrawn = true;
            // $("#lead-bitrix-status-last6month-tab").removeClass("active");
            // $("#lead-ax-status-last6month-tab").addClass("active");
            // dealLineChart(result.data.deals6month);


        }
    }
});


}
        function getDealAgentData() {

            $.ajax({
                url: "{{ route('admin.work.deals.dashboard.agent.data') }}",
                type: "GET",
                success: function(result) {

                    if (result.status == '1') {

                        stackedBarChart(result.data.agent_listings);
                    }
                }
            });


        }
        const stackedBarChart = (data) =>{
            $("#loader_agent_listing_overview").addClass("d-none");
            var chartStackedBarColors = getChartColorsArray("agent_listing_stacked_bar");
            if(chartStackedBarColors){
                let agents = [];
                let agent_resi_rent = [];
                let agent_resi_sale = [];
                let agent_com_sale = [];
                let agent_com_rent = [];
                let chartLabel = [];
                let total = data.total;
                jQuery.each(data, function (index, item) {
                    if(item.for_residential_rent != 0 || item.for_residential_sale != 0 || item.for_commercial_sale != 0 || item.for_commercial_rent != 0){
                        agents.push(item.name);
                        agent_resi_rent.push(item.completed);
                        agent_resi_sale.push(item.in_progress);
                        agent_com_sale.push(item.rejected);
                    }
                });


                var options = {
                    series: [{
                        name: 'Completed',
                        data: agent_resi_rent
                    }, {
                        name: 'In Progress',
                        data: agent_com_rent
                    }, {
                        name: 'Rejected',
                        data: agent_resi_sale
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        stacked: true,
                        toolbar: {
                            show: false,
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                        },
                    },
                    stroke: {
                        width: 1,
                        colors: ['#fff']
                    },
                    title: {
                        text: 'Agent Deal Overview',
                        style: {
                            fontWeight: 500,
                        },
                    },
                    xaxis: {
                        categories: agents,
                        labels: {
                            formatter: function (val) {
                                return val
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: undefined
                        },
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left',
                        offsetX: 40
                    },
                    colors: chartStackedBarColors
                };

                var chart = new ApexCharts(document.querySelector("#agent_listing_stacked_bar"), options);
                chart.render();
            }
        }

        const getBitrixDashboardData = () => {

            // $.ajax({
            //     url: "{{ route('admin.work.deals.dashboard.bitrix.data') }}",
            //     type: "GET",
            //     success: function(result) {
            //         if (result.status == '1') {

            //             bitrix_data.total_deals = result.data.total_deals;
            //             bitrix_data.completed_deals = result.data.successful_deals;
            //             bitrix_data.rejected_deals = result.data.lost_deals;
            //             bitrix_data.in_progress_deals = result.data.pending_deals;
            //             updateDashboardTitleData(bitrix_data);


            //             let chartData = [];
            //             let chartLabel = [];
            //             jQuery.each(result.data.deal_status, function (index, item) {

            //                 chartData.push(item);
            //                 //make it lowercase and replace _ with space
            //                 let label = index.replace(/_/g, ' ');
            //                 label = label.toLowerCase();
            //                 label = label.charAt(0).toUpperCase() + label.slice(1);
            //                 chartLabel.push(label);
            //             });
            //             $("#lead-ax-status-tab").removeClass("active");
            //             $("#lead-bitrix-status-tab").addClass("active");
            //             $("#lead-ax-status-last6month-tab").removeClass("active");
            //             $("#lead-bitrix-status-last6month-tab").addClass("active");
            //             if(!isBitrixDataFetchedOnce) {
            //                 saleListSourceDoughnutChart(chartLabel, chartData, 'bitrix-deal-status-pie', 'loader_bitrix_deal_status');
            //                 dealLineChart(result.data.deals6month, 'dealStatusBitrixLineChart', 'loader_leadStatusLineChartBitrix');
            //             }
            //             isBitrixDataFetchedOnce = true;
            //         }
            //     }
            // });


        }
        $(".dashboardType").change(function () {

            if($(this).val() == 'bitrix'){
                getBitrixDashboardData();

            }else{
                $("#lead-ax-status-tab, #lead-ax-status-last6month-tab").addClass("active");
                $("#lead-bitrix-status-tab, #lead-bitrix-status-last6month-tab").removeClass("active");
                updateDashboardTitleData(ax_data);
            }
        });

        getDealAgentData();
        getDealStatusData();
        getBitrixDealStatusData()
        $("#report_agent").change(function () {
            let agent_id = $(this).val();
            if (agent_id != '') {
                window.location.href = "{{ route('admin.deals_revamped.bitrix.report.agent') }}/?agent_id=" + agent_id;
            }
        });
        let dataTable = $('#dealDataTable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            dom: 'Bfrtip',
            buttons: [
                'colvis'
            ],
            language: {
                searchPlaceholder: "Search deals"
            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{route('admin.deals_revamped.report.agent.bitrix.dt')}}",
                type: "post",
                data: function (d) {
                    d.agent_id = "{{ $agent->id }}";

                },
                error: function () {
                    $(".users-error").html("");
                    $("#dataTable").append('<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#dataTable_processing").css("display", "none");
                }
            },
            fnInitComplete: function (oSettings, json) {
                let first = $("#dataTable_wrapper .row div:first");
                first.addClass("col-md-5").removeClass("col-md-6")
                $(first).next().addClass("col-md-7").removeClass("col-md-6")
                $(".dataTables_filter label").addClass("d-flex justify-between-between align-items-center gap-2")

            },

            "aoColumns": [
                {
                    data: null,
                    orderable: false,
                }, {
                    data: 'id',
                    name: 'id',
                }, {
                    data: 'bitrix_deal_id',
                    name: 'bitrix_deal_id',
                }, {
                    data:  null,
                    name: 'id',
                    orderable: false,
                },
                {
                    data: null,
                    orderable: false,
                }, {
                    data:  null,
                    name: 'id',
                    orderable: false,
                }, {
                    data: 'id',
                    name: 'id',
                    orderable: false,
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    orderable: false,
                },
                {
                    data: 'is_used',
                    name: 'is_used',
                    orderable: false,
                },
                 {
                    data: 'id',
                    orderable: false,
                },
                {
                    data: 'id',
                    orderable: false,
                }
            ],
            "columnDefs": [
                {
                    "targets": 0,
                    orderable: true,
                    visible: false,
                    render: function (type, row, data, meta) {
                        return `<div class="form-check">
                                                <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` + data.id + `">
                                            </div>`;
                    }
                },
                {
                    "targets": 1,
                    orderable: true,
                    visible: false,
                    render: function (type, row, data, meta) {
                        return data.id;
                      //  return `<a href="javascript:void(0)" class="text-primary" onclick="viewData('` + data.id + `')">` + (data?.deal_type ? data.deal_type.name : "") + `</a>`;
                    }
                },{
                    "targets": 2,
                    orderable: true,
                    render: function (type, row, data, meta) {

                        return data.bitrix_deal_id;
                    }
                },
                {
                    "targets": 3,
                    orderable: true,
                    render: function (type, row, data, meta) {
                       if(data.deal_data != null) {
                        let json_data = JSON.parse(data.deal_data);

                        return json_data.TITLE;
                       }

                        return "N/A";


                    }
                },{
                    "targets": 4,
                    orderable: true,
                    render: function (type, row, data, meta) {
                       if(data.lead != null) {
                        return data?.lead?.first_name+" "+data?.lead.last_name;
                       }

                        return "N/A";


                    }
                },
                {
                    targets: 5,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        if(data.deal_leads == null){
                                return '-';
                        }
                        if(data.lead_source_matched){
                            return `<span class="badge bg-success">Matched</span>`;
                        }else{
                            return `<span class="badge bg-danger">Not Matched</span>`;
                        }

                    }
                },{
                    "targets": 6,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        if(data.deal_data != null) {
                        let json_data = JSON.parse(data.deal_data);
                        return  numberFormatAX(json_data.OPPORTUNITY);
                       }
                        return '-';
                    }
                }, {
                    targets: 7,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        if (!data.updated_by) return "-";
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.updated_by.id}"  title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.first_name}<p title="Updated at" class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>
                                </div>`;
                    }
                }, {
                    targets: 8,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        if (data.updated_at == null) {
                            return '-';
                        } else {
                            return moment(data.deal_created_at).format('MM/DD/YYYY hh:mm:ss');
                        }
                    }
                }, {
                    targets: 9,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {

                        let json_data = JSON.parse(data.deal_data);
                        let stage = json_data.STAGE_ID;
                        return deal_stages[stage] || '-';
                    }
                },

                {
                    targets: 10,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        return `<div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a href="javascript:void(0)" onclick="showDeal('`+data.id+`')" data-id="` + data.id + `" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                        <li>
                                            <a href="javascript:void(0)" onclick="deleteData(` + data.id + `)" data-id="` + data.id + `" class="dropdown-item remove-item-btn delete-link">
                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>`
                    }
                }
            ]
        });

    </script>
@endsection
