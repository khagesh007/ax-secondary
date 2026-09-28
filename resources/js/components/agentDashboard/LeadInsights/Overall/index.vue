<script setup>
import {onMounted, ref} from "vue";
import StackedBarChart from "../../../dashboard/StackedBarChart.vue";

import PieChartWithLabels from "../../../dashboard/PieChartWithLabels.vue";
import StackedBarChart2 from "../../../dashboard/StackedBarChart2.vue";

const source_wise_lead_data = ref([]);
const source_medium_wise_lead_data = ref([]);
const props = defineProps({
    data: {
        type: Object,
        required: true
    }
})

function getCounterSumByName(name) {
    let source_wise_lead_count = props.data?.source_wise_lead_count;
    if (source_wise_lead_count) {
        const foundItem = source_wise_lead_count.find(item => item.name === name);
        if (foundItem) {
            return foundItem.count;
        } else {
            console.error("Name not found in source_wise_lead_count");
            return 0;
        }
    } else {
        console.error("source_wise_lead_count is undefined or null");
        return 0;
    }
}

const formatSourceWiseData = (data) => {
    if (!data) {
        source_wise_lead_data.value = [];
        return;
    }

    const formattedData = data.map(item => {
        let formattedName = item.name.replace('_count', '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        formattedName = formattedName === 'Pf' ? 'Property Finder' : formattedName;

        return {
            count: item.count,
            name: formattedName
        };
    });

    source_wise_lead_data.value = formattedData;

};
const formatSourceWiseMediumData = (data) => {
    if (!data) {
        source_medium_wise_lead_data.value = [];
        return;
    }

    const formattedData = data.map(item => {
        const statuses = [
            { name: "email", count: 0 },
            { name: "whatsapp", count: 0 },
            { name: "call", count: 0 },
            { name: "other", count: 0 },
        ];
        //loop through item.counter and update statuses array
        for (const [key, value] of Object.entries(item.counter)) {
            const status = statuses.find(status => status.name === key);
            if (status) {
                status.count = value;
            }
        }

        return {
            agent_name: item.name,
            statuses: statuses
        };
    });

    source_medium_wise_lead_data.value = formattedData;

};



onMounted(() => {

    let sourceWiseData = props.data?.source_wise_lead_count;
    formatSourceWiseData(sourceWiseData);
    let sourceWiseMediumLeadData = props.data?.overview;
    formatSourceWiseMediumData(sourceWiseMediumLeadData);
})

const portals = ref([
    {
        name: "Property Finder",
        url: 'https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644265820230215063058.png'
    },
    {
        name: "Bayut",
        url: 'https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644259820230215062958.png'
    },
    {
        name: "Dubizzle",
        url: 'https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644264020230215063040.png'
    },
    {
        name: "Other",
        url: ''
    }
]);

</script>

<template>
    <v-row>
        <v-col cols="12" md="3">
            <v-card color="success" variant="tonal">
                <v-card-text>
                    <div class="d-flex align-center gap-2">
                        <v-img class="flex-0-0" width="20" height="20" :src="portals[0].url"></v-img>
                        <span class="text-caption text-grey-darken-1 font-weight-bold">Property Finder</span>
                    </div>
                    <h6 class="text-h4 font-weight-bold mt-1 mb-0">
                        {{ getCounterSumByName('pf_count') }}
                    </h6>
                </v-card-text>
            </v-card>
        </v-col>


        <v-col cols="12" md="3">
            <v-card color="info" variant="tonal">
                <v-card-text>
                    <div class="d-flex align-center gap-2">
                        <v-img class="flex-0-0" width="20" height="20" :src="portals[1].url"></v-img>
                        <span class="text-caption text-grey-darken-1 font-weight-bold">Bayut</span>
                    </div>
                    <h6 class="text-h4 font-weight-bold mt-1 mb-0">
                        {{ getCounterSumByName('bayut_count') }}
                    </h6>
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" md="3">
            <v-card color="warning" variant="tonal">
                <v-card-text>
                    <div class="d-flex align-center gap-2">
                        <v-img class="flex-0-0" width="20" height="20" :src="portals[2].url"></v-img>
                        <span class="text-caption text-grey-darken-1 font-weight-bold">Dubizzle</span>
                    </div>
                    <h6 class="text-h4 font-weight-bold mt-1 mb-0">
                        {{ getCounterSumByName('dubizzle_count') }}
                    </h6>
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" md="3">
            <v-card color="secondary" variant="tonal">
                <v-card-text>
                    <div class="d-flex align-center gap-2">
                        <!--                        <v-img class="flex-0-0" width="20" height="20" :src="portals[1].url"></v-img>-->
                        <span class="text-caption text-grey-darken-1 font-weight-bold">Other</span>
                    </div>
                    <h6 class="text-h4 font-weight-bold mt-1 mb-0">
                        {{ getCounterSumByName('other_count') }}
                    </h6>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" md="6">
            <v-card variant="flat" border>
                <v-card-text class="p-0">

                    <PieChartWithLabels
                        title="Source Wise Lead Count"
                        subtitle="Distribution of leads by source"
                        info
                        info-text="This chart illustrates the distribution of leads based on their source, including Property Finder, Bayut, Dubizzle, and other platforms. The data is filtered according to the selected date range in the CRM."
                        :data="source_wise_lead_data"
                    />
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" md="6">
            <v-card variant="flat" border>
                <v-card-text class="p-0">
                    <PieChartWithLabels
                        title="Medium Wise Lead Count"
                        subtitle="Distribution of leads by medium"
                        info
                        info-text="This chart displays the distribution of leads based on the medium through which they were received, providing insights into lead generation trends. The data is filtered according to the selected date range in the CRM."
                        :data="data?.medium_wise_lead_count"
                    />
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="6" md="6">
            <v-card variant="flat" border>
                <StackedBarChart
                    title="Leads by Status"
                    subtitle="Distribution of Leads by status"
                    info
                    info-text="This chart presents the distribution of leads categorized by their current status, helping to track the progression of leads from initial inquiry to conversion. The data is filtered according to the selected date range in the CRM."
                    :data="data?.lead_status_count_with_user"
                />
            </v-card>
        </v-col>

        <v-col cols="6" md="6">
            <v-card variant="flat" border>
                <StackedBarChart2
                    title="Leads by Medium & Source"
                    subtitle="Distribution of Leads by medium under a source"
                    info
                    info-text="This chart presents the distribution of leads categorized by their medium and respective sources. The data is filtered according to the selected date range in the CRM."
                    :data="source_medium_wise_lead_data"
                />
            </v-card>
        </v-col>
    </v-row>
</template>

<style scoped>

</style>
