<html>
<head>
    <title>Banner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/fontawesome.min.css"  crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/favicon-32x32.png') }}">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />
    <style>

        :root {
            --background-color: #111010;
            --white: #ffffff;
        }
        body {
            background: var(--background-color);

        }
        .status-card{
            background: #2d2c34;
            padding: 13px;
            border-radius: 5px;
            color: #d3a188;

        }
        .status-card-text{
            background: -webkit-linear-gradient(#d68e6a, #ddcdc6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 25px;
            vertical-align: middle;
            margin-left: 5px;
        }
        .status-card-title{
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
    </style>
</head>
<body>
<div class="container">
    <div class="row mt-5">
        <div class="col-4">

            <div class="text-center text-white">Next goal 1.8 B</div>
            <div id="apex" data-colors="[&quot;--vz-primary&quot;]"></div>


        </div>
        <div class="col-8">
            <div class="row" style="    margin-top: 115px;">
                <div class="col-3 text-center animate__animated animate__fadeIn">
                           <span style="color:#aaa0a0;" id="dubaiDate">
                           </span>
                    <br>
                    <span id="dubaiTime" class="text-white" style="font-size: 21px;"></span>
                    <br>
                    <div class="" style="color: #d3a188;font-size: 24px;">Dubai</div>

                </div>   <div class="col-3 text-center animate__animated animate__fadeIn">
                    <span id="londonDate" style="color:#aaa0a0;"></span>
                    <br>
                    <span id="londonTime" class="text-white" style="font-size: 21px;"></span>
                    <br>
                    <div class="" style="color: #d3a188;font-size: 24px;">LONDON</div>

                </div>   <div class="col-3 text-center animate__animated animate__fadeIn">
                    <span id="moscowDate" style="color:#aaa0a0;"></span>
                    <br>
                    <span id="moscowTime" class="text-white" style="font-size: 21px;"></span>
                    <br>
                    <div class="" style="color: #d3a188;font-size: 24px;">MOSCOW</div>

                </div>   <div class="col-3 text-center animate__animated animate__fadeIn">
                    <span id="newYorkDate" style="color:#aaa0a0;"></span>
                    <br>
                    <span id="newYorkTime" class="text-white" style="font-size: 21px;"></span>
                    <br>
                    <div class="" style="color: #d3a188;font-size: 24px;">NEW YORK</div>

                </div>
            </div>

        </div>
    </div>
    <div  class="row mt-8 animate__animated animate__fadeInUp" style="    margin-top: 30px;">
        <div class="col-4">
            <span class="status-card-title" > Total Deals this month</span>
            <div class="status-card">
                            <span class="circle-border" >
                                <i class="fa fa-plus" style="vertical-align: middle;
    color: #d3a188;
    margin-top: 3px;"></i>
                        </span>
                <span class="status-card-text">  78</span>
            </div>
        </div>  <div class="col-4">
            <span class="status-card-title" > Total SALES value this month</span>
            <div class="status-card">
                            <span class="circle-border" >
                                <i class="fa fa-plus" style="vertical-align: middle;
    color: #d3a188;
    margin-top: 3px;"></i>
                        </span>
                <span class="status-card-text"> 78,345,345</span>
            </div>
        </div>  <div class="col-4">
            <span class="status-card-title" > Total LEAD so far</span>
            <div class="status-card">
                                <span class="circle-border" >
                                <i class="fa fa-plus" style="vertical-align: middle;
                                    color: #d3a188;
                                    margin-top: 3px;"></i>
                        </span>
                <span class="status-card-text">  15,53,594</span>
            </div>
        </div>
    </div> <div class="row animate__animated animate__fadeInUp" style="margin-top:30px;">
        <div class="col-4">
            <span class="status-card-title"> MARKET SHARE/GOAL - 25%</span>
            <div class="status-card">
                              <span class="circle-border" >
                                <i class="fa fa-plus" style="vertical-align: middle;
    color: #d3a188;
    margin-top: 3px;"></i>
                        </span>
                <span class="status-card-text">2.5%</span>
            </div>
        </div>  <div class="col-4">
            <span class="status-card-title"> DLD Transactions, August 2023</span>
            <div class="status-card">
                              <span class="circle-border" >
                                <i class="fa fa-plus" style="vertical-align: middle;
    color: #d3a188;
    margin-top: 3px;"></i>
                        </span>
                <span class="status-card-text">3203</span>

            </div>
        </div>  <div class="col-4">
            <span class="status-card-title"> DLD Sales(AED), August 2023</span>
            <div class="status-card">
                                 <span class="circle-border" >
                                <i class="fa fa-plus" style="vertical-align: middle;
    color: #d3a188;
    margin-top: 3px;"></i>
                        </span>
                <span class="status-card-text">9,815,463,298</span>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"  crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.3/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"  referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.43/moment-timezone.min.js"  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.43/moment-timezone-with-data.min.js"  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    setInterval(function () {
        showDateTime();
    }, 10000);
function showDateTime(){
    $('#dubaiTime').text(moment.tz('Asia/Dubai').format('HH:mm A'));
    $('#newYorkTime').text(moment.tz('America/New_York').format('HH:mm A'));
    $('#londonTime').text(moment.tz('Europe/London').format('HH:mm A'));
    $('#moscowTime').text(moment.tz('Europe/Moscow').format('HH:mm A'));
    $("#dubaiDate").text(moment.tz('Asia/Dubai').format('ddd MMM DD YYYY'));
    $("#newYorkDate").text(moment.tz('America/New_York').format('ddd MMM DD YYYY'));
    $("#londonDate").text(moment.tz('Europe/London').format('ddd MMM DD YYYY'));
    $("#moscowDate").text(moment.tz('Europe/Moscow').format('ddd MMM DD YYYY'));
}
    showDateTime();
    let target = 1800000000;
    let current = 248841347;
    let percentage = (current / target) * 100;
    percentage = percentage.toFixed(0);
    const vals = '248';
    var options = {
        series: [percentage],
        chart: {
            height: 350,
            type: 'radialBar',
            offsetY: -10
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                track: {
                    background: "#312e2e",
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
                        offsetY: 76,
                        fontSize: '22px',
                        color: "white",
                        formatter: function () {
                            return   vals+"m";
                        }
                    }
                }
            }
        },

        colors: ['#d3a188'],
        labels: [''],
    };

    var chart = new ApexCharts(document.querySelector("#apex"), options);
    chart.render();
</script>

</body>
</html>
