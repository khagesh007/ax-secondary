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
                title="Account Suspension Details"
                subtitle="Top 10 Agents - Number of time accounts suspended due to unactioned leads. "
                :data="data"
                item-value-key="suspension_count"
                info
                info-text='Here is the updated bar chart showing the number of times the top 10 agents had their accounts suspended due to unactioned leads in the "New Status." for more than 24 hours.'
            />
        </v-card-text>
    </v-card>
</template>

<style scoped>

</style>
