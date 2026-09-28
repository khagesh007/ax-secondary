function getChartColorsArray(e) {
    if (null !== document.getElementById(e)) {
        var a = document.getElementById(e).getAttribute("data-colors");
        if (a) return (a = JSON.parse(a)).map(function(e) {
            var a = e.replace(" ", "");
            return -1 === a.indexOf(",") ? getComputedStyle(document.documentElement).getPropertyValue(a) || a : 2 == (e = e.split(",")).length ? "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(e[0]) + "," + e[1] + ")" : a
        });
        console.warn("data-colors atributes not found on", e)
    }
}

const drawRadialBar = (element, data) =>{
    data = isNaN(data) ? 0 : data;

    data = parseInt(data);
    let options = {
        series: [data],
        chart: {
            type: "radialBar",
            width: 105,
            sparkline: {
                enabled: !0
            }
        },
        dataLabels: {
            enabled: !1
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    margin: 0,
                    size: "70%"
                },
                track: {
                    margin: 1
                },
                dataLabels: {
                    show: !0,
                    name: {
                        show: !1
                    },
                    value: {
                        show: !0,
                        fontSize: "16px",
                        fontWeight: 600,
                        offsetY: 8
                    }
                }
            }
        },
        colors: getChartColorsArray(element)
    }
    let chart = new ApexCharts(document.querySelector("#"+element), options);
    chart.render();
}

const getRecentDeals = () => {

    $.ajax({
        url: recentDealUrl+"?agent_id="+agent_id,
        type: "GET",
        success: function(result) {
            if (result.status == '1') {

                let html = `<tr>
                                              <td class="fw-medium">Today's deals</td>
                                              <td>${result.data.bitrix_todays_deal}</td>
                                              <td>${result.data.axcrm_todays_deal}</td>

                                          </tr>`;
                html += `<tr>
                                              <td class="fw-medium">Deal in last 7 days</td>
                                              <td>${result.data.bitrix_past_7_days}</td>
                                              <td>${result.data.axcrm_past_7_days}</td>

                                          </tr>`;
                    html += `<tr>
                        <td class="fw-medium">Deal in last 15 days</td>
                        <td>${result.data.bitrix_past_15_days}</td>
                        <td>${result.data.axcrm_past_15_days}</td>
                        </tr>`;
                        html += `<tr>
                        <td class="fw-medium">Deal in last 30 days</td>
                        <td>${result.data.bitrix_past_30_days}</td>
                        <td>${result.data.axcrm_past_30_days}</td>
                        </tr>`;
                        html += `<tr>
                        <td class="fw-medium">Deal in last 60 days</td>
                        <td>${result.data.bitrix_past_60_days}</td>
                        <td>${result.data.axcrm_past_60_days}</td>
                         </tr>`;
                                          $("#recentDealTable").html(html);
            }
        }
    });
}
