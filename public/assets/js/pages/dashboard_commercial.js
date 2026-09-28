let propertyFinder = {};
let bayutObj = {};
let dubizzleObj = {};
let leadObj = {};
let propertyFinderBar = {};
let agentsObj = {};
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

function overViewChartDraw(data) {

    let chartData = [];
    let chartLabel = [];

    for (let i = 0; i < data.length; i++) {
        chartData.push(data[i].count);
        data[i].publish_status = data[i].publish_status.charAt(0).toUpperCase() + data[i].publish_status.slice(1).toLowerCase();
        chartLabel.push(data[i].publish_status);
    }
    var chartDatalabelsBarColors = getChartColorsArray("listingOverviewBarChart");
    if (chartDatalabelsBarColors) {

            var o = {
                series: [{
                    data: chartData,
                    name: "Listings"
                }],
                chart: {
                    type: "bar",
                    height: 265,
                    toolbar: {
                        show: !1
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 2,
                        horizontal: !0,
                        distributed: !0,
                        dataLabels: {
                            position: "top"
                        }
                    }
                },
                colors: chartDatalabelsBarColors,
                dataLabels: {
                    enabled: !0,
                    offsetX: 32,
                    style: {
                        fontSize: "12px",
                        fontWeight: 400,
                        colors: ["black"]
                    }
                },
                legend: {
                    show: !1
                },
                grid: {
                    show: !1
                },
                xaxis: {
                    categories: chartLabel
                }
            };
            new ApexCharts(document.querySelector("#listingOverviewBarChart"), o).render()
        $("#loader_listingOverviewBarChart").addClass("d-none");

    }
}

function filterEvents(type) {
    getEventDataHtmlList(globalUser.id, 'agent_id', '1', type, 'upcoming-event-list');
}

function getOverviewData() {

    $.ajax({
        url: route('admin.dashboard.commercial.overview'),
        type: "GET",
        success: function (result) {
            if (result.status == '1') {

                listingNotChanged(result.data.listing_published_in);
                overViewChartDraw(result.data.listing_overview)
            }
        }
    })
    getEventDataHtmlList(globalUser.id, 'agent_id', '1', '', 'upcoming-event-list');

}

getOverviewData();

function recentLeads() {

    $.ajax({
        url: route('admin.dashboard.commercial.recent.leads'),
        type: "GET",
        success: function (result) {
            if (result.status == '1') {

                recentLeadsDraw(result.data,'recentLeadChangedBody','loader_recent_lead_loader');

            }
        }
    })

}
function recentListingLeads() {

    $.ajax({
        url: route('admin.dashboard.commercial.recent.listing.leads'),
        type: "GET",
        success: function (result) {
            if (result.status == '1') {
                recentLeadsDraw(result.data,'recentListingLeadChangedBody','loader_recent_listing_lead_loader');
            }
        }
    })

}
function leadStatusPieChart(data) {

    let chartData = Object.entries(data).map(([name, value]) => ({
        value,
        name
    }));

    var chartPieColors = getChartColorsArray("lead-status-pie");
    if (chartPieColors) {
        var chartDom = document.getElementById('lead-status-pie');
        var myChart = echarts.init(chartDom);
        var option;

        option = {
            tooltip: {
                trigger: 'item'
            },
            label: {
                alignTo: 'edge',
                formatter: '{name|{b}}\n{time|{c} total}',
                minMargin: 5,
                edgeDistance: 10,
                lineHeight: 15,
                rich: {
                    time: {
                        fontSize: 10,
                        color: '#999'
                    }
                }
            },
            labelLine: {
                length: 15,
                length2: 0,
                maxSurfaceAngle: 80
            },

            color: chartPieColors,
            series: [{
                name: 'Total Leads',
                type: 'pie',
                radius: '50%',
                data: chartData,
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }],
            textStyle: {
                fontFamily: 'Poppins, sans-serif'
            },
        };

        option && myChart.setOption(option);
        $("#loader_lead_status_pie").addClass("d-none");
    }
}

