<html>
<head>
    <title>AX CAPITAL STATS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>


    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/favicon-32x32.png') }}">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"
            type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <style>


        :root {
            --background-color: #000000;
            --white: #ffffff;
            --primary-text: #d3a188;
        }

        body {
            background: var(--background-color);

        }

        .primary-text {
            color: var(--primary-text);


        }
        .row>*{
            padding: 5px !important;
        }

        .goal-text {
            font-size: 1.45vw;
        }

        .main-container {
            width: 90%;
            padding: 10px;
            margin: auto;
/*            display: table;*/
        }

        /*.main-container .first-row {
            height: 100%;
            display: table-cell;
            vertical-align: middle;
        }*/

        /*.container {
            max-width: 1640px !important;
        }*/

        .text-new-gray {
            color: #d9a08c8c;
        }

        .bg-primary-ax {
            background: #d0886c;


        }
        /*.row {
          display: -webkit-box;
          display: -webkit-flex;
          display: -ms-flexbox;
          display:         flex;
        }
        .row > [class*='col-'] {
             display: flex;
             flex-direction: row;
        }*/

        .primary-text-grad {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#d68e6a, #ddcdc6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h2.ff-secondary {
            font-size: 2.8vw;
        }

        h5.primary-text {
            font-size: 1.5vw !important;
        }

        h5.primary-text img {
            max-width: 1.5vw;
            /*            width: 1.5vw !important;*/
        }

        .status-card {
            background: #2d2c34;
            padding: 13px;
            border-radius: 5px;
            color: #d3a188;

        }

        .status-card-text {
            background: -webkit-linear-gradient(#d68e6a, #ddcdc6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 25px;
            vertical-align: middle;
            margin-left: 5px;
        }

        #total_property_week div, #total_property_month div {

            margin: 0 auto;
        }

        .agent-details {
            font-size: 1.1vw;
        }

        .agent-details b {
            font-size: 1.2vw;
            margin-right: 10px;
        }
        .agent-details img{
            width: 1.1vw;
        }

        .status-card-title {
            color: #e9e7e7;
            font-size: 20px;
        }

        .circle-border {
            display: inline-block;
            color: black;
            font: 24px sans-serif;
            background: #53525c;
            border: 4px solid #d3a188;
            width: 40px;
            height: 40px;
            border-radius: 20px;
            /* line-height: 34px; */
            text-align: center;
        }

        .top-headline {
            font-size: 2.1rem !important;
            line-height: 32px;
        }

        .mt-custom-5 {
            margin-top: 6rem !important;
        }

        .portal-card {
            border-width: 1px;
            border-style: solid;
            border-image: linear-gradient(
                to top,
                #d3a188,
                rgba(0, 0, 0, 0)
            ) 1 100%;
        }

        .card {
            background-color: #1d1c1c;
            height: 100%;
        }

        .card-border-wrap-left {

/*            border-radius: 7px;*/
            padding: 1rem;
            position: relative;
            background: linear-gradient(to right, #b67961, #2d252d);
            padding: 1px;
        }
        .card-border-wrap-left.no-bottom-radius{
            border-radius: 7px 7px 0 0;
        }
        .card-border-wrap-left.no-top-radius{
            border-radius: 0 0 7px 7px;
        }
        .card-border-wrap-right {

            border-radius: 7px;
            padding: 1rem;
            position: relative;
            background: linear-gradient(to left, #b67961, #2d252d);
            padding: 1px;
        }

        .gradient-line {
            width: 100%;
            height: 1px;
            background: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(182 121 97), rgba(255, 255, 255, 0));
        }
        .wheel img{
            width: 3.5vw;
            height: auto;
        }
        .wheel span{
            font-size: 1.1vw;
        }
        .beta-badge {
            font-weight: 500;
            background: #d0886c66 !important;
            padding: 3px;
            margin-left: 5px;
            vertical-align: bottom;
            font-size: 11px;
        }

        .first {
            transform: translate(22px, -1px) scale(1.1, 0.8) skew(-12deg, 0deg);
            position: relative;
            z-index: 1;
            color: #ffffff7d;
            font-size: 13px;
            animation-delay: -4s;
        }
        .mt-negative-10{
            margin-top: -10px;
        }

        .wheel {
            margin-top: auto;
            animation: rotateAnimation 2s 1 linear;
        }

        .first::before {

            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* background-color: rgb(44 42 42 / 42%); */
            z-index: 0;
        }

        .bluer-2 {
            -webkit-filter: blur(5px);

        }
        .goal-pill-byline{
            font-size: 1.2vw;
        }
        .mt-custom-3{
            margin-top: 3rem;
        }
        img.bluer-2, img.bluer-8 {
            visibility: hidden;
        }
        .weekly-text{
            font-size: 1.4vw;
        }

        .bluer-8 {
            -webkit-filter: blur(8px);
        }

        .cover {

        }
        small{
            font-size: 50%;
        }

        span.agency_position {
            font-weight: 200;
        }

        .flex-column{
            display: flex;
            flex-direction: column;
        }

        .cover::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgb(44 42 42 / 62%);
            z-index: 0;
        }

        .third {
            transform: rotate(356deg) translate(21px, -1px) scale(1.1, .6) skew(8deg, 7deg);
            position: relative;
            z-index: 1;
            font-size: 13px;
            color: #ffffff7d;
            animation-delay: -4s;
        }

        .third::before {

            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* background-color: rgb(44 42 42 / 42%); */
            z-index: 0;
        }

        @keyframes rotateAnimation {

            /*0%, 100% {*/
            /*    transform: translateZ(0) rotateX(0deg);*/
            /*}*/
            /*25% {*/
            /*    transform: translateZ(-100px) rotateX(90deg);*/
            /*}*/
            /*50% {*/
            /*    transform: translateZ(-200px) rotateX(180deg);*/
            /*}*/
            /*75% {*/
            /*    transform: translateZ(-100px) rotateX(270deg);*/
            /*}*/

        }


    </style>
