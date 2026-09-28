<script setup>
import {ref, watch, computed} from "vue";
import VChart from "vue-echarts";

const props = defineProps({
    title: String,
    subtitle: String,
    icon: String, // Pass emoji or icon Unicode (e.g., "⭐")
    data: Array, // Pass pie chart data [{ value: 1000, name: "Standard" }]
    colors: Array, // Pass custom colors ["#45cb85", "#FB8B00", "#299cdb"]
    info: {type: Boolean, default: false},
    infoText: {type: String, default: "Information"},
});


const totalValue = computed(() =>
    props.data.reduce((sum, item) => sum + item.value, 0)
);

const chartOptions = ref({});

// Watch for changes in props and update chart options dynamically
watch(
    () => [props.title, props.subtitle, props.icon, props.data],
    () => {
        chartOptions.value = {
            color: props.colors || ["#5470c6", "#91cc75", "#fac858", "#ee6666", "#73c0de", "#3ba272", "#fc8452", "#9a60b4"], // Default colors
            title: {
                text: props.title,
                subtext: props.subtitle,
                left: "center",
                top: 0,
                textStyle: {
                    fontSize: 16,
                    fontWeight: "bold",
                    rich: {
                        icon: {
                            fontSize: 18 // Adjust icon size
                        }
                    }
                }
            },
            tooltip: {
                trigger: "item"
            },
            legend: {
                bottom: 0,
                left: "center"
            },
            series: [
                {
                    type: "pie",
                    radius: ["40%", "70%"],
                    avoidLabelOverlap: true,
                    minAngle: 10, // Ensures small slices are visible
                    padAngle: 2,
                    itemStyle: {
                        borderRadius: 5
                    },
                    label: {
                        show: true,
                        position: "inside",
                        color: "#fff",
                        fontSize: 14,
                        fontWeight: "bold",
                        formatter: "{c}" // Show count inside the chart
                    },
                    labelLine: {
                        show: false
                    },
                    data: props.data
                }
            ],
            graphic: {
                type: "text",
                left: "center",
                top: "center",
                style: {
                    text: `Total\n${totalValue.value}`,
                    textAlign: "center",
                    fontSize: 18,
                    fontWeight: "bold",
                    fill: "#333"
                }
            }
        };
    },
    {immediate: true}
);
</script>

<template>
    <div class="position-relative">
        <v-menu v-if="info" open-on-hover max-width="300">
            <template v-slot:activator="{ props }">
                <v-icon
                    v-bind="props"
                    style="position: absolute; top: 0; right: 0; z-index: 2"
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
        <v-chart class="chart" :option="chartOptions" autoresize/>
    </div>
</template>

<style scoped>
.chart {
    width: 100% !important;
    height: 400px;
}
</style>