//lead
function leadSourcePieChart(data = {}) {
    data = data == !undefined ? data : leadObj.lead_by_source;
    let chartData = [];
    let chartLabel = [];
    jQuery.each(data, function (index, item) {
        chartData.push(item);
        chartLabel.push(index);
    });


    var chartDom = document.getElementById('lead-source-pie');


    var options = {
        chart: {
            height: 650,
            type: 'bar',
            toolbar: {
                show: false,
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        series: [{
            data: chartData
        }],

        grid: {
            borderColor: '#f1f1f1',
        },
        xaxis: {
            categories: chartLabel,
        }
    }
    var chart = new ApexCharts(document.querySelector("#lead-source-pie"), options);
    chart.render();
    $("#loader_lead_source_pie").addClass("d-none");

}

function pfRankMeterChart() {

    let dom = document.getElementById('pf_rank_meter');
    let value = propertyFinder.pf_avg_rank;
    rankMeterChart(dom, value);

}

function pfRankBarChart() {
    if (typeof propertyFinderBar == 'object') {
        let data = propertyFinderBar;
        let chartData = [];
        let chartLabel = [];
        jQuery.each(data, function (index, item) {
            chartData.push(item);
            chartLabel.push(index + '%');
        });

        let element = document.getElementById('pf_bar_chart');
        barChart(chartLabel, chartData, element);

    }
}


// property finder
function propertyFinderPieChart(data) {
    var donutchartProjectsStatusColors = getChartColorsArray("pf-status");

    if (donutchartProjectsStatusColors) {
        let chartData = [];
        let chartLabel = [];
        let total = data.total;

        jQuery.each(data.level, function (index, item) {
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
                                formatter: function (w) {
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

function propertyFinderData() {
    $.ajax({
        url: route('admin.dashboard.commercial.pf.data'),
        type: "GET",
        success: function (result) {
            if (result.status == '1') {
                propertyFinder.total = result.data.portal_count;
                propertyFinder.level = result.data.level;
                propertyFinder.pf_avg_rank = result.data.avg_pf_rank;
                propertyFinder.pf_ranks = result.data.pf_rank;
                propertyFinder.online = result.data.online;
                propertyFinder.offline = result.data.offline;
                propertyFinderBar = result.data.pf_rank;
                propertyFinder.whatsapp_avg_res = result.data.whatsapp_avg_res;
                $(".pf_whatssapp_avg_time").html(convertSecondsToDHMS(propertyFinder.whatsapp_avg_res));
                propertyFinderPieChart(propertyFinder);
                $(".pf_count").html(result.data.portal_count);
                $(".pf_standard").html(result.data.level.Standard+' Listings');
                $(".pf_featured").html(result.data.level.Featured+' Listings');
                $(".pf_premium").html(result.data.level.Premium+' Listings');

                cardChartPercentage('#pfVerifiedPieChart', result.data.pf_verified, result.data.portal_count, 300, 'Verified', '1.2vw', "2vw")

            }
        }
    })
}

propertyFinderData();

//bauyt graphs
function bayutPieChart(data) {

    let chartData = [];
    let chartLabel = [];
    let total = data.total;
    jQuery.each(data.level, function (index, item) {
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
                                formatter: function (w) {
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



function bayutRankMeterChart() {
    let dom = document.getElementById('bayut_rank_meter');
    let value = bayutObj.avg_rank;
    rankMeterChart(dom, value);
}

function bayutData() {
    $.ajax({
        url: route('admin.dashboard.commercial.bayut.data'),
        type: "GET",
        success: function (result) {
            if (result.status == '1') {
                bayutObj.level = result.data.level;
                bayutObj.ranks = result.data.ranks;
                bayutObj.total = result.data.portal_count;
                bayutObj.verified = result.data.verified;
                bayutObj.trucheck = result.data.trucheck;
                bayutObj.checked = result.data.checked;
                bayutObj.avg_rank = result.data.avg_rank;
                bayutPieChart(bayutObj);
                $(".bayut_pie_count").html(result.data.portal_count);
                $(".bayut_standard").html(result.data.level?.Standard + ' Listings');
                $(".bayut_signature").html(result.data.level?.Signature + ' Listings');
                $(".bayut_hot").html(result.data.level?.Hot + ' Listings');
                $(".bayut_verfied_count").html(result.data.verified);
            }
        }
    })
}

bayutData();

function bayutQualityBarChart() {
    if (typeof bayutObj.ranks == 'object') {
        let data = bayutObj.ranks;
        let chartData = [];
        let chartLabel = [];
        jQuery.each(data, function (index, item) {
            chartData.push(item);
            chartLabel.push(index + '%');
        });
let element = document.getElementById('bayut_quality_bar_chart');
        barChart(chartLabel, chartData, element);

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

        jQuery.each(data.level, function (index, item) {
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
                                formatter: function (w) {
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
        series: [
            {
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
                data: [
                    {
                        value: value
                    }
                ]
            }
        ]
    };

    myChart.setOption({
        series: [
            {
                data: [
                    {
                        value: 50
                    }
                ]
            }
        ]
    });


    if (option && typeof option === 'object') {
        myChart.setOption(option);
    }

    window.addEventListener('resize', myChart.resize);

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
                        formatter: function (e) {
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
                formatter: function (val) {
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
                    formatter: function (val) {
                        return val;
                    }
                }

            }

        };

        var chart = new ApexCharts(element, options);
        chart.render();

}
function dubizzleQualityBarChart() {
    if (typeof dubizzleObj.ranks == 'object') {
        let data = dubizzleObj.ranks;
        let chartData = [];
        let chartLabel = [];
        jQuery.each(data, function (index, item) {
            chartData.push(item);
            chartLabel.push(index + '%');
        });
        let element = document.getElementById('dubizzle_quality_bar_chart');
        barChart(chartLabel, chartData, element);


    }
}

function dubizzleData() {
    $.ajax({
        url: route('admin.dashboard.commercial.dubizzle.data'),
        type: "GET",
        success: function (result) {
            if (result.status == '1') {
                dubizzleObj.level = result.data.level;
                dubizzleObj.ranks = result.data.ranks;
                dubizzleObj.total = result.data.portal_count;
                dubizzleObj.verified = result.data.verified;
                dubizzleObj.trucheck = result.data.trucheck;
                dubizzleObj.avg_rank = result.data.avg_rank;
                //push data to dubizzleObj object
                dubizzleObj.status = {
                    'Total': result.data.portal_count,
                    'Verified': result.data.verified,
                    'Trucheck': result.data.trucheck,
                    'Checked': result.data.checked,
                }

                dubizzelePieChart(dubizzleObj)

                $(".dubizzle_total_count").html(result.data.portal_count);
                $(".dubizzle_standard").html(result.data.level?.Standard + ' Listings');
                $(".dubizzle_featured").html(result.data.level?.Featured + ' Listings');
                $(".dubizzle_premium").html(result.data.level?.Premium + ' Listings');


            }
        }
    })
}

dubizzleData();


function getListingOverView() {
    $.ajax({
        url: route('admin.dashboard.commercial.pf.data'),
        type: "GET",
        success: function (result) {
            if (result.status == '1') {
                propertyFinder.level = result.data.level;
                $(".pf_count").html(result.data.portal_count);
                $(".pf_standard").html(result.data.level.Standard);
                $(".pf_featured").html(result.data.level.Featured);
                $(".pf_premium").html(result.data.level.Premium);

            }
        }
    })
}

function getDashboardLeads() {
    $.ajax({
        url: route('admin.dashboard.commercial.leads'),
        type: "GET",
        success: function (result) {
            if (result.status == '1') {
                leadObj.last6month = result.data.last6month;
                leadObj.lead_by_status = result.data.lead_by_status;
                leadObj.lead_by_source = result.data.lead_by_source;

                leadStatusPieChart(result.data.lead_by_status);

                leadLineChart(leadObj);

            }
        }
    })
}

getDashboardLeads();

function listingNotChanged(data) {
    $("#listingNotChangedBody").html('');
    let totalListing = data.totalProperties;
    let listingData = data.counts;
    for (let key in listingData) {
        if (listingData.hasOwnProperty(key)) {
            let percentage = (parseInt(listingData[key]) / totalListing) * 100;
            $("#loader_listingNotChangedBody").addClass("d-none");
            $("#listingNotChangedBody").append(`<tr>
                                                      <td class="fw-medium">${key}</td>
                                                      <td>${listingData[key]}</td>
                                                      <td>
                                                          <div class="d-flex align-items-center">
                                                              <div class="flex-shrink-0 me-1 text-muted fs-13">${percentage.toFixed(2)}%</div>
                                                              <div class="progress progress-sm bg-soft-primary flex-grow-1"
                                                                   style="width: 68%;">
                                                                  <div class="progress-bar bg-primary rounded"
                                                                       role="progressbar" style="width: ${percentage}%"
                                                                       aria-valuenow="${percentage}" aria-valuemin="0"
                                                                       aria-valuemax="100"></div>
                                                              </div>
                                                          </div>
                                                      </td>
                                                  </tr>`);
        }
    }


}
// function recentLeadsDraw(data) {
//     $("#recentLeadChangedBody").html('');
//     let totalListing = data.totalLeads;
//     let listingData = data.counts;
//     for (let key in listingData) {
//         if (listingData.hasOwnProperty(key)) {
//             let percentage = (parseInt(listingData[key]) / totalListing) * 100;
//             $("#loader_recent_lead_loader").addClass("d-none");
//             $("#recentLeadChangedBody").append(`<tr>
//                                                       <td class="fw-medium">${key}</td>
//                                                       <td>${listingData[key]}</td>
//                                                       <td>
//                                                           <div class="d-flex align-items-center">
//                                                               <div class="flex-shrink-0 me-1 text-muted fs-13">${percentage.toFixed(2)}%</div>
//                                                               <div class="progress progress-sm bg-soft-primary flex-grow-1"
//                                                                    style="width: 68%;">
//                                                                   <div class="progress-bar bg-primary rounded"
//                                                                        role="progressbar" style="width: ${percentage}%"
//                                                                        aria-valuenow="${percentage}" aria-valuemin="0"
//                                                                        aria-valuemax="100"></div>
//                                                               </div>
//                                                           </div>
//                                                       </td>
//                                                   </tr>`);
//         }
//     }
//
//
// }
// var chartPieBasicColors = getChartColorsArray("simple_pie_chart");
// if(chartPieBasicColors){
//     var options = {
//
//         series: [44, 55, 13, 43, 22],
//         chart: {
//             height: 300,
//             type: 'pie',
//         },
//         labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
//         legend: {
//             position: 'bottom'
//         },
//         dataLabels: {
//             dropShadow: {
//                 enabled: false,
//             }
//         },
//         colors: chartPieBasicColors
//     };
//
//     var chart = new ApexCharts(document.querySelector("#simple_pie_chart"), options);
//     chart.render();
// }
function leadLineChart(leadObj) {
    let months = leadObj.last6month?.months;
    let newLeads = leadObj.last6month?.newLeads;
    let successful = leadObj.last6month?.successful;
    let unsuccessful = leadObj.last6month?.unsuccessful;


    var ctx = document.getElementById("leadStatusLineChart").getContext('2d');


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
                    label: 'Successful', // Name the series
                    data: successful, // Specify the data values array
                    fill: false,
                    borderColor: '#45cb85', // Add custom color border (Line)
                    backgroundColor: '#45cb85', // Add custom color background (Points and Fill)
                    borderWidth: 1 // Specify bar border width
                }, {
                    label: 'Unsuccessful', // Name the series
                    data: unsuccessful, // Specify the data values array
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

   // var revenueExpensesChartsColors = getChartColorsArray("leadStatusLineChart");
   //  revenueExpensesChartsColors && (options = {
   //      series: [{
   //          name: "New",
   //          data: newLeads
   //      }, {
   //          name: "Successful",
   //          data: successful
   //      }, {
   //          name: "Unsuccessful",
   //          data: unsuccessful
   //      }],
   //      chart: {
   //          height: 290,
   //          type: "area",
   //          toolbar: "false"
   //      },
   //      dataLabels: {
   //          enabled: !1
   //      },
   //      stroke: {
   //          curve: "smooth",
   //          width: 2
   //      },
   //      xaxis: {
   //          categories: months
   //      },
   //      yaxis: {
   //          labels: {
   //              formatter: function(e) {
   //                  return "$" + e + "k"
   //              }
   //          },
   //          tickAmount: 5,
   //          min: 0,
   //          max: 260
   //      },
   //      colors: revenueExpensesChartsColors,
   //      fill: {
   //          opacity: .06,
   //          colors: revenueExpensesChartsColors,
   //          type: "solid"
   //      }
   //  },
   //      (chart = new ApexCharts(document.querySelector("#leadStatusLineChart"), options)).render());

    $("#loader_leadStatusLineChart").addClass("d-none");
}

function stackedBarChart(data){
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
            agent_resi_rent.push(item.for_residential_rent);
            agent_resi_sale.push(item.for_residential_sale);
            agent_com_sale.push(item.for_commercial_sale);
            agent_com_rent.push(item.for_commercial_rent);
        }
        });


        var options = {
            series: [{
                name: 'Residential Rent',
                data: agent_resi_rent
            }, {
                name: 'Commercial Rent',
                data: agent_com_rent
            }, {
                name: 'Residential Sale',
                data: agent_resi_sale
            }, {
                name: 'Commercial Sale',
                data: agent_com_sale
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
                text: 'Agent Listing Overview',
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
function agentLeadBarChart(data,elementId,lead_type = 'leads'){
$("#loader_"+elementId).addClass("d-none");
    var chartStackedBarColors = getChartColorsArray(elementId);
    if(chartStackedBarColors){
        let agents = [];
        let leads = [];


        jQuery.each(data, function (index, item) {
            if(item != 0){
                leads.push(item);
                agents.push(index);
            }
        });


        var options = {
            series: [{
                name: 'leads',
                data: leads
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
                    dataLabels: {

                        maxItems: 1, // Maximum number of labels to display per bar
                        hideOverflowingLabels: true // Hide overflowing labels
                    },
                },
            },
            stroke: {
                width: 2,
                colors: ['#fff']
            },
            title: {
                text: 'Agent '+lead_type+' leads',
                style: {
                    fontWeight: 300,
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

        var chart = new ApexCharts(document.querySelector("#"+elementId), options);
        chart.render();
    }
}
function agentStatusBarChart(data,elementId,title){
    $("#loader_"+elementId).addClass("d-none");
    var chartStackedBarColors = getChartColorsArray(elementId);
    if(chartStackedBarColors){
        let agents = [];
        let leads = [];


        jQuery.each(data, function (index, item) {
            if(item != 0){
                leads.push(item);
                agents.push(index);
            }
        });


        var options = {
            series: [{
                name: 'Listings',
                data: leads
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
                    barWidth: '50%',
                },
            },
            stroke: {
                width: 2,
                colors: ['#fff']
            },
            title: {
                text: title,
                style: {
                    fontWeight: 300,
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

        var chart = new ApexCharts(document.querySelector("#"+elementId), options);
        chart.render();
    }
}


function getAgentsData(){

    $.ajax({
        url: route('admin.dashboard.commercial.agent.overview.data'),
        type: "GET",
        success: function (result) {
            if (result.status == '1') {
                agentsObj.listings = result.data.agent_listings;
                stackedBarChart(agentsObj.listings);

            }
        }
    })
}
getAgentsData()
function getAgentsLead(status,chartDivId){
    $("#"+chartDivId).html("");

    $("#loader_"+chartDivId).removeClass("d-none");
    $.ajax({
        url: route('admin.dashboard.commercial.agent.leads.data'),
        type: "GET",
        data: {
            status: status
        },
        success: function (result) {
            if (result.status == '1') {

                agentLeadBarChart(result.data.leads,chartDivId,status);

            }
        }
    })
}
function getAgentMetaData(chartDivId,title,key,val){
    $("#"+chartDivId).html("");
    $("#loader_"+chartDivId).removeClass("d-none");
    $.ajax({
        url: route('admin.reports.agent.portal.commercial'),
        type: "POST",
         data: {
                key: key,
                val: val
         },
        dataType: "json",
        success: function (result) {
            if (result.status == '1') {
                agentStatusBarChart(result.data.portal_data,chartDivId,title);
            }
        }
    })
}