</head>
<body>

    @php
        $listing_goal = config('goals.listing_goal');
        $pocket_goal = config('goals.pocket_goal');
        $pf_goal =config('goals.pf_goal');
        $bayut_goal = config('goals.bayut_goal');
    @endphp

<div class="main-container">
    <div class="row row-flex">
        <div class="col-lg-12 mb-2 ">
            <img class="float-left" style="" src="https://static.axcapital.ae/assets/public/svg/logo/logo.svg"
                 width="113" height="33" alt="Ax Capital Logo">
            <span class="primary-text-grad top-headline fs-5"
                  style="vertical-align: bottom"> Secondary Listing Stats</span><span
                class="badge bg-primary-ax beta-badge text-white  text-white  mb-1"
                style="background: #d0886c66 !important;">
         Beta
         </span>
        </div>
    </div>
    <div class="row row-flex">
        <div class="col-md-6">
            <div class="card-border-wrap-left no-bottom-radius">
                <div class="card card-animate">
                    <div class="card-body mb-2">
                        <div class="row row-flex">
                            <div class="col-md-7">
                                <h5 class="fw-medium primary-text mb-0">Published Listings</h5>
                                <h2 class="ff-secondary fw-semibold mt-3 mt-custom-5 ">
                                    <span
                                        class="counter-value primary-text-grad" id="totalPublishedProperty">0</span>
                                    <small class="primary-text">Total</small>
                                </h2>
                                <p class="mb-0 primary-text goal-pill-byline">
                                    {{-- <span class="badge bg-primary-ax text-white mb-0" id="totalPercentage">
                                    </span> --}}
                                </p>
                            </div>
                            <div class="col-md-5">
                                <p class="primary-text goal-text text-center">
                                    Goal {{isset($listing_goal) ? $listing_goal : "0"}}
                                    <small >This Year</small>
                                </p>
                                <div id="total_property" data-colors="[&quot;--vz-success&quot;]"
                                     class="apex-charts" dir="ltr">
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end card body -->
                    <!-- end card-->
                </div>
            </div>
        </div>
        <!-- end col-->
        <div class="col-6">
            <div class="card-border-wrap-right">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="row row-flex">
                            <div class="col-md-7">
                                <h5 class="fw-medium primary-text mb-0"><img
                                        src="https://www.propertyfinder.ae/dist/common/assets/favicon/favicon.ico">Property Finder <small
                                    >Verified</small>
                                </h5>
                                <h2 class=" ff-secondary fw-semibold mt-3 mt-custom-5"><span
                                        class="counter-value primary-text-grad"
                                        id="totalPf">0</span>
                                        <small class="primary-text">Total</small>
                                </h2>
                                <p class="mb-0 primary-text goal-pill-byline mt-2 d-none">
                                    <span
                                        class="badge bg-primary-ax text-white mb-0" id="totalPfCompare">
                                    </span> This Month
                                </p>
                            </div>
                            <div class="col-md-5">
                                <div class="avatar-sm flex-shrink-0">
                                    <p class="primary-text goal-text text-center">Goal <span
                                            id="pfGoalCount"></span>
                                        <small >This Year</small>
                                    </p>
                                    <div id="pfVerifiedGraph" data-colors="[&quot;--vz-success&quot;]"
                                         class="apex-charts" dir="ltr">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row primary-text-grad">
                            <div class="col-12 ">
                        <span class="agent-details">
                        <img src="{{asset('assets/images/users/user-theme-primary.png')}}">Total Agents:
                        <b>
                        <span id="pfTotalAgent"></span>
                        </b>
                        </span>
                                <span class="agent-details">
                        <img src="{{asset('assets/images/users/user-theme-primary.png')}}">    Super Agents:
                        <b>
                        <span id="pfSuperAgent"></span>
                        </b>
                        </span>
                                <span class="agent-details">
                        <img src="{{asset('assets/images/users/user-theme-primary.png')}}">Verified Agents:
                        <b>
                        <span id="pfVerifiedAgent"></span>
                        </b>
                        </span>
                            </div>
                        </div>
                    </div>
                    <!-- end card body -->
                </div>
            </div>
        </div>
        <!-- end col-->
    </div>
    <div class="row row-flex">
        <div class="col-6 mt-negative-10">
            <div class="card-border-wrap-left no-top-radius">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-7">
                                <h5 class="fw-medium primary-text mb-0">Published in {{date('F')}}</h5>
                                {{-- <h2 class="ff-secondary fw-semibold mt-3 mt-custom-5 ">
                                    <span
                                        class="counter-value primary-text-grad" id="totalPublishedPropertyThisMonth">0</span>
                                    <small class="primary-text">Total</small>
                                </h2> --}}

                            </div>
                            <div class="col-5 text-center">
                                <p class="primary-text goal-text text-center">
                                    Goal <span id="monthGoal">0</span>
                                    <small >This Month</small>
                                </p>
                                {{-- <div id="total_property_month" data-colors="[&quot;--vz-success&quot;]"
                                     class="apex-charts" dir="ltr">
                                </div> --}}
                            </div>
                            <div class="col-lg-4">
                            </div>
                            <div class="col-lg-4">
                                <div id="total_property_month" data-colors="[&quot;--vz-success&quot;]"
                                     class="apex-charts" dir="ltr">
                                </div>
                            </div>
                            <div class="col-lg-4">
                            </div>

                            <div class=" text-center d-none">
                                <div class=" primary-text weekly-text" >This Week</div>
                                <div id="total_property_week" data-colors="[&quot;--vz-success&quot;]"
                                     class="apex-charts" dir="ltr">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-border-wrap-right">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="row row-flex" style="height: 100%;">
                            <div class="col-md-7 flex-column">
                                <h5 class="fw-medium primary-text mb-0">
                                    <img src="/bayutlogo.png" class="portal_icon_image">
                                    Bayut <small>TruCheck</small>
                                </h5>
                                <h2 class="ff-secondary fw-semibold mt-custom-3"><span
                                        class="counter-value primary-text-grad"
                                        id="totalBayut">0</span>
                                    <small class="primary-text">Total</small>
                                </h2>
                                <div class="wheel">
                                    <div class="first ">
                                    </div>
                                    <div class="second ">
                                    </div>
                                    <div class="third">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="avatar-sm flex-shrink-0">
                                    <p class="mb-0 mt-2 primary-text goal-text text-center">Goal <span
                                            id="bayutGoalCount"></span>
                                        <small >This Year</small>
                                    </p>
                                    <div id="total_verified_bayut"
                                         data-colors="[&quot;--vz-success&quot;]" class="apex-charts"
                                         dir="ltr">
                                    </div>
                                    <p class="mb-0 primary-text d-none">
                                        <span
                                            class="badge bg-primary-ax text-white mb-0" id="totalBayutCompare">
                                        </span> vs. previous
                                        month
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                        <!-- end card body -->
                </div>
            </div>
        </div>
        <!-- end col-->
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.3/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"
        referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.43/moment-timezone.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.43/moment-timezone-with-data.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/js/all.min.js"
        integrity="sha512-LW9+kKj/cBGHqnI4ok24dUWNR/e8sUD8RLzak1mNw5Ja2JYCmTXJTF5VpgFSw+VoBfpMvPScCo2DnKTIUjrzYw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    const listingGoal = "{{isset($listing_goal) ? $listing_goal : "0"}}";
    const pocketGoal = "{{isset($pocket_goal) ? $pocket_goal : "0"}}";
    const pfGoal = "{{isset($pf_goal) ? $pf_goal : "0"}}";
    const bayutGoal = "{{isset($bayut_goal) ? $bayut_goal : "0"}}";

    function cardChart(element, data, goal, width = 250, label = '', lableFontSize = "16px") {
        let percentage = (data / goal) * 100;
        percentage = percentage.toFixed(2);
        let options = {
            series: [percentage],
            chart: {
                type: "radialBar",
                width: "100%",
                sparkline: {
                    enabled: !0
                }
            },
            dataLabels: {
                enabled: !1
            },
            plotOptions: {
                radialBar: {
                    backgroundColor: "#fff",
                    hollow: {
                        margin: 0,
                        size: "65%"
                    },
                    track: {
                        margin: 1,
                        background: '#212020',
                    },
                    dataLabels: {
                        show: !0,
                        name: {
                            show: label != '' ? 1 : 0,
                        },
                        value: {
                            show: !0,
                            fontSize: lableFontSize,
                            fontWeight: 500,
                            offsetY: 10,
                            color: "#ffffff"
                        },
                        total: {
                            color: "#d3a188",
                            show: !0,
                            label: label,
                            formatter: function (e) {
                                return data
                            }
                        }
                    },

                }
            },
            colors: ["#d0886c"],
            //change the bar background color


        }
        let chart = new ApexCharts(document.querySelector(element), options);
        chart.render()
    }

    function cardChartPercentage(element, data, goal, width = 250, label = '', lableFontSize = "16px", counterFontSize = "20px") {
        let percentage = (data / goal) * 100;
        percentage = percentage.toFixed(0);
        let options = {
            series: [percentage],
            chart: {
                type: "radialBar",
                width: "100%",
                sparkline: {
                    enabled: !0
                }
            },
            dataLabels: {
                enabled: !1
            },
            plotOptions: {
                radialBar: {

                    backgroundColor: "#fff",
                    hollow: {
                        margin: 0,
                        size: "65%"
                    },
                    track: {
                        margin: 1,
                        background: '#212020',
                    },
                    dataLabels: {
                        show: !0,
                        name: {
                            show: label != '' ? 1 : 0,
                        },
                        value: {
                            show: !0,
                            fontSize: counterFontSize,
                            fontWeight: 500,
                            offsetY: 30,
                            // style: {
                            //     margin: `20px 0 0 0` // Adjust the margin as needed
                            // },
                            color: "#ffffff"
                        },
                        total: {
                            color: "#d3a188",
                            show: !0,
                            label: label,
                            fontWeight: 500,
                            style: {
                                margin: `40px 0 0 0` // Adjust the margin as needed
                            },
                            fontSize: lableFontSize,
                            formatter: function (e) {
                                return data;
                            }
                        }
                    },

                }
            },
            colors: ["#d0886c"],
            //change the bar background color


        }
        let chart = new ApexCharts(document.querySelector(element), options);
        chart.render()
    }


    function drawRadialBar(element, current, total, chartHeight = 180) {
        let target = total;
        let percentage = (current / target) * 100;
        percentage = Math.round(percentage);

        var options = {
            series: [percentage],
            chart: {
                height: chartHeight,
                type: 'radialBar',
                offsetY: -10
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    track: {
                        background: "#232222",
                        strokeWidth: "97%",
                        margin: 5,

                    },
                    dataLabels: {
                        name: {
                            fontSize: '16px',
                            color: undefined,
                            offsetY: 120
                        },
                        value: {
                            offsetY: 1,
                            fontSize: '16px',
                            color: "white",
                            formatter: function (val) {
                                return val + "%";
                            }
                        },
                        total: {
                            color: "#d3a188",
                            show: !1,
                            label: total,
                            offsetY: 1,
                            fontSize: '16px',
                            formatter: function (e) {
                                return current + '%';
                            }
                        }
                    }
                }
            },

            colors: ['#d3a188'],
            labels: [''],
        };

        var chart = new ApexCharts(document.querySelector(element), options);
        chart.render();

    }


    function getData() {
        $.ajax({
            url: "{{route('front.api.screen3')}}",
            type: "get",
            data: {
                'dd': '1'

            },
            success: function (result) {
                if (result.data) {
                    let data = result.data;
                    let pfGoalCount = (data.pf_count / 100) * pfGoal;
                    let previousMonth = data.previousMonthPublished;
                    let currentMonth = data.thisMonthPublished;
                    //calculate percentage of how much it is up or down compared to previous month and show it in the card
                    let upOrDown = ((currentMonth - previousMonth) / previousMonth) * 100;
                    upOrDown = upOrDown.toFixed(0);
                    let monthTarget = listingGoal / 12;
                    upOrDown = ((currentMonth - monthTarget) / monthTarget) * 100;
                    upOrDown = upOrDown.toFixed(0);
                    if (upOrDown > 0) {
                        $("#totalCompare").html(upOrDown + '% up')
                    } else {
                        $("#totalCompare").html(upOrDown + '% down')
                    }
                    //pf card info
                    let pfPrevMonth = data.pf_count_pre_month;
                    let pfCurrentMonth = data.pf_count_current_month;
                    let pfMonthTarget = pfGoalCount / 12;
                    upOrDown = ((pfCurrentMonth - pfMonthTarget) / pfMonthTarget) * 100;

                    upOrDown = upOrDown.toFixed(0);
                    if (upOrDown > 0) {
                        $("#totalPfCompare").html(upOrDown + '% up')
                    } else {
                        $("#totalPfCompare").html(upOrDown + '% down')
                    }
                    //bayut card info
                    previousMonth = data.bayut_count_pre_month;
                    currentMonth = data.bayut_count_current_month;
                    upOrDown = ((currentMonth - previousMonth) / previousMonth) * 100;
                    upOrDown = upOrDown.toFixed(0);
                    if (upOrDown > 0) {
                        $("#totalBayutCompare").html(upOrDown + '% up')
                    } else {
                        $("#totalBayutCompare").html(upOrDown + '% down')

                    }
                    $("#totalPf").html(data.pf_count)
                    $("#totalBayut").html(data.bayut_count)
                    $("#totalPublishedProperty").html(data.totalPropertyCount)
                    $("#totalPocketProperty").html(data.pf_count)
                    let totalPercentage = (data.totalPropertyCount / listingGoal) * 100;
                    $("#totalPercentage").html(totalPercentage.toFixed(0) + '%')
                    cardChart('#total_property', data.totalPropertyCount, listingGoal, 500, '', '2vw')
                    $("#pfGoalCount").html(pfGoalCount.toFixed(0))

                    cardChartPercentage('#pfVerifiedGraph', data.pf_verified_count, pfGoalCount.toFixed(0), 500, 'Verified', '1.2vw', "2vw")
                    let bayutGoalCount = (data.bayut_count / 100) * bayutGoal;
                    cardChartPercentage('#total_verified_bayut', data.bayut_trucheck_count, bayutGoalCount, 500, 'TruCheck', '1.2vw', "2vw")
                    $("#bayutGoalCount").html(bayutGoalCount.toFixed(0))
                    //get total days in this month
                    let daysInMonth = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getDate();

                    //get total weeks in this month
                    let weekTarget = listingGoal / 4;
                    weekTarget = weekTarget.toFixed(0);

                    // cardChart('#total_property_today', data.todayPublished, dayTarget, 150)
                    //  cardChart('#total_property_week', data.thisWeekPublished, weekTarget, 150)

                   // cardChart('#total_property_week', data.thisWeekPublished, weekTarget, 350, '', '1.7vw')
                   $("#totalPublishedPropertyThisMonth").html(data.thisMonthPublished)
                   $("#monthGoal").html(monthTarget.toFixed(0))
                    cardChart('#total_property_month', data.thisMonthPublished, monthTarget.toFixed(0), 350, '', '1.7vw')
                    // previousMonth = data.pocketPreviousMonth;
                    // currentMonth = data.pocketCurrentMonth;
                    // upOrDown = ((currentMonth - previousMonth) / previousMonth) * 100;
                    // if (upOrDown > 0) {
                    //     $("#totalPocketCompare").html(upOrDown+'% up')
                    // } else {
                    //     $("#totalPocketCompare").html(upOrDown+'% down')
                    // }
                    // let dayTargetPocket = pocketGoal / daysInMonth;
                    // dayTargetPocket = dayTargetPocket.toFixed(0);
                    // //get total weeks in this month
                    // let weekTargetPocket = daysInMonth/4;
                    // weekTargetPocket = weekTargetPocket.toFixed(0);
                    // cardChart('#totalPocketPropertyGraph', data.totalPocketCount, pocketGoal,150,'','14px')
                    // cardChart('#pocket_today', data.todayPocket, dayTargetPocket, 70,'','10px')
                    // cardChart('#pocket_week', data.thisWeekPocket, weekTargetPocket, 70,'','10px')
                }
            }
        });
    }

    getData()

    function getPfData() {
        $.ajax({
            url: "{{route('front.api.pf.agents.status')}}",
            type: "get",
            data: {
                'dd': '1'

            },
            success: function (result) {
                if (result.data) {
                    $("#pfTotalAgent").html(result.data.total_agents);
                    $("#pfSuperAgent").html(result.data.total_super_agents);
                    $("#pfVerifiedAgent").html(result.data.total_verified_agents);
                    $("#pfOtherAgent").html(result.data.total_other_agents);


                }
            }
        });
    }

    function getBayutData() {
        $.ajax({

            url: "{{route('front.api.bayut.agency.status')}}",
            type: "get",
            data: {
                'dd': '1'

            },
            success: function (result) {
                if (result.data) {
                    let count = 0;
                    let name = [];
                    let value = [];
                    // let agency = result.data.agency_name;
                    // let total = result.data.agency_count;
                    let data = result.data;
                    $.each(data, function (key, value) {

                        if (value.name === 'AX CAPITAL Real Estate') {
                                if(count>0) {

                                    $(".first").html(`
                                                            <img class="bluer-8" alt="${data[count - 1]}" src="${data[count - 1]?.logo}">
                                                            <span class="primary-text"><span class="agency_position">#${count}</span> <b><span class="bluer-21">**** *** **** ***</span></b></span>  <span class="badge bg-primary-ax text-white mb-0" style="background: #656463;">${data[count - 1].count}</span>
                                                      `)
                                }
                            $(".second").html(`
                                                            <img  alt="AX CAPITAL Real Estate" src="${data[count].logo}">
                                                            <span class="primary-text-grad" padding-top:10px;"><span class="agency_position">#${count + 1}</span> <b>${data[count].name}</b></span>  <span class="badge bg-primary-ax text-white mb-0" style="background: #656463;">${data[count].count}</span>
                                                        `)
                            if(data[count + 1] !=undefined) {
                                $(".third").html(`
                                                            <img class="bluer-2" alt="${data[count + 1].name}" src="${data[count + 1].logo}">
                                                            <span class="primary-text"><span class="agency_position">#${count + 2}</span> <b><span class="bluer-21">**** *** **** ***</span></b></span>  <span class="badge bg-primary-ax text-white mb-0" style="background: #656463;">${data[count + 1].count}</span>
                                                       `)
                            }
                        }
                        count++;

                    });
                }
            }
        });
    }

    getBayutData()
    getPfData()
