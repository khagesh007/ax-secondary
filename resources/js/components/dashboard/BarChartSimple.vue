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
import { computed } from "vue";
import VChart from "vue-echarts";

const props = defineProps({
  title: String,
  subtitle: String,
  data: {
    type: Array,
    default: () => [],
  },
  color: {
    type: String,
    default: "#d4a188",
  },
  info: { type: Boolean, default: false },
  infoText: { type: String, default: "Information" },
});

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
      const item = params[0];
      return `<div><strong>${item.axisValue}</strong><hr class='my-2' />
        <div class='d-flex align-center gap-2'>
          <span style='display: inline-block; width: 10px; height: 10px; background: ${item.color}; border-radius: 50%;'></span>
          <span style='font-size: 12px;'>Count: <strong>${item.value}</strong></span>
        </div></div>`;
    },
  },
  grid: {
    left: 50,
    right: 50,
    top: 80,
    bottom: 100,
  },
  xAxis: {
    type: "category",
    data: props.data.map(d => d.date),
    axisLabel: {
      fontSize: 12,
    },
  },
  yAxis: {
    name: "Count",
    type: "value",
  },
  series: [
    {
      name: "Count",
      type: "bar",
      data: props.data.map(d => d.count),
      itemStyle: {
        color: props.color,
      },
    },
  ],
}));
</script>

<style scoped>
.chart {
  width: 100% !important;
  height: 400px;
}
</style>

