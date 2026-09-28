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
        <v-tab value="overall" color="primary" class="text-body-2">Overall</v-tab>
        <v-tab value="property-finder" color="primary" class="text-body-2">Property Finder</v-tab>
        <v-tab value="bayut" color="primary" class="text-body-2">Bayut</v-tab>
        <v-tab value="dubizzle" color="primary" class="text-body-2">Dubizzle</v-tab>
    </v-tabs>

    <v-tabs-window v-model="tab">

        <v-tabs-window-item value="overall" eager>
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <Barchart
                        title="Highest Number of Listings"
                        subtitle="Top 10 Agents with highest number of listings"
                        :data="data?.overall_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart displays the top 10 agents with the highest number of listings across all platforms, providing a comprehensive view of agent performance in listing management."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="property-finder" eager>
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <Barchart
                        title="Highest Number of Listings ( Property Finder )"
                        subtitle="Top 10 Agents with highest number of listings ( Property Finder )"
                        :data="data?.property_finder_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart highlights the top 10 agents who have the highest number of listings specifically on Property Finder."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="bayut" eager>
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <Barchart
                        title="Highest Number of Listings ( Bayut )"
                        subtitle="Top 10 Agents with highest number of listings ( Bayut )"
                        :data="data?.bayut_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart presents the top 10 agents with the highest number of listings on Bayut, giving insights into agent performance on this platform."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="dubizzle" eager>
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <Barchart
                        title="Highest Number of Listings ( Dubizzle )"
                        subtitle="Top 10 Agents with highest number of listings ( Dubizzle )"
                        :data="data?.dubizzle_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart showcases the top 10 agents with the highest number of listings on Dubizzle, providing a detailed view of agent success on this platform."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

    </v-tabs-window>

</template>

<style scoped>

</style>
