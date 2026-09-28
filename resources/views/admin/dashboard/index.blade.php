@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row d-none" id="notification-request-tab">

        <div class="col-12">
            <!-- Info Alert -->
            <div class="alert alert-info alert-dismissible alert-label-icon rounded-label shadow fade show" role="alert">
                <i class="ri-airplay-line label-icon"></i><strong>Hi {{ auth()?->user()?->name }}</strong> - You can receive
                notification in your web browser, you need to enable this

                <button type="button" class="btn rounded-pill btn-info waves-effect waves-light"
                    id="notification-show-button">Check if enabled</button>
                <button class="btn rounded-pill btn-danger waves-effect waves-light" id="permission-request-button">Request
                    permissions</button>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

            </div>
        </div>
    </div>

    <div class="row">

        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">{{ timeGreetings() }}, {{ auth()?->user()?->name }}!</h4>
                                <p class="text-muted mb-0">Here's what's happening with your properties
                                    today.</p>
                            </div>
                            <div class="mt-3 mt-lg-0 d-none">
                                <form action="javascript:void(0);">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <div class="col-sm-auto">
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control border-0 dash-filter-picker shadow"
                                                    data-provider="flatpickr" data-range-date="true"
                                                    data-date-format="d M, Y"
                                                    data-deafult-date="01 Jan 2022 to 31 Jan 2022">
                                                <div class="input-group-text bg-primary border-primary text-white">
                                                    <i class="ri-calendar-2-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xxl-6">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Listing Overview</h4>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-nowrap align-middle mb-3">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 30%;">Status</th>
                                                <th scope="col" style="width: 30%;">Count</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td>Published</td>
                                                <td>12</td>
                                            </tr>
                                            <tr>
                                                <td>Published</td>
                                                <td>12</td>
                                            </tr>
                                            <tr>
                                                <td>Published</td>
                                                <td>12</td>
                                            </tr>
                                            <tr>
                                                <td>Published</td>
                                                <td>12</td>
                                            </tr>
                                        </tbody><!-- end tbody -->
                                    </table><!-- end table -->
                                </div><!-- end table responsive -->
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xxl-6">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Listing Activities</h4>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-nowrap align-middle mb-3">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 30%;">Time</th>
                                                <th scope="col" style="width: 30%;">Count</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td>Past 7 days</td>
                                                <td>12</td>
                                            </tr>
                                            <tr>
                                                <td>Past 14 days</td>
                                                <td>12</td>
                                            </tr>
                                            <tr>
                                                <td>Past 21 days</td>
                                                <td>12</td>
                                            </tr>
                                            <tr>
                                                <td>Past 28 days</td>
                                                <td>12</td>
                                            </tr>
                                        </tbody><!-- end tbody -->
                                    </table><!-- end table -->
                                </div><!-- end table responsive -->
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-xxl-4 col-lg-4">
                        <div class="card card-height-100 mb-3">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Property Finder</h4>
                                <div class="flex-shrink-0">
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div id="prjects-status"
                                    data-colors="[&quot;--vz-success&quot;, &quot;--vz-primary&quot;, &quot;--vz-warning&quot;, &quot;--vz-danger&quot;]"
                                    class="apex-charts" dir="ltr" style="min-height: 212.8px;">
                                    <div id="apexchartscrz4u1xuf"
                                        class="apexcharts-canvas apexchartscrz4u1xuf apexcharts-theme-light"
                                        style="width: 357px; height: 212.8px;"><svg id="SvgjsSvg1588" width="357"
                                            height="212.8" xmlns="http://www.w3.org/2000/svg" version="1.1"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev"
                                            class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)"
                                            style="background: transparent;">
                                            <g id="SvgjsG1590" class="apexcharts-inner apexcharts-graphical"
                                                transform="translate(76.5, 0)">
                                                <defs id="SvgjsDefs1589">
                                                    <clipPath id="gridRectMaskcrz4u1xuf">
                                                        <rect id="SvgjsRect1592" width="210" height="228"
                                                            x="-2" y="0" rx="0" ry="0"
                                                            opacity="1" stroke-width="0" stroke="none"
                                                            stroke-dasharray="0" fill="#fff"></rect>
                                                    </clipPath>
                                                    <clipPath id="forecastMaskcrz4u1xuf"></clipPath>
                                                    <clipPath id="nonForecastMaskcrz4u1xuf"></clipPath>
                                                    <clipPath id="gridRectMarkerMaskcrz4u1xuf">
                                                        <rect id="SvgjsRect1593" width="210" height="232"
                                                            x="-2" y="-2" rx="0" ry="0"
                                                            opacity="1" stroke-width="0" stroke="none"
                                                            stroke-dasharray="0" fill="#fff"></rect>
                                                    </clipPath>
                                                </defs>
                                                <g id="SvgjsG1594" class="apexcharts-pie">
                                                    <g id="SvgjsG1595" transform="translate(0, 0) scale(1)">
                                                        <circle id="SvgjsCircle1596" r="86.8390243902439" cx="103"
                                                            cy="103" fill="transparent"></circle>
                                                        <g id="SvgjsG1597" class="apexcharts-slices">
                                                            <g id="SvgjsG1598"
                                                                class="apexcharts-series apexcharts-pie-series"
                                                                seriesName="Completed" rel="1" data:realIndex="0">
                                                                <path id="SvgjsPath1599"
                                                                    d="M 103 6.512195121951208 A 96.48780487804879 96.48780487804879 0 0 1 160.64719711426022 180.37374978023257 L 154.8824774028342 172.63637480220933 A 86.8390243902439 86.8390243902439 0 0 0 103 16.160975609756093 L 103 6.512195121951208 z"
                                                                    fill="rgba(10,179,156,1)" fill-opacity="1"
                                                                    stroke-opacity="1" stroke-linecap="round"
                                                                    stroke-width="0" stroke-dasharray="0"
                                                                    class="apexcharts-pie-area apexcharts-donut-slice-0"
                                                                    index="0" j="0"
                                                                    data:angle="143.31210191082803" data:startAngle="0"
                                                                    data:strokeWidth="0" data:value="125"
                                                                    data:pathOrig="M 103 6.512195121951208 A 96.48780487804879 96.48780487804879 0 0 1 160.64719711426022 180.37374978023257 L 154.8824774028342 172.63637480220933 A 86.8390243902439 86.8390243902439 0 0 0 103 16.160975609756093 L 103 6.512195121951208 z">
                                                                </path>
                                                            </g>
                                                            <g id="SvgjsG1600"
                                                                class="apexcharts-series apexcharts-pie-series"
                                                                seriesName="InxProgress" rel="2"
                                                                data:realIndex="1">
                                                                <path id="SvgjsPath1601"
                                                                    d="M 160.64719711426022 180.37374978023257 A 96.48780487804879 96.48780487804879 0 0 1 83.82123957893091 197.56252766765306 L 85.73911562103783 188.10627490088774 A 86.8390243902439 86.8390243902439 0 0 0 154.8824774028342 172.63637480220933 L 160.64719711426022 180.37374978023257 z"
                                                                    fill="rgba(64,81,137,1)" fill-opacity="1"
                                                                    stroke-opacity="1" stroke-linecap="round"
                                                                    stroke-width="0" stroke-dasharray="0"
                                                                    class="apexcharts-pie-area apexcharts-donut-slice-1"
                                                                    index="0" j="1"
                                                                    data:angle="48.152866242038215"
                                                                    data:startAngle="143.31210191082803"
                                                                    data:strokeWidth="0" data:value="42"
                                                                    data:pathOrig="M 160.64719711426022 180.37374978023257 A 96.48780487804879 96.48780487804879 0 0 1 83.82123957893091 197.56252766765306 L 85.73911562103783 188.10627490088774 A 86.8390243902439 86.8390243902439 0 0 0 154.8824774028342 172.63637480220933 L 160.64719711426022 180.37374978023257 z">
                                                                </path>
                                                            </g>
                                                            <g id="SvgjsG1602"
                                                                class="apexcharts-series apexcharts-pie-series"
                                                                seriesName="YetxtoxStart" rel="3"
                                                                data:realIndex="2">
                                                                <path id="SvgjsPath1603"
                                                                    d="M 83.82123957893091 197.56252766765306 A 96.48780487804879 96.48780487804879 0 0 1 8.634086897172239 123.12388964524445 L 18.070678207455032 121.11150068072 A 86.8390243902439 86.8390243902439 0 0 0 85.73911562103783 188.10627490088774 L 83.82123957893091 197.56252766765306 z"
                                                                    fill="rgba(247,184,75,1)" fill-opacity="1"
                                                                    stroke-opacity="1" stroke-linecap="round"
                                                                    stroke-width="0" stroke-dasharray="0"
                                                                    class="apexcharts-pie-area apexcharts-donut-slice-2"
                                                                    index="0" j="2"
                                                                    data:angle="66.49681528662418"
                                                                    data:startAngle="191.46496815286625"
                                                                    data:strokeWidth="0" data:value="58"
                                                                    data:pathOrig="M 83.82123957893091 197.56252766765306 A 96.48780487804879 96.48780487804879 0 0 1 8.634086897172239 123.12388964524445 L 18.070678207455032 121.11150068072 A 86.8390243902439 86.8390243902439 0 0 0 85.73911562103783 188.10627490088774 L 83.82123957893091 197.56252766765306 z">
                                                                </path>
                                                            </g>
                                                            <g id="SvgjsG1604"
                                                                class="apexcharts-series apexcharts-pie-series"
                                                                seriesName="Cancelled" rel="4" data:realIndex="3">
                                                                <path id="SvgjsPath1605"
                                                                    d="M 8.634086897172239 123.12388964524445 A 96.48780487804879 96.48780487804879 0 0 1 102.98315970125404 6.512196591544509 L 102.98484373112863 16.160976932390057 A 86.8390243902439 86.8390243902439 0 0 0 18.070678207455032 121.11150068072 L 8.634086897172239 123.12388964524445 z"
                                                                    fill="rgba(240,101,72,1)" fill-opacity="1"
                                                                    stroke-opacity="1" stroke-linecap="round"
                                                                    stroke-width="0" stroke-dasharray="0"
                                                                    class="apexcharts-pie-area apexcharts-donut-slice-3"
                                                                    index="0" j="3"
                                                                    data:angle="102.03821656050957"
                                                                    data:startAngle="257.96178343949043"
                                                                    data:strokeWidth="0" data:value="89"
                                                                    data:pathOrig="M 8.634086897172239 123.12388964524445 A 96.48780487804879 96.48780487804879 0 0 1 102.98315970125404 6.512196591544509 L 102.98484373112863 16.160976932390057 A 86.8390243902439 86.8390243902439 0 0 0 18.070678207455032 121.11150068072 L 8.634086897172239 123.12388964524445 z">
                                                                </path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </g>
                                                <line id="SvgjsLine1606" x1="0" y1="0" x2="206"
                                                    y2="0" stroke="#b6b6b6" stroke-dasharray="0"
                                                    stroke-width="1" stroke-linecap="butt"
                                                    class="apexcharts-ycrosshairs"></line>
                                                <line id="SvgjsLine1607" x1="0" y1="0" x2="206"
                                                    y2="0" stroke-dasharray="0" stroke-width="0"
                                                    stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line>
                                            </g>
                                            <g id="SvgjsG1591" class="apexcharts-annotations"></g>
                                        </svg>
                                        <div class="apexcharts-legend"></div>
                                        <div class="apexcharts-tooltip apexcharts-theme-dark">
                                            <div class="apexcharts-tooltip-series-group" style="order: 1;"><span
                                                    class="apexcharts-tooltip-marker"
                                                    style="background-color: rgb(10, 179, 156);"></span>
                                                <div class="apexcharts-tooltip-text"
                                                    style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                                    <div class="apexcharts-tooltip-y-group"><span
                                                            class="apexcharts-tooltip-text-y-label"></span><span
                                                            class="apexcharts-tooltip-text-y-value"></span></div>
                                                    <div class="apexcharts-tooltip-goals-group"><span
                                                            class="apexcharts-tooltip-text-goals-label"></span><span
                                                            class="apexcharts-tooltip-text-goals-value"></span></div>
                                                    <div class="apexcharts-tooltip-z-group"><span
                                                            class="apexcharts-tooltip-text-z-label"></span><span
                                                            class="apexcharts-tooltip-text-z-value"></span></div>
                                                </div>
                                            </div>
                                            <div class="apexcharts-tooltip-series-group" style="order: 2;"><span
                                                    class="apexcharts-tooltip-marker"
                                                    style="background-color: rgb(64, 81, 137);"></span>
                                                <div class="apexcharts-tooltip-text"
                                                    style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                                    <div class="apexcharts-tooltip-y-group"><span
                                                            class="apexcharts-tooltip-text-y-label"></span><span
                                                            class="apexcharts-tooltip-text-y-value"></span></div>
                                                    <div class="apexcharts-tooltip-goals-group"><span
                                                            class="apexcharts-tooltip-text-goals-label"></span><span
                                                            class="apexcharts-tooltip-text-goals-value"></span></div>
                                                    <div class="apexcharts-tooltip-z-group"><span
                                                            class="apexcharts-tooltip-text-z-label"></span><span
                                                            class="apexcharts-tooltip-text-z-value"></span></div>
                                                </div>
                                            </div>
                                            <div class="apexcharts-tooltip-series-group" style="order: 3;"><span
                                                    class="apexcharts-tooltip-marker"
                                                    style="background-color: rgb(247, 184, 75);"></span>
                                                <div class="apexcharts-tooltip-text"
                                                    style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                                    <div class="apexcharts-tooltip-y-group"><span
                                                            class="apexcharts-tooltip-text-y-label"></span><span
                                                            class="apexcharts-tooltip-text-y-value"></span></div>
                                                    <div class="apexcharts-tooltip-goals-group"><span
                                                            class="apexcharts-tooltip-text-goals-label"></span><span
                                                            class="apexcharts-tooltip-text-goals-value"></span></div>
                                                    <div class="apexcharts-tooltip-z-group"><span
                                                            class="apexcharts-tooltip-text-z-label"></span><span
                                                            class="apexcharts-tooltip-text-z-value"></span></div>
                                                </div>
                                            </div>
                                            <div class="apexcharts-tooltip-series-group" style="order: 4;"><span
                                                    class="apexcharts-tooltip-marker"
                                                    style="background-color: rgb(240, 101, 72);"></span>
                                                <div class="apexcharts-tooltip-text"
                                                    style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                                    <div class="apexcharts-tooltip-y-group"><span
                                                            class="apexcharts-tooltip-text-y-label"></span><span
                                                            class="apexcharts-tooltip-text-y-value"></span></div>
                                                    <div class="apexcharts-tooltip-goals-group"><span
                                                            class="apexcharts-tooltip-text-goals-label"></span><span
                                                            class="apexcharts-tooltip-text-goals-value"></span></div>
                                                    <div class="apexcharts-tooltip-z-group"><span
                                                            class="apexcharts-tooltip-text-z-label"></span><span
                                                            class="apexcharts-tooltip-text-z-value"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-center align-items-center mb-4">
                                        <h2 class="me-3 ff-secondary mb-0">258</h2>
                                        <div>
                                            <p class="text-muted mb-0">Total Properties</p>
                                            <p class="text-success fw-medium mb-0">
                                                <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                        class="ri-arrow-right-up-line"></i></span> +3 New
                                            </p>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                            Total</p>
                                        <div>
                                            <span class="text-muted">125 Properties</span>
                                            {{-- <span class="text-success fw-medium fs-12">15870hrs</span> --}}
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>
                                            Standard</p>
                                        <div>
                                            <span class="text-muted">42 Properties</span>
                                            {{-- <span class="text-success fw-medium fs-12">243hrs</span> --}}
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-checkbox-blank-circle-fill text-warning align-middle me-2"></i>Premium
                                        </p>
                                        <div>
                                            <span class="text-muted">58 Properties</span>
                                            {{-- <span class="text-success fw-medium fs-12">~2050hrs</span> --}}
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex justify-content-between py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-checkbox-blank-circle-fill text-danger align-middle me-2"></i>
                                            Featured</p>
                                        <div>
                                            <span class="text-muted">89 Properties</span>
                                            {{-- <span class="text-success fw-medium fs-12">~900hrs</span> --}}
                                        </div>
                                    </div><!-- end -->
                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xxl-4 col-lg-4">
                        <div class="card card-height-100 mb-3">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Bayut</h4>
                                <div class="flex-shrink-0">

                                </div>
                            </div>

                            <div class="card-body">
                                <div id="prjects-status"
                                    data-colors="[&quot;--vz-success&quot;, &quot;--vz-primary&quot;, &quot;--vz-warning&quot;, &quot;--vz-danger&quot;]"
                                    class="apex-charts" dir="ltr" style="min-height: 212.8px;">
                                    <div id="apexchartscrz4u1xuf"
                                        class="apexcharts-canvas apexchartscrz4u1xuf apexcharts-theme-light"
                                        style="width: 357px; height: 212.8px;"><svg id="SvgjsSvg1588" width="357"
                                            height="212.8" xmlns="http://www.w3.org/2000/svg" version="1.1"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev"
                                            class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)"
                                            style="background: transparent;">
                                            <g id="SvgjsG1590" class="apexcharts-inner apexcharts-graphical"
                                                transform="translate(76.5, 0)">
                                                <defs id="SvgjsDefs1589">
                                                    <clipPath id="gridRectMaskcrz4u1xuf">
                                                        <rect id="SvgjsRect1592" width="210" height="228"
                                                            x="-2" y="0" rx="0" ry="0"
                                                            opacity="1" stroke-width="0" stroke="none"
                                                            stroke-dasharray="0" fill="#fff"></rect>
                                                    </clipPath>
                                                    <clipPath id="forecastMaskcrz4u1xuf"></clipPath>
                                                    <clipPath id="nonForecastMaskcrz4u1xuf"></clipPath>
                                                    <clipPath id="gridRectMarkerMaskcrz4u1xuf">
                                                        <rect id="SvgjsRect1593" width="210" height="232"
                                                            x="-2" y="-2" rx="0" ry="0"
                                                            opacity="1" stroke-width="0" stroke="none"
                                                            stroke-dasharray="0" fill="#fff"></rect>
                                                    </clipPath>
                                                </defs>
                                                <g id="SvgjsG1594" class="apexcharts-pie">
                                                    <g id="SvgjsG1595" transform="translate(0, 0) scale(1)">
                                                        <circle id="SvgjsCircle1596" r="86.8390243902439" cx="103"
                                                            cy="103" fill="transparent"></circle>
                                                        <g id="SvgjsG1597" class="apexcharts-slices">
                                                            <g id="SvgjsG1598"
                                                                class="apexcharts-series apexcharts-pie-series"
                                                                seriesName="Completed" rel="1" data:realIndex="0">
                                                                <path id="SvgjsPath1599"
                                                                    d="M 103 6.512195121951208 A 96.48780487804879 96.48780487804879 0 0 1 160.64719711426022 180.37374978023257 L 154.8824774028342 172.63637480220933 A 86.8390243902439 86.8390243902439 0 0 0 103 16.160975609756093 L 103 6.512195121951208 z"
                                                                    fill="rgba(10,179,156,1)" fill-opacity="1"
                                                                    stroke-opacity="1" stroke-linecap="round"
                                                                    stroke-width="0" stroke-dasharray="0"
                                                                    class="apexcharts-pie-area apexcharts-donut-slice-0"
                                                                    index="0" j="0"
                                                                    data:angle="143.31210191082803" data:startAngle="0"
                                                                    data:strokeWidth="0" data:value="125"
                                                                    data:pathOrig="M 103 6.512195121951208 A 96.48780487804879 96.48780487804879 0 0 1 160.64719711426022 180.37374978023257 L 154.8824774028342 172.63637480220933 A 86.8390243902439 86.8390243902439 0 0 0 103 16.160975609756093 L 103 6.512195121951208 z">
                                                                </path>
                                                            </g>
                                                            <g id="SvgjsG1600"
                                                                class="apexcharts-series apexcharts-pie-series"
                                                                seriesName="InxProgress" rel="2"
                                                                data:realIndex="1">
                                                                <path id="SvgjsPath1601"
                                                                    d="M 160.64719711426022 180.37374978023257 A 96.48780487804879 96.48780487804879 0 0 1 83.82123957893091 197.56252766765306 L 85.73911562103783 188.10627490088774 A 86.8390243902439 86.8390243902439 0 0 0 154.8824774028342 172.63637480220933 L 160.64719711426022 180.37374978023257 z"
                                                                    fill="rgba(64,81,137,1)" fill-opacity="1"
                                                                    stroke-opacity="1" stroke-linecap="round"
                                                                    stroke-width="0" stroke-dasharray="0"
                                                                    class="apexcharts-pie-area apexcharts-donut-slice-1"
                                                                    index="0" j="1"
                                                                    data:angle="48.152866242038215"
                                                                    data:startAngle="143.31210191082803"
                                                                    data:strokeWidth="0" data:value="42"
                                                                    data:pathOrig="M 160.64719711426022 180.37374978023257 A 96.48780487804879 96.48780487804879 0 0 1 83.82123957893091 197.56252766765306 L 85.73911562103783 188.10627490088774 A 86.8390243902439 86.8390243902439 0 0 0 154.8824774028342 172.63637480220933 L 160.64719711426022 180.37374978023257 z">
                                                                </path>
                                                            </g>
                                                            <g id="SvgjsG1602"
                                                                class="apexcharts-series apexcharts-pie-series"
                                                                seriesName="YetxtoxStart" rel="3"
                                                                data:realIndex="2">
                                                                <path id="SvgjsPath1603"
                                                                    d="M 83.82123957893091 197.56252766765306 A 96.48780487804879 96.48780487804879 0 0 1 8.634086897172239 123.12388964524445 L 18.070678207455032 121.11150068072 A 86.8390243902439 86.8390243902439 0 0 0 85.73911562103783 188.10627490088774 L 83.82123957893091 197.56252766765306 z"
                                                                    fill="rgba(247,184,75,1)" fill-opacity="1"
                                                                    stroke-opacity="1" stroke-linecap="round"
                                                                    stroke-width="0" stroke-dasharray="0"
                                                                    class="apexcharts-pie-area apexcharts-donut-slice-2"
                                                                    index="0" j="2"
                                                                    data:angle="66.49681528662418"
                                                                    data:startAngle="191.46496815286625"
                                                                    data:strokeWidth="0" data:value="58"
                                                                    data:pathOrig="M 83.82123957893091 197.56252766765306 A 96.48780487804879 96.48780487804879 0 0 1 8.634086897172239 123.12388964524445 L 18.070678207455032 121.11150068072 A 86.8390243902439 86.8390243902439 0 0 0 85.73911562103783 188.10627490088774 L 83.82123957893091 197.56252766765306 z">
                                                                </path>
                                                            </g>
                                                            <g id="SvgjsG1604"
                                                                class="apexcharts-series apexcharts-pie-series"
                                                                seriesName="Cancelled" rel="4" data:realIndex="3">
                                                                <path id="SvgjsPath1605"
                                                                    d="M 8.634086897172239 123.12388964524445 A 96.48780487804879 96.48780487804879 0 0 1 102.98315970125404 6.512196591544509 L 102.98484373112863 16.160976932390057 A 86.8390243902439 86.8390243902439 0 0 0 18.070678207455032 121.11150068072 L 8.634086897172239 123.12388964524445 z"
                                                                    fill="rgba(240,101,72,1)" fill-opacity="1"
                                                                    stroke-opacity="1" stroke-linecap="round"
                                                                    stroke-width="0" stroke-dasharray="0"
                                                                    class="apexcharts-pie-area apexcharts-donut-slice-3"
                                                                    index="0" j="3"
                                                                    data:angle="102.03821656050957"
                                                                    data:startAngle="257.96178343949043"
                                                                    data:strokeWidth="0" data:value="89"
                                                                    data:pathOrig="M 8.634086897172239 123.12388964524445 A 96.48780487804879 96.48780487804879 0 0 1 102.98315970125404 6.512196591544509 L 102.98484373112863 16.160976932390057 A 86.8390243902439 86.8390243902439 0 0 0 18.070678207455032 121.11150068072 L 8.634086897172239 123.12388964524445 z">
                                                                </path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </g>
                                                <line id="SvgjsLine1606" x1="0" y1="0" x2="206"
                                                    y2="0" stroke="#b6b6b6" stroke-dasharray="0"
                                                    stroke-width="1" stroke-linecap="butt"
                                                    class="apexcharts-ycrosshairs"></line>
                                                <line id="SvgjsLine1607" x1="0" y1="0" x2="206"
                                                    y2="0" stroke-dasharray="0" stroke-width="0"
                                                    stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line>
                                            </g>
                                            <g id="SvgjsG1591" class="apexcharts-annotations"></g>
                                        </svg>
                                        <div class="apexcharts-legend"></div>
                                        <div class="apexcharts-tooltip apexcharts-theme-dark">
                                            <div class="apexcharts-tooltip-series-group" style="order: 1;"><span
                                                    class="apexcharts-tooltip-marker"
                                                    style="background-color: rgb(10, 179, 156);"></span>
                                                <div class="apexcharts-tooltip-text"
                                                    style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                                    <div class="apexcharts-tooltip-y-group"><span
                                                            class="apexcharts-tooltip-text-y-label"></span><span
                                                            class="apexcharts-tooltip-text-y-value"></span></div>
                                                    <div class="apexcharts-tooltip-goals-group"><span
                                                            class="apexcharts-tooltip-text-goals-label"></span><span
                                                            class="apexcharts-tooltip-text-goals-value"></span></div>
                                                    <div class="apexcharts-tooltip-z-group"><span
                                                            class="apexcharts-tooltip-text-z-label"></span><span
                                                            class="apexcharts-tooltip-text-z-value"></span></div>
                                                </div>
                                            </div>
                                            <div class="apexcharts-tooltip-series-group" style="order: 2;"><span
                                                    class="apexcharts-tooltip-marker"
                                                    style="background-color: rgb(64, 81, 137);"></span>
                                                <div class="apexcharts-tooltip-text"
                                                    style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                                    <div class="apexcharts-tooltip-y-group"><span
                                                            class="apexcharts-tooltip-text-y-label"></span><span
                                                            class="apexcharts-tooltip-text-y-value"></span></div>
                                                    <div class="apexcharts-tooltip-goals-group"><span
                                                            class="apexcharts-tooltip-text-goals-label"></span><span
                                                            class="apexcharts-tooltip-text-goals-value"></span></div>
                                                    <div class="apexcharts-tooltip-z-group"><span
                                                            class="apexcharts-tooltip-text-z-label"></span><span
                                                            class="apexcharts-tooltip-text-z-value"></span></div>
                                                </div>
                                            </div>
                                            <div class="apexcharts-tooltip-series-group" style="order: 3;"><span
                                                    class="apexcharts-tooltip-marker"
                                                    style="background-color: rgb(247, 184, 75);"></span>
                                                <div class="apexcharts-tooltip-text"
                                                    style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                                    <div class="apexcharts-tooltip-y-group"><span
                                                            class="apexcharts-tooltip-text-y-label"></span><span
                                                            class="apexcharts-tooltip-text-y-value"></span></div>
                                                    <div class="apexcharts-tooltip-goals-group"><span
                                                            class="apexcharts-tooltip-text-goals-label"></span><span
                                                            class="apexcharts-tooltip-text-goals-value"></span></div>
                                                    <div class="apexcharts-tooltip-z-group"><span
                                                            class="apexcharts-tooltip-text-z-label"></span><span
                                                            class="apexcharts-tooltip-text-z-value"></span></div>
                                                </div>
                                            </div>
                                            <div class="apexcharts-tooltip-series-group" style="order: 4;"><span
                                                    class="apexcharts-tooltip-marker"
                                                    style="background-color: rgb(240, 101, 72);"></span>
                                                <div class="apexcharts-tooltip-text"
                                                    style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                                    <div class="apexcharts-tooltip-y-group"><span
                                                            class="apexcharts-tooltip-text-y-label"></span><span
                                                            class="apexcharts-tooltip-text-y-value"></span></div>
                                                    <div class="apexcharts-tooltip-goals-group"><span
                                                            class="apexcharts-tooltip-text-goals-label"></span><span
                                                            class="apexcharts-tooltip-text-goals-value"></span></div>
                                                    <div class="apexcharts-tooltip-z-group"><span
                                                            class="apexcharts-tooltip-text-z-label"></span><span
                                                            class="apexcharts-tooltip-text-z-value"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-center align-items-center mb-4">
                                        <h2 class="me-3 ff-secondary mb-0">258</h2>
                                        <div>
                                            <p class="text-muted mb-0">Total Properties</p>
                                            <p class="text-success fw-medium mb-0">
                                                <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                        class="ri-arrow-right-up-line"></i></span> +3 New
                                            </p>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                            Total</p>
                                        <div>
                                            <span class="text-muted">125 Properties</span>
                                            {{-- <span class="text-success fw-medium fs-12">15870hrs</span> --}}
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>
                                            Signature</p>
                                        <div>
                                            <span class="text-muted">42 Properties</span>
                                            {{-- <span class="text-success fw-medium fs-12">243hrs</span> --}}
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-checkbox-blank-circle-fill text-warning align-middle me-2"></i>Hot
                                        </p>
                                        <div>
                                            <span class="text-muted">58 Properties</span>
                                            {{-- <span class="text-success fw-medium fs-12">~2050hrs</span> --}}
                                        </div>
                                    </div><!-- end -->

                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xxl-4 col-lg-4">
                        <div class="card card-height-100 mb-3">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Dubizzle</h4>
                                <div class="flex-shrink-0">

                                </div>
                            </div>

                            <div class="card-body">
                                <div id="dubizzelePieChart"
                                data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                class="apex-charts" dir="ltr"></div>

                                <div class="mt-3">
                                    <div class="d-flex justify-content-center align-items-center mb-4">
                                        <h2 class="me-3 ff-secondary mb-0">258</h2>
                                        <div>
                                            <p class="text-muted mb-0">Total Properties</p>
                                            <p class="text-success fw-medium mb-0">
                                                <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                        class="ri-arrow-right-up-line"></i></span> +3 New
                                            </p>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                            Total</p>
                                        <div>
                                            <span class="text-muted">125 Properties</span>
                                            {{-- <span class="text-success fw-medium fs-12">15870hrs</span> --}}
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>
                                            Signature</p>
                                        <div>
                                            <span class="text-muted">42 Properties</span>
                                            {{-- <span class="text-success fw-medium fs-12">243hrs</span> --}}
                                        </div>
                                    </div><!-- end -->
                                    <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-checkbox-blank-circle-fill text-warning align-middle me-2"></i>Hot
                                        </p>
                                        <div>
                                            <span class="text-muted">58 Properties</span>
                                            {{-- <span class="text-success fw-medium fs-12">~2050hrs</span> --}}
                                        </div>
                                    </div><!-- end -->

                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
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
                                            Listings</p>
                                    </div>

                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value"
                                                data-target="{{ $propertyCount ?? 0 }}">0</span>
                                        </h4>
                                        <a href="{{ route('admin.listing.index') }}"
                                            class="text-decoration-underline">View all listings</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success rounded fs-3">
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
                                            class="text-decoration-underline">View all contacts</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-info rounded fs-3">
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
                                            class="text-decoration-underline">View all leads</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-warning rounded fs-3">
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
                                            class="text-decoration-underline">View all deals</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-danger rounded fs-3">
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
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 25%" aria-valuenow="25"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 18%" aria-valuenow="18"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 22%" aria-valuenow="22"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 16%" aria-valuenow="16"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 19%" aria-valuenow="19"
                                             aria-valuemin="0" aria-valuemax="100"></div>
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
                                                    <span class="badge badge-soft-dark">{{ $portal['count'] ?? 0 }}</span>
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



            </div> <!-- end .h-100-->

        </div> <!-- end col -->


    </div>
    <div id="app">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 sm:items-center py-4 sm:pt-0">
            <welcome/>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ mix('vue/js/vue.js') }}"></script>
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/swiper/swiper.min.js') }}"></script>
    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
        {{-- <script src=" "></script> --}}



    <script>
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
        dashboardDecider();

        function dashboardDecider() {
            $.ajax({
                url: "{{ route('admin.dashboard.dashboard.decider') }}",
                type: "GET",
                success: function(result) {
                    if (result.status == '1') {
                        let values = [];
                        let labels = [];
                        jQuery.each(result.data.meta.dubizzle.datasets.dubizzle_pie_chart?.label, (index, item) => {
                            labels.push(item);
                        });
                        jQuery.each(result.data.meta.dubizzle.datasets.dubizzle_pie_chart?.data, (index, item) => {
                            values.push(item);
                        });
                        dubizzelePieChart(labels, values);


                    }
                }
            });
        }

        function dubizzelePieChart(label, values) {



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
            var chart = new ApexCharts(document.querySelector("#dubizzelePieChart"), options);
            chart.render();
            console.log(label, values);
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


        $('#DashActive').addClass('active');
    </script>
@endsection
