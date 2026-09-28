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

// Fixed colors for each source
const sourceColor = {
  'Property Finder': '#FF5733',
  'Bayut': '#3498DB',
  'Dubizzle': '#2ECC71',
  'Other': '#F1C40F',
};

// Sources: always take from props.data.sources (even if no leads)
const allSources = computed(() => props.data?.sources || []);

const sortedData = computed(() => {
  let list = props.data?.listing_lead_with_source || props.data;
  if (!Array.isArray(list) && typeof list === 'object' && list !== null) {
    list = Object.values(list);
  }

  if (Array.isArray(list)) {
    return [...list].sort((a, b) => b.total_leads - a.total_leads);
  }

  return [];
})

// Chart options
const chartOptions = ref({});

// Main update function
const updateChart = () => {
  chartOptions.value = {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      formatter: (params) => {
        let refNo = params[0]?.axisValue;
        let listing = sortedData.value.find(item => item.ref_no === refNo);
        if (!listing) return '';

        let tooltip = `<div style="display: flex; align-items: center;">
          <img src="${listing.image}" alt="Property Image" style="width: 60px; height: 40px; margin-right: 8px; border-radius: 5px;">
          <div>
            <b>Agent.: ${listing.agent_name}</b><br>
            <b>Location: ${listing.location}</b><br/>
            <b>${refNo}</b><br/>`;

        params.forEach(item => {
          tooltip += `${item.marker} ${item.seriesName}: <b>${item.data}</b><br/>`;
        });

        tooltip += `</div></div>`;
        return tooltip;
      },
    },
    legend: {
      data: allSources.value,
      bottom: 0,
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '10%',
      containLabel: true,
    },
    xAxis: {
      type: 'category',
      data: sortedData.value.map(item => item.ref_no),
      axisLabel: {
        rotate: 30,
        interval: 0,
      },
    },
    yAxis: {
      type: 'value',
      name: 'Leads',
      minInterval: 1,
    },
    dataZoom: [
      {
        type: 'slider',
        show: true,
        xAxisIndex: [0],
        start: 0,
        end: sortedData.value.length > 50 ? 5 : 100,
        bottom: 40,
      },
      {
        type: 'inside',
        zoomOnMouseWheel: false,
        moveOnMouseMove: false,
        xAxisIndex: [0],
        start: 0,
        end: 100,
      }
    ],
    series: allSources.value.map(source => ({
      name: source,
      type: 'bar',
      stack: 'total',
      barWidth: '50%',
      itemStyle: {
        color: sourceColor[source] || '#CCCCCC',
      },
      data: sortedData.value.map(item => item.leads[source] || 0),
    })),
  };
};

// Watch for props change
watch(() => props.data, updateChart, { immediate: true });
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
