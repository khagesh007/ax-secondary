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
                title="Days since last Deal"
                subtitle="Top 10 Agents - Number of Days without closed a Deal ( 2025) "
                :data="data"
                item-value-key="days_since_last_deal"
                info
                info-text="Here is a bar chart representing the top 10 agents and the number of days they did not close a deal in 2025"
            />
        </v-card-text>
    </v-card>
</template>

<style scoped>

</style>
