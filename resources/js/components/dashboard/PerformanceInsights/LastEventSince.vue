<script setup>

import Barchart from "../BarChart.vue";

const props = defineProps({
    data: {
        type: Array,
        required: true
    }
})

</script>

<template>
    <v-card variant="flat" border>
        <v-card-text class="p-0">
            <Barchart
                title="Last Event Since"
                subtitle="Agents with highest number of days since last event"
                :data="data"
                item-value-key="days"
            />
        </v-card-text>
    </v-card>
</template>

<style scoped>

</style>
