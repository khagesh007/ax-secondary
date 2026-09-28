<html>

<head>
    <title>Banner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
        integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/favicon-32x32.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <style>
        :root {
            --background-color: #111010;
            --white: #ffffff;
            --primary-text: #d3a188;
        }

        body {
            background: var(--background-color);

        }

        .primary-text {
            color: var(--primary-text);


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



        .goal-text {
            font-size: 22px;
        }

        .main-container {
            height: 100vh;
            display: table;
        }

        .main-container .first-row {
            height: 100%;
            display: table-cell;
            vertical-align: middle;
        }

        .container {
            max-width: 1640px !important;
        }

        .text-new-gray {
            color: #d9a08c8c;
        }

        .bg-primary-ax {
            background: #d0886c;


        }

        .primary-text-grad {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#d68e6a, #ddcdc6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h2 .counter-value {
            font-size: 3.5vw !important;
        }

        h5.primary-text {
            font-size: 32px !important;
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

        #total_property_week div,
        #total_property_month div {

            margin: 0 auto;
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
            border-image: linear-gradient(to top,
                    #d3a188,
                    rgba(0, 0, 0, 0)) 1 100%;
        }

        .card {
            background-color: #2c2a2a;
        }

        .card-border-wrap-left {

            border-radius: 7px;
            padding: 1rem;
            position: relative;
            background: linear-gradient(to right, #b67961, #2d252d);
            padding: 1px;
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

        .wheel {

            animation: rotateAnimation 2s 1 linear;
        }

        .first::before {

            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgb(44 42 42 / 42%);
            z-index: 0;
        }

        .bluer-2 {
            -webkit-filter: blur(5px);

        }

        img.bluer-2,
        img.bluer-8 {
            visibility: hidden;
        }

        .bluer-8 {
            -webkit-filter: blur(8px);
        }

        .cover {}

        span.agency_position {
            font-size: 30px;
            font-weight: 200;
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
            /* font-size: 13px; */
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
            background-color: rgb(44 42 42 / 42%);
            z-index: 0;
        }




        /* Mobile Phones */
        @media only screen and (max-width: 480px) {
            #primaryGrid {
                height: 65px;
            }

            #totalPublishedProperty {
                font-size: 3.5vw !important;
            }

            #totalPublishedPropertyTotal {
                font-size: 1vw !important;
            }

            #thisYear {
                font-size: 1vw;
            }

            #total_property {
                min-height: 88.5px;
                margin-right: 22px;
            }

            #hiddenDiv {
                visibility: hidden;
                height: 110px;
            }

            .agent_icon {
                width: 18px;
                vertical-align: baseline;
            }

            #agent_data {
                font-size: 15px;
            }

            #trucheckDiv {
                margin-top: 0.7rem !important;
            }

            #bayutTrucheck {
                font-size: .7vw;
            }


        }

        /* Portrait Tablets and Small Screens */
        @media only screen and (min-width: 481px) and (max-width: 767px) {
            #primaryGrid {
                height: 65px;
            }

            #totalPublishedProperty {
                font-size: 3.5vw !important;
            }

            #totalPublishedPropertyTotal {
                font-size: 1vw !important;
            }

            #thisYear {
                font-size: 1vw;
            }

            #total_property {
                min-height: 88.5px;
                margin-right: 22px;
            }

            #hiddenDiv {
                visibility: hidden;
                height: 110px;
            }

            .agent_icon {
                width: 18px;
                vertical-align: baseline;
            }

            #agent_data {
                font-size: 15px;
            }

            #trucheckDiv {
                margin-top: 0.7rem !important;
            }

            #bayutTrucheck {
                font-size: .7vw;
            }


        }

        /* Landscape Tablets and Medium Screens */
        @media only screen and (min-width: 768px) and (max-width: 1023px) {
            #primaryGrid {
                height: 65px;
            }

            #totalPublishedProperty {
                font-size: 3.5vw !important;
            }

            #totalPublishedPropertyTotal {
                font-size: 1vw !important;
            }

            #thisYear {
                font-size: 1vw;
            }

            #total_property {
                min-height: 88.5px;
                margin-right: 22px;
            }

            #hiddenDiv {
                visibility: hidden;
                height: 110px;
            }

            .agent_icon {
                width: 18px;
                vertical-align: baseline;
            }

            #agent_data {
                font-size: 15px;
            }

            #trucheckDiv {
                margin-top: 0.7rem !important;
            }

            #bayutTrucheck {
                font-size: .7vw;
            }


        }

        /* Desktops and Large Screens */
        @media only screen and (min-width: 1024px) and (max-width: 1365px) {
            #primaryGrid {
                height: 65px;
            }

            #totalPublishedProperty {
                font-size: 1.5vw !important;
            }

            #totalPublishedPropertyTotal {
                font-size: 1vw !important;
            }

            #thisYear {
                font-size: 1vw;
            }

            #total_property {
                min-height: 88.5px;
                margin-right: 22px;
            }

            #hiddenDiv {
                visibility: hidden;
                height: 115px;
            }

            .agent_icon {
                width: 18px;
                vertical-align: baseline;
            }

            #agent_data {
                font-size: 15px;
            }

            #trucheckDiv {
                margin-top: 0.7rem !important;
            }

            #bayutTrucheck {
                font-size: .7vw;
            }

            .published_listing {
                font-size: 1.4rem !important;
            }

        }

        /* Large Desktops and Extra-Large Screens */
        @media only screen and (min-width: 1366px) and (max-width: 1919px) {
            .totalPublishedPropertyTotal {
                font-size: 1vw !important;
            }
            .pf-icon{
                width: 50px;
            }
            h2{
                font-size: 1.8vw !important;
            }
            .monthlyVariation{
                font-size: 1vw !important;
            }
            .thisYear{
                font-size: 0.8vw !important;
            }
            .agent-details {
                font-size: 20px;
            }

            .agent-details b {
                font-size: 20px;
                margin-right: 15px;
            }
            .published_listing{
                font-size: 20px!important;
            }
            .agent_icon{
                width: 20px;
            }
            .page-title{
                font-size: 20px!important;
            }
            .title-beta{
                font-size: 10px!important;
                margin-bottom: 5px!important;
            }

            .agency_style{
                font-size: 10px!important;
            }
            .agency_style img{
                width: 30px;
                height: 30px;
            }

            .agency_position{
                font-size: 15px!important;
            }
            .marginTop{
                margin-top: 50px!important;
            }
            .agency_style{
                font-size: 15px!important;
            }
            .top-name{
                margin-bottom: 5px!important;
            }
            .marginTop5{
                /* margin-top: 1rem!important; */
            }
        }

        /* 2K and Widescreen Monitors */
        @media only screen and (min-width: 1920px) and (max-width: 2560px) {

            .pf-icon{
                width: 30px;
            }
            .totalPublishedPropertyTotal {
                font-size: 1vw !important;
            }
            .monthlyVariation{
                font-size: 1.3vw !important;
            }
            h2{
                font-size: 2.4vw !important;
            }

            .thisYear{
                font-size: 1.0vw !important;
            }

            .agent-details {
                font-size: 20px;
            }

            .agent-details b {
                font-size: 20px;
                margin-right: 15px;
            }

            .published_listing{
                font-size: 35px!important;
            }

            .agent_icon{
                width: 20px;
            }
            .page-title{
                font-size: 35px!important;
            }
            .title-beta{
                font-size: 25px!important;
            }
            .agency_style img{
                width: 30px;
                height: 30px;
            }
            .agency_style{
                font-size: 20px!important;
            }
            .first img{
                width: 60px;
                height: 60px;
            }
            .second img{
                width: 60px;
                height: 60px;
            }
            .third img{
                width: 60px;
                height: 60px;
            }
            .agency_position{
                font-size: 35px!important;
            }
            .marginTop{
                margin-top: 45px!important;
            }
            .top-name{
                margin-bottom: 10px!important;
            }
            .marginTop5{
                margin-top: 3.2rem!important;
            }
        }

        /* 4K and Ultra-Widescreen Monitors */
        @media only screen and (min-width: 2561px) {

            .pf-icon{
                width: 70px;
            }
            .totalPublishedPropertyTotal {
                font-size: 1.4vw !important;
            }
            .monthlyVariation{
                font-size: 1.5vw !important;
            }
            .thisYear{
                font-size: 1.2vw !important;
            }
            h2{
                font-size: 1.6vw !important;
            }

            .agent-details {
                font-size: 50px;
            }

            .agent-details b {
                font-size: 50px;
                margin-right: 15px;
            }
            .published_listing{
                font-size: 80px!important;
            }
            .agent_icon{
                width: 50px;
            }
            .first{
                font-size: 40px;
            }
            .third{
                font-size: 40px;
            }
            .second{
                font-size: 40px;
            }

            .page-title{
                font-size: 60px!important;
            }
            .title-beta{
                font-size: 25px!important;
            }
            .first img{
                width: 120px;
                height: 100px;
            }
            .second img{
                width: 120px;
                height: 100px;
            }
            .third img{
                width: 120px;
                height: 100px;
            }
            .bluer-21{
               font-size:60px;
            }

            .agency_position{
                font-size: 60px!important;
            }
            .marginTop{
                margin-top: 100px!important;
            }
            .top-name{
                margin-bottom: 30px!important;
            }
            .marginTop5{
                margin-top: 5rem!important;
            }

        }
    </style>
