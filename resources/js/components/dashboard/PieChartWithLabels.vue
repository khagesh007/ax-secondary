<script setup>
import {ref, watch} from 'vue';
import VChart from 'vue-echarts';

// Define props
const props = defineProps({
    title: {type: String, default: 'Referer of a Website'},
    subtitle: {type: String, default: 'Fake Data'},
    data: {type: Array, required: true},
    itemNameKey: {type: String, default: 'name'},
    itemValueKey: {type: String, default: 'count'},
    info: {type: Boolean, default: false},
    infoText: {type: String, default: "Information"},
});

// Define chart options
const options = ref({
    title: {
        text: '',
        subtext: '',
        left: 'center',
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
        trigger: 'item'
    },
    legend: {
        bottom: 0,
        left: 'center'
    },
    series: [
        {
            type: "pie",
            radius: ["30%", "55%"],
            // top: '10%',
            avoidLabelOverlap: true,
            minAngle: 10, // Ensures small slices are visible
            padAngle: 2,
            itemStyle: {
                borderRadius: 5
            },
            labelLine: {
                show: true
            },
        },
    ]
});

const getLabel = (key) => {
    //remove id if exists
    key = key.replace(/_id/g, '')
    return key.split('_').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

// Watch for prop changes and update the chart dynamically
// watch(props, () => {
//     options.value.title.text = props.title;
//     options.value.title.subtext = props.subtitle;
//
//     // Use count directly as the value for the pie chart data (no transformation needed)
//     options.value.series[0].data = props.data?.map(item => ({
//         value: item[props.itemValueKey],
//         name: item[props.itemNameKey]
//     }));
// }, {immediate: true});
watch(props, () => {
    options.value.title.text = props.title;
    options.value.title.subtext = props.subtitle;

    // Compute total sum for percentage calculation
    const total = props.data.reduce((sum, item) => sum + item[props.itemValueKey], 0);

    options.value.series[0].data = props.data?.map(item => {
        const percentage = ((item[props.itemValueKey] / total) * 100).toFixed(1); // One decimal place
        return {
            value: item[props.itemValueKey],
            name: item[props.itemNameKey], // Legend should only show the name
            percentage: percentage // Store percentage separately
        };
    });

    // Ensure labels display name with percentage
    options.value.series[0].label = {
        show: true,
        formatter: ({ data }) => `${data.name} (${data.percentage}%)` // Only show percentage in labels
    };

    // Legend will automatically pick `name`, so it remains clean
}, {immediate: true});

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
        <VChart class="pie-chart" :option="options" autoresize/>
    </div>
</template>

<style scoped>
.pie-chart {
    width: 100% !important;
    height: 400px;
}
</style>
