<script setup>

import Barchart from "../BarChart.vue";
import {ref} from "vue";

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
})

const tab = ref('overall');

</script>

<template>

    <v-tabs v-model="tab">
        <v-tab value="property-finder" color="primary" class="text-body-2">Property Finder</v-tab>
        <v-tab value="bayut" color="primary" class="text-body-2">Bayut</v-tab>
        <v-tab value="dubizzle" color="primary" class="text-body-2">Dubizzle</v-tab>
    </v-tabs>

    <v-tabs-window v-model="tab">

        <v-tabs-window-item value="property-finder" eager>
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <Barchart
                        title="Highest Number of Quality Score ( Property Finder )"
                        subtitle="Top 10 Agents with highest number of Quality Score ( Property Finder )"
                        :data="data?.property_finder_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart displays the top 10 agents with the highest number of quality scores on Property Finder, providing a view of agent performance based on quality metrics on this platform."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="bayut" eager>
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <Barchart
                        title="Highest Number of Quality Score ( Bayut )"
                        subtitle="Top 10 Agents with highest number of Quality Score ( Bayut )"
                        :data="data?.bayut_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart showcases the top 10 agents with the highest number of quality scores on Bayut, giving insights into the quality of agent performance on this platform."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="dubizzle" eager>
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <Barchart
                        title="Highest Number of Quality Score ( Dubizzle )"
                        subtitle="Top 10 Agents with highest number of Quality Score ( Dubizzle )"
                        :data="data?.dubizzle_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart highlights the top 10 agents with the highest number of quality scores on Dubizzle, reflecting the quality of agent performance on this platform."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

    </v-tabs-window>

</template>

<style scoped>

</style>
