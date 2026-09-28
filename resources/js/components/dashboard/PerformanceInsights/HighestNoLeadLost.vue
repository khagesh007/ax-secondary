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
                title="Leads Lost"
                subtitle="Top 10 Agents with the Highest Number of Leads lost due to not actioning"
                :data="data"
                item-value-key="lead_count"
                info
                info-text="This bar chart represents the top 10 agents with the highest number of Leads lost due to not actioning, highlighting non-actioned leads based on the selected duration."
            />
        </v-card-text>
    </v-card>
</template>

<style scoped>

</style>
