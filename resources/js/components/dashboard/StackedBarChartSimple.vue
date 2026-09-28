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
    <VChart class="chart" :option="chartOptions" autoresize />
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import VChart from "vue-echarts";

const props = defineProps({
  title: String,
  subtitle: String,
  data: Object,
  colors: {
    type: Array,
    default: () => ["#5470c6", "#91cc75", "#fac858", "#ee6666", "#73c0de", "#3ba272", "#fc8452", "#9a60b4"],
  },
  info: { type: Boolean, default: false },
  infoText: { type: String, default: "Information" },
});

// Extract categories (Medium, High, Low) and statuses (Active, Won, Lost)
const categories = computed(() => Object.keys(props.data || {}));
const statuses = computed(() => {
  const firstCategory = Object.values(props.data || {})[0];
  return firstCategory ? Object.keys(firstCategory) : [];
});

// Chart options
const chartOptions = computed(() => ({
  title: {
    text: props.title,
    subtext: props.subtitle,
    left: "center",
    top: 10,
    textStyle: { fontSize: 16, fontWeight: "bold" },
  },
  tooltip: {
    trigger: "axis",
    axisPointer: { type: "shadow" },
    formatter: (params) => {
      const category = params[0].axisValue;
      let html = `<div><strong>${category}</strong><hr class="my-2" />`;

      params.forEach((item) => {
        html += `<div class="d-flex align-center gap-2">
          <span style="display: inline-block; width: 10px; height: 10px; background: ${item.color}; border-radius: 50%;"></span>
          <span style="font-size: 12px;">${item.seriesName}: <strong>${item.value}</strong></span>
        </div>`;
      });

      return html + `</div>`;
    },
  },
  legend: {
    bottom: 0,
    left: "center",
  },
  grid: {
    left: 50,
    right: 50,
    top: 80,
    bottom: 100,
  },
  xAxis: {
    type: "category",
    data: categories.value,
    axisLabel: {
      fontSize: 12,
    },
  },
  yAxis: {
    name: "Count",
    type: "value",
  },
  series: statuses.value.map((status, index) => ({
    name: status,
    type: "bar",
    stack: "total",
    data: categories.value.map(category => props.data[category][status] || 0),
    itemStyle: {
      color: props.colors[index % props.colors.length],
    },
  })),
}));
</script>

<style scoped>
.chart {
  width: 100% !important;
  height: 400px;
}
</style>
