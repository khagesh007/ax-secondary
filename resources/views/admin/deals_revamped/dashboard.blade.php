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
                    <div class="col-8"></div>
                <div class="col-4 ">
                    <!-- Radio Buttons -->
                    <div class="btn-group float-end" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check dashboardType" name="btnradio" id="btnradio1" autocomplete="off" value="ax" checked="">
                        <label class="btn btn-outline-secondary" for="btnradio1">AX CRM</label>

                        <input type="radio" class="btn-check dashboardType" name="btnradio" id="btnradio2" value="bitrix" autocomplete="off">
                        <label class="btn btn-outline-secondary" for="btnradio2">BITRIX</label>
                    </div>
                </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-md-6">
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

                    <div class="col-xl-3 col-md-6">
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

                    <div class="col-xl-3 col-md-6">
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

                    <div class="col-xl-3 col-md-6">
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
                <div class="row d-none">
                    <div class="col-xl-4">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Deal Agent Overview</h4>
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
                        <div class="card" style="min-height: 447px;">
                            <div class="card-header align-items-center border-0 d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Deals</h4>
                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#lead-ax-status-tab"
                                                role="tab" aria-selected="false">Status</a>
                                        </li>
                                        <li class="nav-item d-none">
                                            <a class="nav-link" onclick="leadSourcePieChart()" data-bs-toggle="tab"
                                                href="#lead-bitrix-status-tab" role="tab" aria-selected="true">Source</a>
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

                                    <div class="tab-pane" id="lead-bitrix-status-tab" role="tabpanel"
                                        style="
                                                    overflow-y: scroll;
                                                    height: 370px;
                                                ">
                                        <div class="text-center uk-vertical-align-middle" id="loader_bitrix_deal_status">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div id="bitrix-deal-status-pie"
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
                            </div>
                            <div class="card-body" >
                                <div class="tab-content p-0">
                                    <div class="tab-pane active" id="lead-ax-status-last6month-tab" role="tabpanel" style=" max-height: 370px;">
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
                                    <div class="tab-pane " id="lead-bitrix-status-last6month-tab" role="tabpanel" style=" max-height: 370px;">
                                        <div class="text-center uk-vertical-align-middle" id="loader_leadStatusLineChartBitrix">
                                            <div class="spinner-border text-success mt-5 mb-5" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                        <div id="dealStatusLineCharts"
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
                                <h4 class="card-title mb-0 flex-grow-1">Agent Overview</h4>
                                <div class="flex-shrink-0">
                                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                        role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab"
                                            href="#agents-overview-tab"
                                              id="axCrmAgentDataTab"
                                                role="tab" aria-selected="false">AX CRM</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#agent-status-bitrix-tab"
                                                role="tab"
                                                id="bitrixAgentDataTab"
                                                aria-selected="true">Bitrix</a>
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

                                    <div class="tab-pane tasks-board" id="agent-status-bitrix-tab" role="tabpanel">

                                        <div class="" style="width:6500px; height:500px; overflow-x: scroll;">
                                            <div id="agent_status_bitrix_stacked_bar"
                                                data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                                class="apex-charts" dir="ltr"></div>
                                        </div>
                                        </div>


                                    </div>


                                </div>


                            </div>

                        </div><!-- end tab pane -->
                    </div><!-- end cardbody -->
                </div>



            </div> <!-- end .h-100-->

        </div> <!-- end col -->


    </div>

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




    <script>

        let total_deal = "{{ $total_deals }}";
        let completed_deal = "{{ $completed_deals }}";
        let rejected_deal = "{{ $rejected_deals }}";
        let in_progress_deal = "{{ $in_progress_deals }}";
        let isBitrixDataFetchedOnce = false;
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
                url: "{{ route('admin.work.deals.dashboard.status.data') }}",
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
        function getDealAgentData() {

            $.ajax({
                url: "{{ route('admin.work.deals.dashboard.agent.data') }}",
                type: "GET",
                success: function(result) {

                    if (result.status == '1') {

                        stackedBarChart(result.data.agent_ax_data,'agent_listing_stacked_bar');
                        stackedBarChart(result.data.agent_bitrix_data,'agent_status_bitrix_stacked_bar');
                    }
                }
            });


        }
        const stackedBarChart = (data,ele) =>{
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

                var chart = new ApexCharts(document.querySelector("#"+ele), options);
                chart.render();
            }
        }

        const getBitrixDashboardData = () => {

            $.ajax({
                url: "{{ route('admin.work.deals.dashboard.bitrix.data') }}",
                type: "GET",
                success: function(result) {
                    if (result.status == '1') {

                        bitrix_data.total_deals = result.data.total_deals;
                        bitrix_data.completed_deals = result.data.successful_deals;
                        bitrix_data.rejected_deals = result.data.lost_deals;
                        bitrix_data.in_progress_deals = result.data.pending_deals;
                        updateDashboardTitleData(bitrix_data);


                        let chartData = [];
                        let chartLabel = [];
                        jQuery.each(result.data.deal_status, function (index, item) {

                            chartData.push(item);
                            //make it lowercase and replace _ with space
                            let label = index.replace(/_/g, ' ');
                            label = label.toLowerCase();
                            label = label.charAt(0).toUpperCase() + label.slice(1);
                            chartLabel.push(label);
                        });
                        $("#lead-ax-status-tab").removeClass("active");
                        $("#lead-bitrix-status-tab").addClass("active");
                        $("#lead-ax-status-last6month-tab").removeClass("active");
                        $("#lead-bitrix-status-last6month-tab").addClass("active");
                        if(!isBitrixDataFetchedOnce) {
                            saleListSourceDoughnutChart(chartLabel, chartData, 'bitrix-deal-status-pie', 'loader_bitrix_deal_status');
                            dealLineChart(result.data.deals6month, 'dealStatusBitrixLineChart', 'loader_leadStatusLineChartBitrix');
                        }
                        isBitrixDataFetchedOnce = true;
                    }
                }
            });


        }
        $(".dashboardType").change(function () {

            if($(this).val() == 'bitrix'){
                $("#bitrixAgentDataTab").tab('show');

                getBitrixDashboardData();

            }else{
                $("#axCrmAgentDataTab").tab('show')
                $("#lead-ax-status-tab, #lead-ax-status-last6month-tab").addClass("active");
                $("#lead-bitrix-status-tab, #lead-bitrix-status-last6month-tab").removeClass("active");
                updateDashboardTitleData(ax_data);
            }
        });

        getDealAgentData();
        getDealStatusData();



        $("#dealSidebarBlock").addClass('collapse show');
        $("#dealDashboardSidebar").addClass('active');
        $("#dealSidebarBlock").addClass('active');
    </script>
@endsection