</head>

<body style="padding: 0 5%;">

    <div class="main-container">

        <div class="row mt-5 first-row">

            <div class="col-12">
                <div class="row">
                    <div class="col-lg-12 top-name">
                        <img class="float-left" style=""
                            src="https://static.axcapital.ae/assets/public/svg/logo/logo.svg" width="113" height="40" alt="AX CAPITAL Logo">
                        <span class="primary-text-grad top-headline page-title" style="vertical-align: bottom"> Secondary
                            Listing Stats</span> <span style="vertical-align: bottom" class="badge bg-primary-ax beta-badge text-white  text-white  msb-1 title-beta" style="background: #d0886c66 !important;">
                            Beta
                        </span>
                    </div>

                    <div class="col-12 mt-2">
                    </div>

                    <div class="col-md-6 d-flex flex-columm">
                            <div class="card-border-wrap-left flex-fill">
                                <div class="card card-animate ">
                                    <div class="card-body mb-2">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h3 class="fw-medium primary-text mb-0 published_listing">Published Listings
                                                </h3>
                                                <h2 class="ff-secondary fw-semibold mt-3 mt-custom-5 "><span
                                                        class="counter-value primary-text-grad totalCounts"
                                                        id="totalPublishedProperty">0</span>
                                                    <span class="primary-text totalPublishedPropertyTotal" id="">Total</span>
                                                </h2>
                                                <p class="mb-0 primary-text monthlyVariation"><span
                                                        class="badge bg-primary-ax text-white mb-0" id="totalCompare">
                                                    </span> This Month</p>


                                            </div>
                                            <div>
                                                <div class=" flex-shrink-0">
                                                    <p class="mb-0 mt-2 primary-text goal-text text-center monthlyVariation">Goal
                                                        {{ isset($_GET['listing_goal']) ? $_GET['listing_goal'] : '0' }}
                                                        <span id="thisYear" class="thisYear">&nbsp;This Year</span>
                                                    </p>

                                                    <div id="total_property" data-colors="[&quot;--vz-success&quot;]"
                                                        class="apex-charts" dir="ltr">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row primary-text-grad " id="primaryGrid">
                                            &nbsp;
                                        </div>
                                        <div class="gradient-line mt-2"></div>
                                        <div class="row">
                                            <div class="col-md-1 mt-4">
                                            </div>
                                            <div class="col-md-5 mt-5">
                                                <div class="primary-text monthlyVariation" style="text-align: center;">This Week</div>
                                                <div id="total_property_week" data-colors="[&quot;--vz-success&quot;]"
                                                    class="apex-charts" dir="ltr">
                                                </div>
                                            </div>
                                            <div class="col-5 mt-5">
                                                <div class=" primary-text monthlyVariation" style="text-align:center;">This Month</div>
                                                <div id="total_property_month" data-colors="[&quot;--vz-success&quot;]"
                                                    class="apex-charts" dir="ltr">
                                                </div>

                                            </div>
                                            <div class="col-1 mt-5">
                                            </div>

                                        </div>

                                        <div style="" id="hiddenDiv">
                                            &nbsp;
                                        </div>

                                    </div><!-- end card body -->
                                </div> <!-- end card-->
                            </div>
                    </div>

                    <div class="col-md-6 d-flex flex-column ">
                        <div class="row flex-fill">
                            <div class="col-md-12">
                                <div class="card-border-wrap-right">
                                    <div class="card card-animate">

                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="fw-medium primary-text mb-0 published_listing"> <img class="pf-icon"
                                                            src="https://www.propertyfinder.ae/dist/common/assets/favicon/favicon.ico"
                                                            alt="">Property Finder <span
                                                            class="thisYear">Verified</span></h4>
                                                    <h2 class=" ff-secondary fw-semibold mt-3 mt-custom-5"><span
                                                            class="counter-value primary-text-grad"
                                                            id="totalPf">0</span> <span class="primary-text totalPublishedPropertyTotal"
                                                            >Total</span></h2>


                                                    <p class="mb-0 primary-text mt-2 monthlyVariation"><span
                                                            class="badge bg-primary-ax text-white mb-0"
                                                            id="totalPfCompare">
                                                        </span> This Month</p>
                                                </div>
                                                <div>
                                                    <div class="avatar-sm flex-shrink-0 ">
                                                        <p class="mb-0 mt-2 primary-text goal-text text-center monthlyVariation">Goal
                                                            <span id="pfGoalCount"></span>
                                                        </p>

                                                        <div id="pfVerifiedGraph"
                                                            data-colors="[&quot;--vz-success&quot;]"
                                                            class="apex-charts" dir="ltr">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row primary-text-grad mt-2" style="" id="agent_data">
                                                <div class="col-12 "><span class="agent-details">
                                                        <img class="agent_icon"
                                                            src="{{ asset('assets/images/users/user-theme-primary.png') }}"
                                                            style="">Total
                                                        Agents:<b> <span id="pfTotalAgent"></span></b></span> <span
                                                        class="agent-details" style="margin-left: 7px;">
                                                        <img class="agent_icon"
                                                            src="{{ asset('assets/images/users/user-theme-primary.png') }}"
                                                            style="">Super
                                                        Agents: <b><span id="pfSuperAgent"></span></b></span> <span
                                                        class="agent-details" style="margin-left: 7px;">
                                                        <img class="agent_icon"
                                                            src="{{ asset('assets/images/users/user-theme-primary.png') }}"
                                                            style="">Verified
                                                        Agents: <b><span id="pfVerifiedAgent"></span></b></span>
                                                </div>

                                            </div>
                                        </div><!-- end card body -->
                                    </div>
                                </div> <!-- end card-->
                            </div> <!-- end col-->

                            <div class="col-md-12" style="margin-top: 0.7rem !important;">
                                <div class="card-border-wrap-right">
                                    <div class="card card-animate" style="padding-bottom: 0!important">
                                        <div class="card-body" style="padding-bottom: 0!important">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="fw-medium primary-text mb-0 published_listing">
                                                        <svg class="pf-icon" viewBox="0 0 22 19"
                                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                                            aria-hidden="true" focusable="false" class="">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M13.2845 9.5037C12.4877 9.5037 11.8397 10.1565 11.8397 10.9485V12.7485C11.8397 13.0413 11.5997 13.2813 11.3069 13.2813H10.8365H10.5341H9.8813H9.3869C9.0269 13.2813 8.7293 12.9837 8.7293 12.6237V11.8989V8.7021C8.7293 7.7325 8.7245 7.6413 9.1037 7.4205L12.4781 5.4717C12.7901 5.2941 13.0061 5.1597 13.2845 5.1597C13.5581 5.1597 13.7789 5.2893 14.0909 5.4717L17.4701 7.4205C17.8493 7.6365 17.8445 7.7325 17.8445 8.7021V11.8941V12.6189C17.8445 12.9789 17.5469 13.2765 17.1869 13.2765H16.6925H16.0397H15.7373H15.2669C14.9741 13.2765 14.7341 13.0365 14.7341 12.7437V10.9437C14.7293 10.1517 14.0765 9.5037 13.2845 9.5037ZM15.5117 1.1853C10.9181 -0.043501 6.1949 2.6829 4.9661 7.2765C4.4717 9.1149 4.6157 10.9725 5.2541 12.6189C5.2397 12.6285 5.2205 12.6333 5.2061 12.6429L0.478102 15.3741C0.118102 15.5805 -0.00669783 16.0461 0.204502 16.4013L0.770902 17.3805C0.977302 17.7405 1.4429 17.8653 1.7981 17.6541L6.5261 14.9229C6.5405 14.9133 6.5597 14.9037 6.5741 14.8941C7.6781 16.2717 9.2189 17.3229 11.0573 17.8173C15.6509 19.0461 20.3741 16.3197 21.6029 11.7261C22.8317 7.1373 20.1053 2.4141 15.5117 1.1853ZM13.2845 16.3293C17.0525 16.3293 20.1101 13.2717 20.1101 9.5037C20.1101 5.7357 17.0525 2.6781 13.2845 2.6781C9.5165 2.6781 6.4589 5.7357 6.4589 9.5037C6.4589 13.2717 9.5117 16.3293 13.2845 16.3293Z"
                                                                fill="#00A766"></path>
                                                        </svg>
                                                        Bayut <span class="thisYear">TruCheck</span>
                                                    </h4>
                                                    <h2 class="ff-secondary fw-semibold mt-3"><span
                                                            class="counter-value primary-text-grad"
                                                            id="totalBayut">0</span> <span class="primary-text totalPublishedPropertyTotal"
                                                            >Total</span></h2>



                                                    <div class="wheel " >

                                                        <div class="first agency_style" >

                                                        </div>

                                                        <div class="second agency_style">

                                                        </div>
                                                        <div class="third agency_style">

                                                        </div>
                                                    </div>



                                                </div>
                                                <div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <p class="mb-0 mt-2 primary-text goal-text text-center monthlyVariation">Goal
                                                            <span id="bayutGoalCount"></span>
                                                        </p>

                                                        <div id="total_verified_bayut"
                                                            data-colors="[&quot;--vz-success&quot;]"
                                                            class="apex-charts" dir="ltr">
                                                        </div>
                                                        <p class="mb-0 primary-text d-none"><span
                                                                class="badge bg-primary-ax text-white mb-0"
                                                                id="totalBayutCompare">
                                                            </span> vs. previous
                                                            month</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end card body -->
                                    </div> <!-- end card-->

                                </div>
                            </div> <!-- end col-->
                        </div>
                    </div>


                </div>
                <div class="row mt-3">
                </div>
            </div>


        </div>

        <div class="col-md-6 d-none">
            <div class="card-border-wrap-right">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="fw-medium primary-text mb-0">Dubizzle</h5>
                                <h2 class="mt-4 ff-secondary fw-semibold"><span
                                        class="counter-value primary-text-grad" data-target="1600">1600</span></h2>
                                <p class="mb-0 primary-text"><span class="badge bg-light text-success mb-0">
                                        <i class="ri-arrow-up-line align-middle"></i> 16.24 % </span> vs. previous
                                    month</p>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">


                                    <div id="total_verified_dubizzle" data-colors="[&quot;--vz-success&quot;]"
                                        class="apex-charts" dir="ltr">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div> <!-- end card-->
            </div>
        </div> <!-- end col-->

    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.3/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" referrerpolicy="no-referrer">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.43/moment-timezone.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.43/moment-timezone-with-data.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/js/all.min.js"
        integrity="sha512-LW9+kKj/cBGHqnI4ok24dUWNR/e8sUD8RLzak1mNw5Ja2JYCmTXJTF5VpgFSw+VoBfpMvPScCo2DnKTIUjrzYw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>



    <script>
        let published_chart_width = 200;
        let published_chart_font_size = 26;


        let property_finder_chart_width = 200;
        let property_finder_chart_font_size = 26;


        let bayut_chart_width = 200;
        let bayut_chart_font_size = 26;


        let weekly_chart_width = 200;
        let weekly_chart_font_size = 26;

        let monthly_chart_width = 200;
        let monthly_chart_font_size = 26;
        var resizeTimeout;
        let count = 0;

        function checkScreenSize() {
                var windowWidth = $(window).width();

                if (windowWidth <= 480) {
                    published_chart_width = 100;
                    published_chart_font_size = 26;
                    property_finder_chart_width = 200;
                    property_finder_chart_font_size = 26;
                    property_finder_chart_verified = 26;
                    bayut_chart_trucheck = 26;

                    bayut_chart_width = 200;
                    bayut_chart_font_size = 26;
                    weekly_chart_width = 350;
                    weekly_chart_font_size = 40;
                    monthly_chart_width = 350;
                    monthly_chart_font_size = 40;
                    counterOffSet = 30;
                        getData()


                } else if (windowWidth <= 767) {
                    published_chart_width = 100;
                    published_chart_font_size = 26;
                    property_finder_chart_width = 200;
                    property_finder_chart_font_size = 26;
                     property_finder_chart_verified = 26;
                    bayut_chart_trucheck = 26;
                    bayut_chart_width = 200;
                    bayut_chart_font_size = 26;
                    weekly_chart_width = 350;
                    weekly_chart_font_size = 40;
                    monthly_chart_width = 350;
                    monthly_chart_font_size = 40;
                    counterOffSet = 30;


                        getData()

                } else if (windowWidth <= 1023) {
                    published_chart_width = 500;
                    published_chart_font_size = 50;
                    property_finder_chart_width = 500;
                    property_finder_chart_font_size = 26;
                    property_finder_chart_verified = 26;
                    bayut_chart_trucheck = 26;
                    bayut_chart_width = 200;
                    bayut_chart_font_size = 26;
                    weekly_chart_width = 350;
                    weekly_chart_font_size = 40;
                    monthly_chart_width = 350;
                    monthly_chart_font_size = 40;
                    counterOffSet = 30;


                        getData()

                } else if (windowWidth <= 1365) {
                    // alert('1365')
                    published_chart_width = 500;
                    published_chart_font_size = 50;
                    property_finder_chart_width = 500;
                    property_finder_chart_font_size = 50;
                    property_finder_chart_verified = 26;
                    bayut_chart_trucheck = 26;
                    bayut_chart_width = 500;
                    bayut_chart_font_size = 50;
                    weekly_chart_width = 350;
                    weekly_chart_font_size = 40;
                    monthly_chart_width = 350;
                    monthly_chart_font_size = 40;
                    counterOffSet = 30;


                        getData()

                } else if (windowWidth <= 1919) {
                    // alert('1919')
                    published_chart_width = 250;
                    published_chart_font_size = 25;
                    property_finder_chart_width = 250;
                    property_finder_chart_font_size = 25;
                    property_finder_chart_verified = 21;
                    bayut_chart_trucheck = 21;
                    bayut_chart_width = 250;
                    bayut_chart_font_size = 25;
                    weekly_chart_width = 200;
                    weekly_chart_font_size = 25;
                    monthly_chart_width = 200;
                    monthly_chart_font_size = 25;
                    counterOffSet = 30;


                        getData()

                } else if (windowWidth <= 2560) {
                    published_chart_width = 400;
                    published_chart_font_size = 50;
                    property_finder_chart_width = 400;
                    property_finder_chart_font_size = 50;
                    property_finder_chart_verified = 32;
                    bayut_chart_trucheck = 32;
                    bayut_chart_width = 400;
                    bayut_chart_font_size = 50;
                    weekly_chart_width = 350;
                    weekly_chart_font_size = 30;
                    monthly_chart_width = 350;
                    monthly_chart_font_size = 30;
                    counterOffSet = 40;


                        getData()

                } else {

                    published_chart_width = 800;
                    published_chart_font_size = 60;
                    property_finder_chart_width = 800;
                    property_finder_chart_font_size = 60;
                    property_finder_chart_verified = 50;
                    bayut_chart_trucheck = 50;
                    bayut_chart_width = 800;
                    bayut_chart_font_size = 60;
                    weekly_chart_width = 650;
                    weekly_chart_font_size = 60;
                    monthly_chart_width = 650;
                    monthly_chart_font_size = 60;
                    counterOffSet = 60;


                        getData()

                }
            }
        $(document).ready(function() {

            checkScreenSize();
            $(window).resize(function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {

                    checkScreenSize();
                    console.log('resized');
                }, 2500);
            });


        });
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const listingGoal = "{{ isset($_GET['listing_goal']) ? $_GET['listing_goal'] : '0' }}";
        const pocketGoal = "{{ isset($_GET['pocket_goal']) ? $_GET['pocket_goal'] : '0' }}";
        const pfGoal = "{{ isset($_GET['pf_goal']) ? $_GET['pf_goal'] : '0' }}";
        const bayutGoal = "{{ isset($_GET['bayut_goal']) ? $_GET['bayut_goal'] : '0' }}";

        function cardChart(element, data, goal, width = 250, label = '', lableFontSize = "16px") {
            let percentage = (data / goal) * 100;
            percentage = percentage.toFixed(2);
            let options = {
                series: [percentage],
                chart: {
                    type: "radialBar",
                    width: width,
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
                                offsetY: 8,
                                color: "#ffffff"
                            },
                            total: {
                                color: "#d3a188",
                                show: !0,
                                label: label,
                                formatter: function(e) {
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

        function cardChartPercentage(element, data, goal, width = 250, label = '', lableFontSize = "16px", counterFontSize =
            "20px") {
            let percentage = (data / goal) * 100;
            percentage = percentage.toFixed(0);
            let options = {
                series: [percentage],
                chart: {
                    type: "radialBar",
                    width: width,
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
                                offsetY: counterOffSet,
                                style: {
                                    margin: `40px 0 0 0` // Adjust the margin as needed
                                },
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
                                formatter: function(e) {
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
                                formatter: function(val) {
                                    return val + "%";
                                }
                            },
                            total: {
                                color: "#d3a188",
                                show: !1,
                                label: total,
                                offsetY: 1,
                                fontSize: '16px',
                                formatter: function(e) {
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
            console.log('getData')
            $.ajax({
                url: "{{ route('front.api.screen3') }}",
                type: "get",
                data: {
                    'dd': '1'

                },
                success: function(result) {
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


                        cardChart('#total_property', data.totalPropertyCount, listingGoal,
                            published_chart_width, '', published_chart_font_size + 'px')
                        $("#pfGoalCount").html(pfGoalCount.toFixed(0))

                        cardChartPercentage('#pfVerifiedGraph', data.pf_verified_count, pfGoalCount.toFixed(0),
                            property_finder_chart_width, 'Verified', property_finder_chart_verified+'px',
                            property_finder_chart_font_size + "px")
                        let bayutGoalCount = (data.bayut_count / 100) * bayutGoal;
                        cardChartPercentage('#total_verified_bayut', data.bayut_trucheck_count, bayutGoalCount,
                            bayut_chart_width, 'TruCheck', bayut_chart_trucheck+'px', bayut_chart_font_size + "px")
                        $("#bayutGoalCount").html(bayutGoalCount.toFixed(0))
                        //get total days in this month
                        let daysInMonth = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0)
                            .getDate();

                        //get total weeks in this month
                        let weekTarget = listingGoal / 4;
                        weekTarget = weekTarget.toFixed(0);

                        // cardChart('#total_property_today', data.todayPublished, dayTarget, 150)
                        //  cardChart('#total_property_week', data.thisWeekPublished, weekTarget, 150)

                        cardChart('#total_property_week', data.thisWeekPublished, weekTarget,
                            weekly_chart_width, '', weekly_chart_font_size + "px")
                        cardChart('#total_property_month', data.thisMonthPublished, monthTarget.toFixed(0),
                            monthly_chart_width, '', monthly_chart_font_size + "px")
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



        function getPfData() {
            $.ajax({
                url: "{{ route('front.api.pf.agents.status') }}",
                type: "get",
                data: {
                    'dd': '1'

                },
                success: function(result) {
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
                url: "{{ route('front.api.bayut.agency.status') }}",
                type: "get",
                data: {
                    'dd': '1'

                },
                success: function(result) {
                    if (result.data) {
                        let count = 0;
                        let name = [];
                        let value = [];
                        // let agency = result.data.agency_name;
                        // let total = result.data.agency_count;
                        let data = result.data;
                        $.each(data, function(key, value) {

                            if (value.name === 'AX CAPITAL Real Estate') {


                                $(".first").html(` <img class="bluer-8" alt="${data[count-1]}" src="${data[count-1].logo}">
                                                     <span class="primary-text agency-text"><span class="agency_position">#${count}</span>
                                                    <b><span class="bluer-21">**** *** ****</span></b></span>  <span class="badge bg-primary-ax text-white mb-0 remaining-badge" style="background: #656463;">${data[count-1].count}</span>
                                                      `)
                                $(".second").html(`<img alt="AX CAPITAL Real Estate" src="${data[count].logo}">
                                                            <span class="primary-text-grad agency-text" style="padding-top:10px;"><span class="agency_position">#${count+1}</span> <b>${data[count].name}</b></span>  <span class="badge bg-primary-ax text-white mb-0" style="background: #656463;">${data[count].count}</span>
                                                        `)
                                $(".third").html(`
                                                            <img class="bluer-2" alt="${data[count+1].name}" src="${data[count+1].logo}">
                                                            <span class="primary-text agency-text"><span class="agency_position">#${count+2}</span> <b><span style="class="bluer-21">**** *** ****</span></b></span>  <span class="badge bg-primary-ax text-white mb-0 remaining-badge" style="background: #656463;">${data[count+1].count}</span>
                                                       `)
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
