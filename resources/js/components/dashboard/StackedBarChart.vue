<script setup>
import {ref, watch, computed} from "vue";
import VChart from "vue-echarts";

const props = defineProps({
    title: String,
    subtitle: String,
    data: Array,
    colors: {
        type: Array,
        default: () => ["#5470c6", "#91cc75", "#fac858", "#ee6666", "#73c0de", "#3ba272", "#fc8452", "#9a60b4"],
    },
    info: {type: Boolean, default: false},
    infoText: {type: String, default: "Information"},
});

// Extract status names
const statusNames = computed(() => {
    return props.data?.[0]?.statuses?.map((s) => s.name) || [];
});

// Track whether a status affects sorting (all start as true)
const selectedStatuses = ref({});
watch(statusNames, (newStatuses) => {
    selectedStatuses.value = Object.fromEntries(newStatuses.map((s) => [s, true]));
}, {immediate: true});

// Compute sorted data dynamically (excluding unchecked statuses from sorting)
const sortedData = computed(() => {
    if (!props.data || props.data.length === 0) return [];

    return [...props.data]
        .map((agent) => {
            const totalCount = agent.statuses
                .reduce((sum, status) => sum + (selectedStatuses.value[status.name] ? status.count : 0), 0);

            return {agent_name: agent.agent_name, totalCount, statuses: agent.statuses};
        })
        .sort((a, b) => b.totalCount - a.totalCount);
});

// Sorted agent names
const sortedAgentNames = computed(() => sortedData.value.map((agent) => agent.agent_name));

// Data remains visible but sorting is based on selected statuses
const sortedRawData = computed(() => {
    return statusNames.value.map((status) =>
        sortedData.value.map((agent) => {
            const foundStatus = agent.statuses.find((s) => s.name === status);
            return foundStatus ? foundStatus.count : 0; // Always return data (never hide bars)
        })
    );
});

// Chart options
const chartOptions = ref({});

const updateChart = () => {
    chartOptions.value = {
        title: {
            text: props.title,
            subtext: props.subtitle,
            left: "center",
            top: 10,
            textStyle: {fontSize: 16, fontWeight: "bold"},
        },
        // tooltip: {trigger: "axis", axisPointer: {type: "shadow"}},
        tooltip: {
            trigger: "axis",
            axisPointer: {type: "shadow"},
            formatter: (params) => {
                let agent = props.data.find(a => a.agent_name === params[0].axisValue);
                if (!agent) return "";

                let html = `<div>`;

                html += `<div>
                            <div class="d-flex align-center gap-2">
                                <img src="${agent.image}" alt="${agent.agent_name}" style="width: 40px; height: 40px; border-radius: 5px;">
                                    <div>
                                        <p style="font-size: 14px; font-weight: 600; margin: 0; padding: 0">${agent.agent_name}</p>
                                        <div class="d-flex align-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2">
                                            <path d="M6 6v6a3 3 0 0 0 3 3h10l-4 -4m0 8l4 -4"></path>
                                        </svg>
                                        <span style="font-size: 12px; font-weight: 500; margin: 0; padding: 0">${agent.manager}</span>

                                        </div>
                                    </div>
                                </div>
                            </div>`

                html += `<hr class="my-2" />`;

                params.forEach((item) => {
                    html += `<div class="d-flex align-center gap-2">
                                <span style="display: inline-block; width: 10px; height: 10px; background: ${item.color}; border-radius: 99px;"></span>
                                <span style="font-size: 12px; font-weight: 500;">${formatText(item.seriesName)}: <strong>${item.value}</strong></span>
                             </div>`;
                });

                html += `</div>`;

                return html;
            },
        },

        legend: {
            bottom: 0,
            left: "center",
            selected: selectedStatuses.value, // Sync selected statuses
        },
        grid: {
            left: 50,
            right: 50,
            top: 80,
            bottom: 200,
        },
        xAxis: {
            type: "category",
            data: sortedAgentNames.value,
            axisLabel: {
                interval: 0,
                rotate: 45,
                fontSize: 12,
                margin: 20,
            },
            axisLine: {show: true},
        },
        yAxis: {
            name: "Count",
        },
        dataZoom: [
            {
                type: "slider",
                show: true,
                start: 0,
                end: sortedAgentNames.value.length > 5 ? 30 : 100,
                bottom: 40,
            },
            {
                type: "inside",
                // moveOnMouseWheel: false,
                zoomOnMouseWheel: false,
            },
        ],
        series: statusNames.value.map((name, index) => ({
            name,
            type: "bar",
            stack: "total",
            barWidth: "50%",
            label: {
                show: false,
                position: "inside",
                fontSize: 12,
                color: "#fff",
                formatter: "{c}",
            },
            itemStyle: {
                color: props.colors[index % props.colors.length],
            },
            data: sortedRawData.value[index], // Bars stay visible, sorting logic is separate
        })),
    };
};

// Watch for data changes
watch(
    () => [props.title, props.subtitle, sortedData.value, selectedStatuses.value],
    updateChart,
    {immediate: true}
);

// Handle legend click: Adjust sorting logic without hiding bars
const onLegendClick = (params) => {
    const status = params.name;
    selectedStatuses.value[status] = !selectedStatuses.value[status]; // Toggle only for sorting
    updateChart();
};

const formatText = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
};
</script>

<template>
    <div class="position-relative">
        <v-menu v-if="info" open-on-hover max-width="300">
            <template v-slot:activator="{ props }">
                <v-icon
                    v-bind="props"
                    style="position: absolute; top: 10px; right: 10px; z-index: 2"
                    color="info"
                >
                    mdi-information-outline
                </v-icon>
            </template>

            <v-card>
                <v-card-text class="text-sm-caption text-grey-darken-1">
                    {{ infoText }}
                </v-card-text>
            </v-card>
        </v-menu>
        <VChart class="chart" :option="chartOptions" autoresize @legendselectchanged="onLegendClick"/>
    </div>
</template>

<style scoped>
.chart {
    width: 100% !important;
    height: 600px;
}
</style>
