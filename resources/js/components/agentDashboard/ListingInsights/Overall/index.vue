<script setup>

import Barchart from "../../../dashboard/BarChart.vue";
import PieChartWithLabels from "../../../dashboard/PieChartWithLabels.vue";
import {computed, ref} from "vue";
import PieChart from "../../../dashboard/PieChart.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
})

// Listings by Publishing
const publishingData = computed(() => [
    {value: props.data?.insights?.listing_counts_by_publish_status?.published || 0, name: "Published"},
    {value: props.data?.insights?.listing_counts_by_publish_status?.unpublished || 0, name: "Unpublished"},
    {value: props.data?.insights?.listing_counts_by_publish_status?.republished || 0, name: "Republished"},
]);

const categoryData = computed(() =>
    [
        {value: props.data?.insights?.listing_counts_by_category?.commercial || 0, name: "Commercial"},
        {value: props.data?.insights?.listing_counts_by_category?.residential || 0, name: "Residential"},
    ]
);

</script>

<template>
    <v-row>


        <v-col cols="12" md="3">
            <v-card height="100%" variant="flat" border>
                <v-card-text>
                    <PieChart
                        info
                        info-text="This chart illustrates the distribution of listings based on their publishing status, categorizing them as Published, Relisted, or Unpublished. The data is filtered according to the selected date range in the CRM."
                        title="Published Listings"
                        subtitle="Listings by Published, Relisted, and Unpublished Status"
                        :data="publishingData"
                    />
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" md="3">
            <v-card height="100%" variant="flat" border>
                <v-card-text>
                    <PieChart
                        info
                        info-text="This chart illustrates the distribution of listings based on different property categories, providing insights into the types of properties listed. The data is filtered according to the selected date range in the CRM."
                        title="Listings Category"
                        subtitle="Listings by Commercial and Residential Categories"
                        :data="categoryData"
                    />
                </v-card-text>
            </v-card>
        </v-col>

        <!--        <v-col cols="12" md="6">
                    <v-card variant="flat" border>
                        <v-card-text class="p-0">
                            <Barchart
                                info
                                info-text="This chart visualizes the distribution of listings based on their status, helping to identify how many listings are in each status such as Published, Unpublished, Draft, Archived and Pending."
                                title="Listings by Status"
                                subtitle="Distribution of listings by status"
                                :data="data?.listing_counts_by_status"
                            />
                        </v-card-text>
                    </v-card>
                </v-col>-->

    </v-row>
</template>

<style scoped>

</style>
