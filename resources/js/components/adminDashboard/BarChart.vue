<script setup>
import {ref, watch, computed} from "vue";
import VChart from "vue-echarts";

const props = defineProps({
    title: {type: String, default: "Number of Listings"},
    subtitle: {
        type: String,
        default: "Number of listings",
    },
    data: {type: Array, required: true},
    itemNameKey: {type: String, default: "name"},
    itemValueKey: {type: String, default: "count"},
    barColor: {type: String, default: "#d3a188"},
    info: {type: Boolean, default: false},
    infoText: {type: String, default: "Information"},
});

const tab = ref("chart");
const page = ref(1);
const headers = ref([
    {title: "User", value: "user_name"},
    {title: "Count", value: "count"},
]);

const options = ref({
    title: {
        text: "",
        subtext: "",
        left: "center",
        top: 0,
        textStyle: {
            fontSize: 16,
            fontWeight: "bold",
            rich: {
                icon: {
                    fontSize: 18, // Adjust icon size
                },
            },
        },
    },
    tooltip: {
        trigger: "axis",
        axisPointer: {type: "shadow"},
        formatter: (params) => {
            let user = props.data.find(a => a[props.itemNameKey] === params[0].axisValue);
            if (!user) return "";

            let html = `<div>`;

            // user Image, Name, and Parent
            html += `<div>
                    <div class="d-flex align-center gap-2">
                        <img src="${user.image}" alt="${user[props.itemNameKey]}" style="width: 40px; height: 40px; border-radius: 5px;">
                        <div>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; padding: 0">${user[props.itemNameKey]}</p>
                            <div class="d-flex align-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2">
                                    <path d="M6 6v6a3 3 0 0 0 3 3h10l-4 -4m0 8l4 -4"></path>
                                </svg>
                                <span style="font-size: 12px; font-weight: 500; margin: 0; padding: 0">${user.parent}</span>
                            </div>
                        </div>
                    </div>
                </div>`;

            html += `<hr class="my-2" />`;

            // Series Data
            html += `<div class="d-flex align-center gap-2">
                         <span style="display: inline-block; width: 10px; height: 10px; background: #d3a188; border-radius: 99px;"></span>
                         <span style="font-size: 12px; font-weight: 500;"><strong>${user[props.itemValueKey]}</strong></span>
                     </div>`;

            html += `</div>`;

            return html;
        }
    },
    dataset: [],
    xAxis: {
        type: "category",
        axisLabel: {interval: 0, rotate: 45}, // Rotate labels for better fit
    },
    yAxis: {name: "Count"},
    series: {
        type: "bar",
        encode: {
            x: props.itemNameKey,
            y: props.itemValueKey,
        },
        datasetIndex: 1,
        itemStyle: {borderRadius: 5},
        label: {
            show: true,
            position: "inside", // Display count inside the bar
            color: "#fff", // White text for visibility
            fontSize: 14,
            fontWeight: "bold",
        },
    },
    grid: {
        left: "3%",
        right: "3%",
        top: "15%",
        bottom: "5%", // Reduce bottom space
        containLabel: true,
    },
    dataZoom: [
        {
            type: "slider",
            show: true,
            start: 0,
            end: 30,
            bottom: 40,
        },
        {
            type: "inside",
            // moveOnMouseWheel: false,
            zoomOnMouseWheel: false,
        },
    ],
});

// Watch for prop changes and update the chart dynamically
watch(
    props,
    () => {
        options.value.title.text = props.title;
        options.value.title.subtext = props.subtitle;

        if (props.barColor) {
            options.value.series.itemStyle.color = props.barColor;
        }

        options.value.dataset = [
            {
                dimensions: [props.itemNameKey, props.itemValueKey],
                source: Array.isArray(props.data) ? props.data : [],
            },
            {
                transform: {
                    type: "sort",
                    config: {
                        dimension: props.itemValueKey,
                        order: "desc",
                    },
                },
            },
        ];
    },
    {immediate: true}
);

const getTitle = computed(() => {
    return props.title;
});
</script>

<template>
    <section>
        <v-tabs v-model="tab" class="bg-grey-lighten-3 mb-2" align-tabs="center">
            <v-tab value="chart" class="text-body-2">Chart</v-tab>
            <v-tab value="table" class="text-body-2">Table</v-tab>
        </v-tabs>

        <v-tabs-window v-model="tab">
            <v-tabs-window-item value="chart">
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
                    <VChart class="chart" :option="options" autoresize/>
                </div>
            </v-tabs-window-item>

            <v-tabs-window-item value="table">
                <v-row justify="center" dense>
                    <v-col cols="12" class="text-center mt-1">
                        <h5 class="font-weight-bold d-flex align-center justify-center">
                            {{ getTitle }}
                        </h5>
                        <p class="text-muted text-xs">{{ subtitle }}</p>
                    </v-col>
                </v-row>

                <v-data-table
                    :headers="headers"
                    :items="data"
                    density="compact"
                    v-model:page="page"
                    :items-per-page="data.length"
                    class="elevation-0 overflow-auto"
                    style="max-height: 350px;"
                >
                    <template v-slot:item.name="{ item }">
                        <div class="text-body-2 font-medium">
                            {{ item.user_name }}
                        </div>
                    </template>

                    <template v-slot:item.count="{ item }">
                        <v-chip size="small" class="text-sm" label style="color: #d3a188;">
                            {{
                                item.count
                            }}
                        </v-chip>
                    </template>

                    <template #bottom></template>
                </v-data-table>
            </v-tabs-window-item>
        </v-tabs-window>
    </section>
</template>

<style scoped>
.chart {
    width: 100% !important;
    height: 400px;
}
</style>