</script>

</body>
</html>
{{-- <div class="main-container">
    <div class="row">
        <div class="col-6">
            <div class="card-border-wrap-left" >
                <div class="card card-animate">
                    <div class="card-body mb-2">
                        <div class="half-width d-flex justify-content-between">
                            <h5 class="fw-medium primary-text mb-0" >Published Listings</h5>
                            <h2 class="ff-secondary fw-semibold mt-3 mt-custom-5 " ><span
                                    class="counter-value primary-text-grad" id="totalPublishedProperty">0</span>
                                <span class="primary-text" style="font-size:1vw;">Total</span>
                            </h2>
                            <p class="mb-0 primary-text " ><span class="badge bg-primary-ax text-white mb-0" id="totalCompare">
                                </span> This Month</p>
                        </div>
                        <div class="half-width d-flex justify-content-between">
                            <div class=" flex-shrink-0">
                                    <p class="mb-0 mt-2 primary-text goal-text text-center">Goal {{isset($listing_goal) ? $listing_goal : "0"}} <span >This Year</span></p>

                                <div id="total_property" data-colors="[&quot;--vz-success&quot;]"
                                     class="apex-charts" dir="ltr" >
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="col-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="fw-medium primary-text mb-0"> <img src="https://www.propertyfinder.ae/dist/common/assets/favicon/favicon.ico" alt="" width="30px">Property Finder <span style="font-size:.7vw;">Verified</span></h5>
                            <h2 class=" ff-secondary fw-semibold mt-3 mt-custom-5" ><span
                                    class="counter-value primary-text-grad"
                                     id="totalPf">0</span> <span class="primary-text" style="font-size:1vw;">Total</span></h2>


                            <p class="mb-0 primary-text mt-2" ><span
                                    class="badge bg-primary-ax text-white mb-0" id="totalPfCompare">
                        </span> This Month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <p class="mb-0 mt-2 primary-text goal-text text-center">Goal <span id="pfGoalCount"></span></p>

                                <div id="pfVerifiedGraph" data-colors="[&quot;--vz-success&quot;]"
                                     class="apex-charts" dir="ltr" >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row primary-text-grad">
                        <div class="col-12 ">
                            <span class="agent-details">
                                <img src="{{asset('assets/images/users/user-theme-primary.png')}}" >Total Agents:
                                <b>
                                    <span id="pfTotalAgent"></span>
                                </b>
                            </span>
                            <span class="agent-details" >
                                <img src="{{asset('assets/images/users/user-theme-primary.png')}}">    Super Agents:
                                <b>
                                    <span id="pfSuperAgent"></span>
                                </b>
                            </span>
                            <span class="agent-details">
                                <img src="{{asset('assets/images/users/user-theme-primary.png')}}" >Verified Agents:
                                <b>
                                    <span id="pfVerifiedAgent"></span>
                                </b>
                            </span>
                        </div>

                    </div>
                </div><!-- end card body -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6"></div>
        <div class="col-6"></div>
    </div>
</div> --}}
