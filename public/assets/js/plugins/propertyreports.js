let propertyFinder = {};
let bayutObj = {};
let dubizzleObj = {};


function portalsData() {
    $.ajax({
        type: "POST",
        url: "{{ route('admin.reports.processData') }}",
        data: {
            groupIds: $("#group_id").val(),
            type: "userPropertyMetaReport",
            duration: duration,
        },
        success: function (result) {
            $("#portals_data").removeClass("d-none");
            propertyFinder.total = result.data.propertyfinder.portal_count;
            propertyFinder.level = result.data.propertyfinder.level;
            propertyFinder.pf_avg_rank = result.data.propertyfinder.avg_pf_rank;
            propertyFinder.pf_ranks = result.data.propertyfinder.pf_rank;
            propertyFinder.online = result.data.propertyfinder.online;
            propertyFinder.offline = result.data.propertyfinder.offline;
            propertyFinderBar = result.data.propertyfinder.pf_rank;
            propertyFinder.whatsapp_avg_res =
                result.data.propertyfinder.whatsapp_avg_res;
            $(".pf_whatssapp_avg_time").html(
                convertSecondsToDHMS(propertyFinder.whatsapp_avg_res)
            );
            propertyFinderPieChart(propertyFinder);
            $(".pf_count").html(result.data.propertyfinder.portal_count);
            $(".pf_standard").html(
                result.data.propertyfinder.level.Standard + " Listings"
            );
            $(".pf_featured").html(
                result.data.propertyfinder.level.Featured + " Listings"
            );
            $(".pf_premium").html(
                result.data.propertyfinder.level.Premium + " Listings"
            );

            cardChartPercentage(
                "#pfVerifiedPieChart",
                result.data.propertyfinder.pf_verified,
                result.data.propertyfinder.portal_count,
                300,
                "Verified",
                "1.2vw",
                "2vw"
            );

            bayutObj.level = result.data.bayut.level;
            bayutObj.ranks = result.data.bayut.ranks;
            bayutObj.total = result.data.bayut.portal_count;
            bayutObj.verified = result.data.bayut.verified;
            bayutObj.trucheck = result.data.bayut.trucheck;
            bayutObj.checked = result.data.bayut.checked;
            bayutObj.avg_rank = result.data.bayut.avg_rank;
            bayutPieChart(bayutObj);
            $(".bayut_pie_count").html(result.data.bayut.portal_count);
            $(".bayut_standard").html(
                result.data.bayut.level?.Standard + " Listings"
            );
            $(".bayut_signature").html(
                result.data.bayut.level?.Signature + " Listings"
            );
            $(".bayut_hot").html(result.data.bayut.level?.Hot + " Listings");
            $(".bayut_verfied_count").html(result.data.bayut.verified);

            dubizzleObj.level = result.data.dubizzle.level;
            dubizzleObj.ranks = result.data.dubizzle.ranks;
            dubizzleObj.total = result.data.dubizzle.portal_count;
            dubizzleObj.verified = result.data.dubizzle.verified;
            dubizzleObj.trucheck = result.data.dubizzle.trucheck;
            dubizzleObj.avg_rank = result.data.dubizzle.avg_rank;
            //push data to dubizzleObj object
            dubizzleObj.status = {
                Total: result.data.dubizzle.portal_count,
                Verified: result.data.dubizzle.verified,
                Trucheck: result.data.dubizzle.trucheck,
                Checked: result.data.dubizzle.checked,
            };

            dubizzelePieChart(dubizzleObj);

            $(".dubizzle_total_count").html(result.data.dubizzle.portal_count);
            $(".dubizzle_standard").html(
                result.data.dubizzle.level?.Standard + " Listings"
            );
            $(".dubizzle_featured").html(
                result.data.dubizzle.level?.Featured + " Listings"
            );

            userMetaSplit();
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
    });
}


function cardChartPercentage(element, data, goal, width = 250, label = '', lableFontSize = "16px", counterFontSize =
"20px") {
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
        enabled: 1
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
                background: 'rgb(240, 101, 72)',
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
                    color: "#495057"
                },
                total: {
                    color: "#495057",
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
    colors: ["rgb(69, 203, 133)"],
    //change the bar background color


}
let chart = new ApexCharts(document.querySelector(element), options);
chart.render()
}

