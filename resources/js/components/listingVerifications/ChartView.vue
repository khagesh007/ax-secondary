<script setup>
import PieChart from "../dashboard/PieChart.vue";
import StackedBarChart from "../adminDashboard/StackedBarChart.vue";

const props = defineProps({
    data: Object,
});

</script>

<template>
    <v-row>
        <v-col cols="6">
            <v-card flat class="border position-relative">
                <v-menu open-on-hover max-width="300">
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
                            This refer to the overall number of property listings submitted for verifications to property finder. <br>
                        </v-card-text>
                    </v-card>
                </v-menu>

                <v-card-text>
                    <v-chip
                        variant="tonal"
                        color="success"
                        size="small"
                        label
                    >

                        <span class="font-weight-bold">Total Submissions</span>
                    </v-chip>
                    <h3 class="text-h3 font-weight-bold mt-1 mb-0">
                        {{ (data?.status_count || []).reduce((sum, item) => sum + item.count, 0) }}
                    </h3>

                    <v-row class="mt-2">
                        <v-col cols="6" v-for="item in data?.status_count || []">
                            <v-card class="pa-2 border" flat>
                                <span class="text-caption text-grey-darken-1 font-weight-bold">{{ item.name}}</span>
                                <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                    {{ item.count }}
                                </h6>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" md="6">
            <v-card height="100%" variant="flat" border>
                <v-card-text>
                    <PieChart
                        info
                        info-text="This chart illustrates the distribution of listings based on their status. The data is filtered according to the selected date range in the CRM."
                        title="Listing Verification Status"
                        :data="data?.status_count || []"
                    />
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" md="12">
            <StackedBarChart
                title="Listing Submissions"
                subtitle="Number of Submissions by User"
                :data="data?.user_and_status_count || []"
            />
        </v-col>
    </v-row>
</template>

<style scoped>
.chart {
    width: 100% !important;
    height: 600px;
}
</style>
