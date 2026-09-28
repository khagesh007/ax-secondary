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
                title="Incomplete Leads Overview"
                subtitle="Top 10 Agents with the Highest Number of Incomplete Leads"
                :data="data"
                item-value-key="leads_count"
                info
                info-text="This bar chart represents the top 10 agents with the highest number of incomplete leads, where essential details such as name or property information are missing. The data is based on active leads within the selected duration."
            />
        </v-card-text>
    </v-card>
</template>

<style scoped>

</style>