function pfStatusPieChart() {


let online = propertyFinder.online;
let offline = propertyFinder.offline;


var chartPieBasicColors = getChartColorsArray("pf-status-pie");
if (chartPieBasicColors) {
    var options = {

        series: [online, offline],
        chart: {
            height: 300,
            type: 'pie',
        },
        labels: ["Listings Online", "Listings Offline"],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            dropShadow: {
                enabled: false,
            }
        },
        colors: chartPieBasicColors
    };

    var chart = new ApexCharts(document.querySelector("#pf-status-pie"), options);
    chart.render();
}

}

function dubizzelePieChart(data) {
let standard = data.standard;
let featured = data.featured;


var donutchartProjectsStatusColors = getChartColorsArray("dubizzle-doghnout-chart");

if (donutchartProjectsStatusColors) {
    let chartData = [];
    let chartLabel = [];
    let total = data.total;

    jQuery.each(data.level, function(index, item) {
        chartData.push(item);
        chartLabel.push(index);
        total = total - item;
    });
    chartData.push(total);
    chartLabel.push('Others');
    var options = {
        series: chartData,
        labels: chartLabel,
        chart: {
            type: "donut",
            height: 170,
        },


        plotOptions: {
            pie: {
                size: 100,
                offsetX: 0,
                offsetY: 0,
                donut: {
                    size: "80%",
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b
                                }, 0)
                            }
                        }
                    },

                },
            },
        },
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: false,
        },
        stroke: {
            lineCap: "round",
            width: 0
        },
        colors: donutchartProjectsStatusColors,
    };
    var chart = new ApexCharts(document.querySelector("#dubizzle-doghnout-chart"), options);
    chart.render();
}

}

function pfRankBarChart() {
if (typeof propertyFinderBar == 'object') {
    let data = propertyFinderBar;
    let chartData = [];
    let chartLabel = [];
    jQuery.each(data, function(index, item) {
        chartData.push(item);
        chartLabel.push(index + '%');
    });

    let element = document.getElementById('pf_bar_chart');
    barChart(chartLabel, chartData, element);

}
}

function barChart(chartLabel, chartData, element) {
var options = {
    series: [{
        name: 'Listings having this quality',
        data: chartData
    }],
    chart: {
        height: 350,
        type: 'bar',
    },
    plotOptions: {
        bar: {
            borderRadius: 1,
            dataLabels: {
                position: 'top', // top, center, bottom
            },
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function(val) {
            return val;
        },
        offsetY: -20,
        style: {
            fontSize: '12px',
            colors: ["#878a99"]
        }
    },

    xaxis: {
        categories: chartLabel,
        position: 'bottom',
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: true
        },
        crosshairs: {
            fill: {
                type: 'gradient',
                gradient: {
                    colorFrom: '#D8E3F0',
                    colorTo: '#BED1E6',
                    stops: [0, 100],
                    opacityFrom: 0.4,
                    opacityTo: 0.5,
                }
            }
        },
        tooltip: {
            enabled: true,
        }
    },
    yaxis: {
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false,
        },
        labels: {
            show: true,
            formatter: function(val) {
                return val;
            }
        }

    }

};

var chart = new ApexCharts(element, options);
chart.render();

}

function propertyFinderPieChart(data) {
    var donutchartProjectsStatusColors = getChartColorsArray("pf-status");

    if (donutchartProjectsStatusColors) {
        let chartData = [];
        let chartLabel = [];
        let total = data.total;

        jQuery.each(data.level, function(index, item) {
            chartData.push(item);
            chartLabel.push(index);
            total = total - item;
        });
        chartData.push(total);
        chartLabel.push('Others');
        var options = {
            series: chartData,
            labels: chartLabel,
            chart: {
                type: "donut",
                height: 170,
            },


            plotOptions: {
                pie: {
                    size: 100,
                    offsetX: 0,
                    offsetY: 0,
                    donut: {
                        size: "80%",
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b
                                    }, 0)
                                }
                            }
                        },

                    },
                },
            },
            dataLabels: {
                enabled: false,
            },
            legend: {
                show: false,
            },
            stroke: {
                lineCap: "round",
                width: 0
            },
            colors: donutchartProjectsStatusColors,
        };
        var chart = new ApexCharts(document.querySelector("#pf-status"), options);
        chart.render();
    }
}


