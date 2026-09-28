<template>
    <v-container>
        <div style="background: white; border-radius: 10px; padding: 20px" class="mb-4">

<!--                <h3>Good Morning, <span class="text-primary">Muhammad Uzair Sohail!</span></h3>-->
                <v-row align="center">
                    <v-col cols="12" md="3">
                        <VueDatePicker v-model="date" range :multi-calendars="{ solo: true }"
                            :preset-dates="presetDates" :enable-time-picker="false">
                            <template #preset-date-range-button="{ label, value, presetDate }">
                                <span role="button" :tabindex="0" @click="presetDate(value)"
                                    @keyup.enter.prevent="presetDate(value)" @keyup.space.prevent="presetDate(value)">
                                    {{ label }}
                                </span>
                            </template>
                        </VueDatePicker>
                    </v-col>
                    <v-col cols="12" md="3">
                            <v-autocomplete
                                v-model="department"
                                @update:model-value="callAllApis"
                                :items="departments.data"
                                :loading="departments.loading"
                                item-title="name"
                                item-value="value"
                                label="Select Department"
                                variant="outlined"
                                class="bg-white"
                                density="compact"
                                hide-details
                            ></v-autocomplete>
                        </v-col>
                    <v-col cols="12" lg="3" md="3">
<!--                        <UsersAutocomplete-->
<!--                            :filters="filters"-->
<!--                            :model="filters.user_id || null"-->
<!--                            label="Select Agent"-->
<!--                            return-object-->
<!--                            @update:model="(value) => filters.user_id = value"-->
<!--                            hide-details-->
<!--                        />-->
                    </v-col>

                    <v-col cols="12" md="3">
                       <router-link to="/form" class="btn btn-ax-color float-end">Manage Pipelines</router-link>
                        </v-col>

                </v-row>

        </div>

        <v-row>
            <!-- Summary Widgets -->

            <v-col cols="4">
                <v-tooltip text="Total Sales Value of pipelines in Active status">
                    <template #activator="{ props }">
                        <v-card v-bind="props" class="pa-4">
                            <v-card-title>Active</v-card-title>
                            <v-card-text class="">
                                <div class="text-h5 mb-1">
                                    {{ numberFormat(overview.active_deal_value, false) }}
                                </div>
                                <div class="text-body-2 text-medium-emphasis">
                                    Comm. {{ numberFormat(overview.active_deal_commission, false) }}
                                    <span class="text-primary ml-1">({{overview.active_pipeline_commission_percentage || 0}}%)</span>
                                </div>
                            </v-card-text>
                        </v-card>
                    </template>
                </v-tooltip>
            </v-col>
            <v-col cols="4">
                <v-tooltip text="Total Sales Value of pipelines in Won status">
                    <template #activator="{ props }">
                        <v-card v-bind="props" class="pa-4">
                            <v-card-title>Won</v-card-title>
                            <v-card-text class="">
                                <div class="text-h5 mb-1">
                                    {{ numberFormat(overview.won_deal_value, false) }}
                                </div>
                                <div class="text-body-2 text-medium-emphasis">
                                    Comm. {{ numberFormat(overview.won_deal_commission, false) }}
                                    <span class="text-success ml-1">({{overview.won_pipeline_commission_percentage}}%)</span>
                                </div>
                            </v-card-text>
                        </v-card>
                    </template>


                </v-tooltip>
            </v-col>
            <v-col cols="4">
                <v-tooltip text="Total Sales Value of pipelines in Lost status">
                    <template #activator="{ props }">
                        <v-card v-bind="props" class="pa-4">
                            <v-card-title>Lost</v-card-title>
                            <v-card-text class="">
                                <div class="text-h5 mb-1">
                                    {{ numberFormat(overview.lost_deal_value, false) }}
                                </div>
                                <div class="text-body-2 text-medium-emphasis">
                                    Comm. {{ numberFormat(overview.lost_deal_commission, false) }}
                                    <span class="text-danger ml-1">({{overview.lost_pipeline_commission_percentage}}%)</span>
                                </div>
                            </v-card-text>
                        </v-card>
                    </template>

                </v-tooltip>

            </v-col>

        </v-row>

        <!-- Charts Section -->
        <v-row>

            <v-col cols="6">
                <v-card>
                    <v-card-title>Pipelines for deal type</v-card-title>
                    <v-card-text v-if="pipelinesForDealTypes">
                        <v-tabs v-model="dealTypeTab" bg-color="transparent">
                            <v-tab value="count">Count</v-tab>
                            <v-tab value="deal-values">Sales Value</v-tab>
                            <v-tab value="commission">Commission</v-tab>
                        </v-tabs>

                        <v-tabs-window v-model="dealTypeTab">
                            <v-tabs-window-item value="count">
                                <PieChart title="Pipeline by deal types" subtitle="Pipeline by Pipeline type Sale/Rent" info
                                          info-text="This chart illustrates the distribution of pipelines by deal types across all deals. Only active pipelines are considered."
                                          :data="pipelinesForDealTypes"
                                            :colors="['#d4a188', '#e8ccbe']"
                                />
                            </v-tabs-window-item>

                            <v-tabs-window-item value="deal-values">
                                <PieChartNumbers title="Pipeline Sales Value by Pipeline types" subtitle="Pipeline Sales Value totals by Pipeline type Sale/Rent" info
                                          info-text="This chart illustrates the distribution of pipelines by deal types across all deals. Only active pipelines are considered."
                                          :data="dealValueForDealTypes"
                                                 :colors="['#d4a188', '#e8ccbe']"
                                />
                            </v-tabs-window-item>

                            <v-tabs-window-item value="commission">
                                <PieChartNumbers title="Commission From Sale/Rent" subtitle="Active Pipeline commission by Pipeline types" info
                                          info-text="This chart illustrates the distribution of commission by Pipeline types across pipelines. Only active pipelines are considered."
                                          :data="commissionForDealTypes"
                                                 :colors="['#d4a188', '#e8ccbe']"
                                />
                            </v-tabs-window-item>
                        </v-tabs-window>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="6">
                <v-card>
                    <v-card-title>Pipeline Facts <span class="fs-12" v-if="date[0]">({{start_date}} - {{end_date}})</span></v-card-title>
                    <v-card-text>
                        <table class="table table-striped table-bordered">
                            <tbody>
                            <tr
                                v-for="(item, index) in pipeline_facts"
                                :key="index"
                            >
                                <td>{{item.title}}</td>
                                <td>{{item.val}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>



        <v-row v-if="!isAgent">
            <v-col cols="12">
                <v-card>
                    <v-card-title>Team Sales Value</v-card-title>
                    <v-card-text>
                        <StackedBarChart3
                            title="Team wise Pipeline statics"
                            subtitle="Team wise Sales value distribution of Active deals"
                            info
                            info-text="This chart illustrates the distribution of pipeline statuses across all agents."
                            :data="group_deal_values"
                            :colors="['#d4a188', '#e8ccbe']" />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Table Section -->
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>Agents Pipeline Statics</v-card-title>

                    <v-card-text v-if="user_deal_values">
                        <v-tabs v-model="agentStaticsTab" bg-color="transparent">
                            <v-tab value="deal-values">Deal Values</v-tab>
                            <v-tab value="count">Count</v-tab>

                        </v-tabs>

                        <v-tabs-window v-model="agentStaticsTab">
                            <v-tabs-window-item value="deal-values">
                                <StackedBarChart2
                                    title="Agent Deal Values"
                                    subtitle="Pipeline agent deal values distribution"
                                    info
                                    info-text="This chart illustrates the distribution of pipeline statuses across all agents."
                                    :data="user_deal_values"
                                    :colors='["#d4a188", "#91cc75", "#fac858", "#ee6666", "#73c0de", "#3ba272", "#fc8452", "#9a60b4"]' />
                            </v-tabs-window-item>
                            <v-tabs-window-item value="count">
                                <StackedBarChart2
                                    title="Agent Deal Counts"
                                    subtitle="Pipeline agent deal count distribution"
                                    info
                                    info-text="Counts of deals handled by each agent across all pipelines."
                                    :data="user_deal_counts"
                                    :colors='["#d4a188", "#91cc75", "#fac858", "#ee6666", "#73c0de", "#3ba272", "#fc8452", "#9a60b4"]' />
                            </v-tabs-window-item>


                        </v-tabs-window>
                    </v-card-text>

                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>Agents Status Wise Pipelines</v-card-title>
                    <v-card-text>
                        <StackedBarChart2
                            title="Agent Wise Pipelines"
                            subtitle="Pipeline status distribution"
                            info
                            info-text="This chart illustrates the distribution of pipeline statuses across all agents."
                            :data="user_status_data"
                            :colors="['#5470c6', '#91cc75', '#fac858', '#ee6666', '#73c0de', '#3ba272', '#fc8452', '#9a60b4']" />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="6">
                <v-card>
                    <v-card-title>Pipeline By Priorities</v-card-title>
                    <v-card-text>
                        <PieChart title="Pipelines By Priority" subtitle="Pipeline priority distribution" info
                                  info-text="This chart illustrates the distribution of pipeline statuses across all deals."
                                  :data="piplineByPriorityData" />
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="6">
                <v-card>
                    <v-card-title>Priority Levels</v-card-title>
                    <v-card-text>
                        <StackedBarChartSimple
                            title="Pipeline By Priority"
                            subtitle="Pipeline status distribution"
                            info
                            info-text="This chart illustrates the distribution of pipeline statuses across all deals."
                            :data="priority_status_data"
                            :colors="['#5470c6', '#91cc75', '#fac858', '#ee6666', '#73c0de', '#3ba272', '#fc8452', '#9a60b4']" />
                    </v-card-text>
                </v-card>
            </v-col>

        </v-row>
        <v-row>
            <v-col cols="6">
                <v-card>
                    <v-card-title>Pipeline Status Distribution</v-card-title>
                    <v-card-text v-if="piplineStatusData">
                        <PieChart title="Pipeline Status" subtitle="Pipeline status distribution" info
                                  info-text="This chart illustrates the distribution of pipeline statuses across all deals."
                                  :data="piplineStatusData" />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import {onBeforeMount, onMounted, ref, watch} from "vue";
import axios from 'axios';
import PieChartWithLabels from "../../components/dashboard/PieChartWithLabels.vue";
import PieChart from "../../components/dashboard/PieChart.vue";
import StackedBarChartSimple from "../../components/dashboard/StackedBarChartSimple.vue";
import StackedBarChart2 from "../../components/dashboard/StackedBarChart2.vue";
import StackedBarChart3 from "../../components/dashboard/StackedBarChart3.vue";
import PieChartNumbers from "../../components/dashboard/PieChartNumbers.vue";
import UsersAutocomplete from "../../components/common/UsersAutocomplete.vue";
const totalPipelines = ref(100);
const activePipelines = ref(60);
const wonPipelines = ref(30);
const lostPipelines = ref(10);
const agentStaticsTab = ref('deal-values')
const isAgent = ref(false);
const dealTypeTab = ref('count');
const departments = ref({
    data: [],
    loading: true,
})
const department = ref();
const overview = ref([]);
const date = ref([
    moment().startOf("month").format("YYYY-MM-DD"),
    moment().endOf("month").format("YYYY-MM-DD"),
]);
const presetDates = [
    { label: "Today", value: [moment().format("YYYY-MM-DD"), moment().format("YYYY-MM-DD")] },
    {
        label: "Yesterday",
        value: [moment().subtract(1, "day").format("YYYY-MM-DD"), moment().subtract(1, "day").format("YYYY-MM-DD")]
    },
    {
        label: "Last 7 Days",
        value: [moment().subtract(7, "days").format("YYYY-MM-DD"), moment().subtract(1, "day").format("YYYY-MM-DD")]
    },
    {
        label: "Last 30 Days",
        value: [moment().subtract(30, "days").format("YYYY-MM-DD"), moment().subtract(1, "day").format("YYYY-MM-DD")]
    },
    {
        label: "Last 60 Days",
        value: [moment().subtract(60, "days").format("YYYY-MM-DD"), moment().subtract(1, "day").format("YYYY-MM-DD")]
    },
    {
        label: "Last 90 Days",
        value: [moment().subtract(90, "days").format("YYYY-MM-DD"), moment().subtract(1, "day").format("YYYY-MM-DD")]
    },
    {
        label: "This Week",
        value: [moment().startOf("week").format("YYYY-MM-DD"), moment().endOf("week").format("YYYY-MM-DD")]
    },
    {
        label: "Last Week",
        value: [moment().subtract(1, "week").startOf("week").format("YYYY-MM-DD"), moment().subtract(1, "week").endOf("week").format("YYYY-MM-DD")]
    },
    {
        label: "This Month",
        value: [moment().startOf("month").format("YYYY-MM-DD"), moment().endOf("month").format("YYYY-MM-DD")]
    },
    {
        label: "Last Month",
        value: [moment().subtract(1, "month").startOf("month").format("YYYY-MM-DD"), moment().subtract(1, "month").endOf("month").format("YYYY-MM-DD")]
    },
    {
        label: "Last 3 Months",
        value: [moment().subtract(3, "month").startOf("month").format("YYYY-MM-DD"), moment().subtract(1, "month").endOf("month").format("YYYY-MM-DD")]
    },
    {
        label: "Last 6 Months",
        value: [moment().subtract(6, "month").startOf("month").format("YYYY-MM-DD"), moment().subtract(1, "month").endOf("month").format("YYYY-MM-DD")]
    },
    {
        label: "This Year",
        value: [moment().startOf("year").format("YYYY-MM-DD"), moment().endOf("year").format("YYYY-MM-DD")]
    },
    {
        label: "Last Year",
        value: [moment().subtract(1, "year").startOf("year").format("YYYY-MM-DD"), moment().subtract(1, "year").endOf("year").format("YYYY-MM-DD")]
    },
];
const piplineStatusData = ref([]);
const piplineByPriorityData = ref([]);
const priority_status_data = ref([]);
const user_status_data = ref([]);
const user_deal_values = ref([]);
const user_deal_counts = ref([]);
const pipeline_facts = ref([]);
const group_deal_values = ref([]);
const pipelinesForDealTypes = ref([]);
const extra_data = ref([]);
const dealValueForDealTypes = ref([
    { name: "Sale", value: 0 },
    { name: "Rent", value: 0 },
]);
const commissionForDealTypes = ref([
    { name: "Sale", value: 0 },
    { name: "Rent", value: 0 },
]);
const start_date = ref(null);
const end_date = ref(null);


const params = ref({
    tab: "overall",
    date: date.value,
    start_date: date.value[0],
    end_date: date.value[1],
    group: department.value,
})


const getOverview = async () => {
    params.value.tab = "overall";
    let response = await axios.get('/pipeline/report/landing', { params: params.value })
    let data = response?.data?.data || {};
    overview.value = data?.overview || {};
    const rawOverview = data?.overview || {}
    overview.value = {
        ...rawOverview,
        active_pipeline_commission_percentage: calculatePercentage(rawOverview.active_deal_value, rawOverview.active_deal_commission),
        won_pipeline_commission_percentage: calculatePercentage(rawOverview.won_deal_value, rawOverview.won_deal_commission),
        lost_pipeline_commission_percentage: calculatePercentage(rawOverview.lost_deal_value, rawOverview.lost_deal_commission)
    };
    piplineStatusData.value = data?.status_data || [];
    pipelinesForDealTypes.value = [
        { name: "Sale", value: overview.value.sale_pipelines || 0 },
        { name: "Rent", value: overview.value.rent_pipelines || 0 },

    ];
    dealValueForDealTypes.value = [
        { name: "Sale", value: overview.value.sale_deal_value || 0 },
        { name: "Rent", value: overview.value.rent_deal_value || 0 },
    ];

    // piplineByPriorityData.value = data?.priority_data || [];
    // priority_status_data.value = data?.priority_status_data || [];
    pipeline_facts.value = [
        { title: "Total Pipelines", val: overview.value.total || 0 },
        { title: "Closing", val: overview.value.closing || 0 },
        { title: "Closed (won)", val: overview.value.closed || 0 },
        { title: "Total Sales Value", val: numberFormat(overview.value?.total_deal_value) || 0 },
        { title: "Active Sales Value", val: numberFormat(overview.value?.active_deal_value) || 0 },
        { title: "Won Sales Value", val: numberFormat(overview.value?.won_deal_value) || 0 },
        { title: "Lost Sales Value", val: numberFormat(overview.value?.lost_deal_value) || 0 },
        { title: "Commission Value (Won)", val: (numberFormat(overview.value?.commission_value) || 0 ) +' ('+ overview.value?.commission_percentage + '%)' },
        { title: "Commission Value (Lost)", val: (numberFormat(overview.value?.lost_deal_commission) || 0 ) +' ('+ overview.value?.lost_pipeline_commission_percentage + '%)' },

    ];
}
const getUserData = async () => {

    params.value.tab = "user_based_data";
    let response = await axios.get('/pipeline/report/landing', { params: params.value })
    let data = response?.data?.data || {};
    user_status_data.value = data?.user_data_by_status || [];
    //remove user_status_data if total is 0
    user_status_data.value = user_status_data.value.filter(user => user.total > 0);

    user_deal_values.value = data?.user_deal_values || [];

    user_deal_values.value = user_deal_values.value.filter(user => user.total > 0);

    user_deal_counts.value = data?.user_deal_counts || [];
    user_deal_counts.value = user_deal_counts.value.filter(user => user.total > 0);

}
const getTeamData = async () => {

    params.value.tab = "team_based_data";
    let response = await axios.get('/pipeline/report/landing', { params: params.value })
    let data = response?.data?.data || {};
    group_deal_values.value = data?.group_deal_values || [];


}
const getExtraData = async () => {

    params.value.tab = "extra_data";
    let response = await axios.get('/pipeline/report/landing', { params: params.value })
    extra_data.value = response?.data?.data || {};
    piplineByPriorityData.value = extra_data.value?.priority_data || [];
    priority_status_data.value = extra_data.value?.priority_status_data || [];
    commissionForDealTypes.value = [
        { name: "Sale", value: extra_data.value.sale_rent_commission?.sale || 0 },
        { name: "Rent", value: extra_data.value.sale_rent_commission?.rent || 0 },
    ];


}
const getDepartments = async () => {
    departments.value.loading = true;

    try {
        const {data} = await axios.get("/reporting/dashboard/get-departments");
        if (data.data) {
            departments.value.data = data.data;
            isAgent.value = data.is_agent || false;
            if (data.data.length > 0) {
                department.value = data.data[0].value;
                params.value.group = department.value;
               await callAllApis();
            }
            if(isAgent.value){
                department.value = 'secondary';
                await callAllApis();
            }
        }
    } catch (error) {
        console.error(error);
    } finally {
        departments.value.loading = false;
    }
};
watch(date, async (newVal) => {
   //only run when departments.value.data.length > 0
    if (departments.value.data.length === 0) {
        return;
    }
    params.value.start_date = newVal[0];
    params.value.end_date = newVal[1];
    params.value.group = department.value;
    //put formatted date in start_date and end_date
    start_date.value = moment(newVal[0]).format("DD-MM-YYYY");
    end_date.value = moment(newVal[1]).format("DD-MM-YYYY");
    await getOverview();
    await getUserData();
    await getTeamData();
    await getExtraData();
}, { immediate: true });

const numberFormat = (value) => {
    if (value === null || value === undefined) {
        return '';
    }
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'AED',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};
const callAllApis = async () => {
    params.value.group = department.value;
    await getOverview();
    await getUserData();
    await getTeamData();
    await getExtraData();
};
const calculatePercentage = (value1, value2) => {
    if (!value1 || !value2) return 0;
    return ((Number(value2) / Number(value1)) * 100).toFixed(2);
}
onBeforeMount(async () => {
    await getDepartments();
});
onMounted(async () => {
    start_date.value = moment(date.value[0]).format("DD-MM-YYYY");
    end_date.value = moment(date.value[1]).format("DD-MM-YYYY");
});
</script>

<style scoped>
.v-card {
    margin-bottom: 20px;
}
.btn-ax-color {
    background: #d3a188 !important;
}
</style>
