<html>
<head>
    <title>Banner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/fontawesome.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
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

        .portal-card {
            border-width: 1px;
            border-style: solid;
            border-image: linear-gradient(
                to top,
                #d3a188,
                rgba(0, 0, 0, 0)
            ) 1 100%;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="row mt-5">


    </div>
    <div class="row mt-8 " style="    margin-top: 30px;">
        <div class="col-4 portal-card ">
            <div>


                <div id="apex1" data-colors="[&quot;--vz-primary&quot;]"></div>

            </div>
            <div class="text-center">
                <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644265820230215063058.png" alt="" width="20px">
                <span class="status-card-title"> Property Finder</span>
            </div>
            <div class="mt-2">

                <div class="status-card animate__animated animate__fadeIn">
                    Total
                    <span class="status-card-text float-end" id="pf_total"> 0</span>
                </div>
            </div>
            <div class="mt-2">

                <div class="status-card animate__animated animate__fadeIn">
                    Verified
                    <span class="status-card-text float-end" id="pf_verified"> 0</span>
                </div>
            </div>
            <div class="mt-2">

                <div class="status-card animate__animated animate__fadeIn">
                    .
                </div>
            </div>
            <div class="mt-2">

                <div class="status-card animate__animated animate__fadeIn">
                   .
                </div>
            </div>


        </div>
        <div class="col-4 portal-card">
            <div>


                <div id="apex2" data-colors="[&quot;--vz-primary&quot;]"></div>

            </div>
            <div class="text-center">
                <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644264020230215063040.png" alt="" width="20px">
                <span class="status-card-title"> Dubizzle</span>
            </div>
            <div class="mt-2">

                <div class="status-card">
                          Total
                    <span class="status-card-text float-end" id="dubizzleTotal"> 0</span>
                </div>
            </div>
            <div class="mt-2">

                <div class="status-card">
                          Verified
                    <span class="status-card-text float-end" id="dubizzleTrucheck"> 0</span>
                </div>
            </div>
            <div class="mt-2">

                <div class="status-card">
                          Truchecked
                    <span class="status-card-text float-end"> 0</span>
                </div>
            </div> <div class="mt-2">

                <div class="status-card">
                          Checked
                    <span class="status-card-text float-end"> 0</span>
                </div>
            </div>



        </div>
        <div class="col-4 portal-card">
            <div>


                <div id="apex3" data-colors="[&quot;--vz-primary&quot;]"></div>

            </div>
            <div class="text-center">
                <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644259820230215062958.png" alt="" width="20px">   <span class="status-card-title"> Bayut</span>
            </div>
            <div class="mt-2">

                <div class="status-card">
                    Total
                    <span class="status-card-text float-end" id="bayutTotal"> 0</span>
                </div>
            </div>
            <div class="mt-2">

                <div class="status-card">
                    Verified
                    <span class="status-card-text float-end" id="bayutVerified" > 0</span>
                </div>
            </div>
            <div class="mt-2">

                <div class="status-card">
                    Truchecked
                    <span class="status-card-text float-end" id="bayutTrucheck"> 0</span>
                </div>
            </div> <div class="mt-2">

                <div class="status-card">
                    Checked
                    <span class="status-card-text float-end"> 0</span>
                </div>
            </div>

        </div>
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
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

function drawRadialBar(element, current, total) {
    let target = total;
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
                        formatter: function (val) {
                            return val + "%";
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
            url: "{{route('front.api.screen2')}}",
            type: "get",
            data: {
                'dd': '1'
            },
            success: function (result) {
                if (result.data) {
                    let data = result.data;
                    let total = data.totalPropertyCount;
                    $("#pf_total").text(data.pf_count || 0);
                    $("#pf_verified").text(data.pf_verfified_count || 0);

                    $("#bayutTotal").text(data.bayut_count || 0);
                    $("#bayutVerified").text(data.bayut_verified_count || 0);
                    $("#bayutTrucheck").text(data.bayut_trucheck_count || 0);

                    $("#dubizzleTotal").text(data.dubizzle_count || 0);
                    $("#dubizzleTrucheck").text(data.dubizzle_trucheck || 0);
                    drawRadialBar("#apex1", data.pf_count,total);
                    drawRadialBar("#apex2", data.dubizzle_count,total);
                    drawRadialBar("#apex3", data.bayut_count,total);

                }
            }
        });
    }

    getData()
</script>

</body>
</html>
