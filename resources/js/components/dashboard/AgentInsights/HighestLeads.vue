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
                        title="Highest Number of Leads"
                        subtitle="Top 10 Agents with highest number of leads"
                        :data="data?.overall_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart displays the top 10 agents with the highest number of leads across all sources, providing insights into overall lead generation performance."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="property-finder" eager>
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <Barchart
                        title="Highest Number of Leads ( Property Finder )"
                        subtitle="Top 10 Agents with highest number of leads ( Property Finder )"
                        :data="data?.property_finder_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart highlights the top 10 agents who have generated the highest number of leads specifically from Property Finder."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="bayut" eager>
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <Barchart
                        title="Highest Number of Leads ( Bayut )"
                        subtitle="Top 10 Agents with highest number of leads ( Bayut )"
                        :data="data?.bayut_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart presents the top 10 agents with the highest number of leads acquired from Bayut, offering a breakdown of performance on this platform."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

        <v-tabs-window-item value="dubizzle" eager>
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <Barchart
                        title="Highest Number of Leads ( Dubizzle )"
                        subtitle="Top 10 Agents with highest number of leads ( Dubizzle )"
                        :data="data?.dubizzle_counts"
                        itemNameKey="user_name"
                        info
                        info-text="This chart showcases the top 10 agents with the highest number of leads sourced from Dubizzle, helping track performance on this platform."
                    />
                </v-card-text>
            </v-card>
        </v-tabs-window-item>

    </v-tabs-window>

</template>

<style scoped>

</style>