function pfRankMeterChart() {
    let dom = document.getElementById('pf_rank_meter');
    let value = propertyFinder.pf_avg_rank;
    rankMeterChart(dom, value);
}

function bayutRankMeterChart() {
    let dom = document.getElementById('bayut_rank_meter');
    let value = bayutObj.avg_rank;
    rankMeterChart(dom, value);
}

function dubizzleRankMeterChart() {
    let element = document.getElementById('dubbizle_rank_meter');
    let value = dubizzleObj.avg_rank;
    rankMeterChart(element, value);

}

function rankMeterChart(element, value) {
    var myChart = echarts.init(element, null, {
        renderer: 'canvas',
        useDirtyRect: false
    });
    var app = {};

    var option;

    option = {
        series: [{
            type: 'gauge',
            axisLine: {
                lineStyle: {
                    width: 15,
                    color: [
                        [0.3, '#d22500'],
                        [0.5, '#f06547'],
                        [0.7, '#f6b84b'],
                        [1, '#46cb85']
                    ]
                }
            },
            pointer: {
                itemStyle: {
                    color: 'inherit'
                }
            },
            axisTick: {
                distance: -30,
                length: 2,
                lineStyle: {
                    color: '#fff',
                    width: 1
                }
            },
            splitLine: {
                distance: -30,
                length: 30,
                lineStyle: {
                    color: '#fff',
                    width: 4
                }
            },
            axisLabel: {
                color: 'inherit',
                distance: 25,
                fontSize: 15
            },
            detail: {
                valueAnimation: true,
                formatter: '{value} %',
                color: 'inherit',
                fontSize: 25
            },
            data: [{
                value: value
            }]
        }]
    };

    myChart.setOption({
        series: [{
            data: [{
                value: 50
            }]
        }]
    });
    if (option && typeof option === 'object') {
        myChart.setOption(option);
    }
    window.addEventListener('resize', myChart.resize);
}


function bayutPieChart(data) {

    let chartData = [];
    let chartLabel = [];
    let total = data.total;
    jQuery.each(data.level, function(index, item) {
        chartData.push(item);
        chartLabel.push(index);
        total = total - item;
    });


    var donutchartProjectsStatusColors = getChartColorsArray("bayut-pie-chart");
    if (donutchartProjectsStatusColors) {
        var options = {
            series: chartData,
            labels: chartLabel,
            chart: {
                type: "donut",
                height: 170,
            },
            plotOptions: {
                pie: {
                    size: 100,
                    offsetX: 0,
                    offsetY: 0,
                    donut: {
                        size: "80%",
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b
                                    }, 0)
                                }
                            }
                        }
                    },
                },
            },
            dataLabels: {
                enabled: false,
            },
            legend: {
                show: false,
            },
            stroke: {
                lineCap: "round",
                width: 0
            },
            colors: donutchartProjectsStatusColors,
        };
        var chart = new ApexCharts(document.querySelector("#bayut-pie-chart"), options);
        chart.render();
    }
}

function bayutVerfiedChart() {

    let total = bayutObj.total;
    let verified = bayutObj.verified;
    let trucheck = bayutObj.trucheck;
    let checked = bayutObj.checked;


    var chartPieBasicColors = getChartColorsArray("bayut-verified-chart");
    if (chartPieBasicColors) {
        var options = {

            series: [verified, trucheck, checked],
            chart: {
                height: 300,
                type: 'pie',
            },
            labels: ["Verified", "Trucheck", "Checked"],
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                dropShadow: {
                    enabled: false,
                }
            },
            colors: chartPieBasicColors
        };

        var chart = new ApexCharts(document.querySelector("#bayut-verified-chart"), options);
        chart.render();
    }

}

