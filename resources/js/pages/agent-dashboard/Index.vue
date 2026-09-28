<script setup>
import {nextTick, onMounted, ref, watch, computed} from "vue";
import moment from "moment";

import Piechart from "../../components/dashboard/PieChart.vue";
import Barchart from "../../components/dashboard/BarChart.vue";

import ListingOverall from "../../components/agentDashboard/ListingInsights/Overall/index.vue";
import ListingPropertyFinder from "../../components/agentDashboard/ListingInsights/PropertyFinder/index.vue";
import ListingBayut from "../../components/agentDashboard/ListingInsights/Bayut/index.vue";
import ListingDubizzle from "../../components/agentDashboard/ListingInsights/Dubizzle/index.vue";
import ListingOthers from "../../components/dashboard/ListingInsights/Others/index.vue";

import LeadOverall from "../../components/agentDashboard/LeadInsights/Overall/index.vue";
import LeadPropertyFinder from "../../components/dashboard/LeadInsights/PropertyFinder/index.vue";
import LeadBayut from "../../components/dashboard/LeadInsights/Bayut/index.vue";
import LeadDubizzle from "../../components/dashboard/LeadInsights/Dubizzle/index.vue";
import LeadOthers from "../../components/dashboard/LeadInsights/Others/index.vue";
import StackedBarChart from "../../components/dashboard/StackedBarChart.vue";
import HighestLeads from "../../components/dashboard/AgentInsights/HighestLeads.vue";
import HighestListings from "../../components/dashboard/AgentInsights/HighestListings.vue";
import HighestQualityScore from "../../components/dashboard/AgentInsights/HighestQualityScore.vue";
import LeadsInNewStatus from "../../components/dashboard/PerformanceInsights/LeadsInNewStatus.vue";
import IncompleteLeads from "../../components/dashboard/PerformanceInsights/IncompleteLeads.vue";
import SuspensionDetails from "../../components/dashboard/PerformanceInsights/SuspensionDetails.vue";
import DaysSinceLastDeal from "../../components/dashboard/PerformanceInsights/DaysSinceLastDeal.vue";
import LastEventSince from "../../components/dashboard/PerformanceInsights/LastEventSince.vue";

const date = ref([
    // '2025-01-01T00:00:00.000Z',
    // '2025-01-31T00:00:00.000Z'
    moment().startOf("month").format("YYYY-MM-DD"),
    moment().endOf("month").format("YYYY-MM-DD"),
]);

const presetDates = [
    {label: "Today", value: [moment().format("YYYY-MM-DD"), moment().format("YYYY-MM-DD")]},
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

const listingTab = ref("overall"); // Active tab

const departments = ref({
    data: [],
    loading: true,
})
const department = ref(null);
const is_inactive_agents = ref(false);
const is_archived_leads = ref(false);
const is_date_exempt = ref(false);
const logged_user = ref({})
const listings = ref({
    data: [],
    loading: true,
    insightsLoading: true,
});



const getListingData = async (loading = true) => {
    if (loading) {
        listings.value.loading = true;
    }

    let params = {
        tab: "overall",
        date: date.value,
        start_date: date.value[0],
        end_date: date.value[1],
        archivedLeads: is_archived_leads.value,
        exceptListingDateFilter: is_date_exempt.value,
    };

    try {
        const {data} = await axios.get(
            "/reporting/agent/dashboard/initial-request",
            {params: params}
        );
        if (data.data) {
            listings.value.data = data.data;
            logged_user.value = data.data.logged_user
        }
    } catch (error) {
        console.error(error);
    } finally {
        listings.value.loading = false;
    }
};

const getListingInsightsData = async () => {
    listings.value.insightsLoading = true;

    let params = {
        tab: "listing-insight",
        date: date.value,
        start_date: date.value[0],
        end_date: date.value[1],
        group: department.value,
        inactiveUsers: is_inactive_agents.value,
        archivedLeads: is_archived_leads.value,
        exceptListingDateFilter: is_date_exempt.value,
    };

    try {
        const {data} = await axios.get(
            "/reporting/agent/dashboard/initial-request",
            {params: params}
        );
        if (data.data) {
            listings.value.data.insights = data.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        listings.value.insightsLoading = false;
    }
};

const leads = ref({
    data: [],
    loading: true,
});

const getLeadData = async () => {
    leads.value.loading = true;

    let params = {
        tab: "lead-insight",
        date: date.value,
        start_date: date.value[0],
        end_date: date.value[1],
        archivedLeads: is_archived_leads.value,
        exceptListingDateFilter: is_date_exempt.value,
    };

    try {
        const {data} = await axios.get(
            "/reporting/agent/dashboard/initial-request",
            {params: params}
        );
        if (data.data) {
            leads.value.data = data.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        leads.value.loading = false;
    }
};

const agents = ref({
    data: [],
    loading: true,
    callInsightData: [],
    callInsightLoading: false,
});

const getAgentData = async () => {
    agents.value.loading = true;

    let params = {
        tab: "agent-insight",
        date: date.value,
        start_date: date.value[0],
        end_date: date.value[1],
        group: department.value,
        inactiveUsers: is_inactive_agents.value,
        archivedLeads: is_archived_leads.value,
        exceptListingDateFilter: is_date_exempt.value,
    };

    try {
        const {data} = await axios.get(
            "/reporting/dashboard/initial-request",
            {params: params}
        );
        if (data.data) {
            agents.value.data = data.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        agents.value.loading = false;
    }
};

const getCallLogData = async () => {
    agents.value.callInsightLoading = true;

    let params = {
        tab: "call-insight",
        date: date.value,
        start_date: date.value[0],
        end_date: date.value[1],
        group: department.value,
        inactiveUsers: is_inactive_agents.value,
        archivedLeads: is_archived_leads.value,
        exceptListingDateFilter: is_date_exempt.value,
    };

    try {
        const {data} = await axios.get(
            "/reporting/dashboard/initial-request",
            {params: params}
        );
        if (data.data) {
            agents.value.callInsightData = data.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        agents.value.callInsightLoading = false;
    }
};

const performance = ref({
    data: [],
    loading: true,
});

const getPerformanceData = async () => {
    performance.value.loading = true;

    let params = {
        tab: "performance-insight",
        date: date.value,
        start_date: date.value[0],
        end_date: date.value[1],
        group: department.value,
        inactiveUsers: is_inactive_agents.value,
        archivedLeads: is_archived_leads.value,
        exceptListingDateFilter: is_date_exempt.value,
    };

    try {
        const {data} = await axios.get(
            "/reporting/dashboard/initial-request",
            {params: params}
        );
        if (data.data) {
            performance.value.data = data.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        performance.value.loading = false;
    }
};

const activityInsight = ref({
    data: [],
    loading: true,
});

const getActivityInsight = async () => {
    activityInsight.value.loading = true;

    let params = {
        tab: "activity-insight",
        date: date.value,
        start_date: date.value[0],
        end_date: date.value[1],
        group: department.value,
        inactiveUsers: is_inactive_agents.value,
        archivedLeads: is_archived_leads.value,
        exceptListingDateFilter: is_date_exempt.value,
    };

    try {
        const {data} = await axios.get(
            "/reporting/dashboard/initial-request",
            {params: params}
        );
        if (data.data) {
            activityInsight.value.data = data.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        activityInsight.value.loading = false;
    }
};

const setAllLoading = (value) => {
    listings.value.loading = value;
    listings.value.insightsLoading = value;
    leads.value.loading = value;
    agents.value.loading = value;
    performance.value.loading = value;
    activityInsight.value.loading = value;
};

onMounted(async () => {
    await getListingData();
    await getListingInsightsData();
    await getLeadData();
    await getAgentData();
    await getCallLogData();
    await getPerformanceData();
    await getActivityInsight();
});

watch(date, async (newVal) => {
    setAllLoading(true);
    await getListingData();
    await getListingInsightsData();
    await getLeadData();
    await getAgentData();
    await getCallLogData();
    await getPerformanceData();
    await getActivityInsight();
});

const callAllApis = async () => {
    setAllLoading(true);
    await getListingData();
    await getListingInsightsData();
    await getLeadData();
    await getAgentData();
    await getCallLogData();
    await getPerformanceData();
    await getActivityInsight();
};

const getListingCount = (key) => computed(() => listings.value.data?.summary?.listings?.[key] || 0);
const getLeadsCount = (key) => computed(() => listings.value.data?.summary?.leads?.[key] || 0);
const getDealsCount = (key) => computed(() => listings.value.data?.summary?.deals?.[key] || 0);

const getHumanReadable = (value) => {
    return Number(value) > 1000000000 ? (Number(value) / 1000000000).toFixed(2) + 'B' : Number(value) > 1000000 ? (Number(value) / 1000000).toFixed(2) + 'M' : Number(value) > 1000 ? (Number(value) / 1000).toFixed(2) + 'K' : value
};

const formatCurrency = (value, decimal = 2) => {
    if (value == null || isNaN(value)) {
        return "-" // Fallback value if input is null or not a number
    }

    // Ensure the number is parsed and fixed to 2 decimal places
    const number = parseFloat(value)

    // Add comma formatting for thousands
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

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
])
const isEnabled = false;

</script>

<template>
    <v-app>
        <v-container fluid>



            <div style="background: white; border-radius: 10px; padding: 20px">
                <!--                <h3>Good Morning, <span class="text-primary">Muhammad Uzair Sohail!</span></h3>-->
                <h3>Welcome {{logged_user.name}} !</h3>
                <div>
                    <v-row align="center">
                        <v-col cols="12" md="3">
                            <VueDatePicker
                                v-model="date"
                                range
                                :multi-calendars="{ solo: true }"
                                :preset-dates="presetDates"
                                :enable-time-picker="false"
                            >
                                <template #preset-date-range-button="{ label, value, presetDate }">
                                    <span
                                        role="button"
                                        :tabindex="0"
                                        @click="presetDate(value)"
                                        @keyup.enter.prevent="presetDate(value)"
                                        @keyup.space.prevent="presetDate(value)"
                                    >
                                      {{ label }}
                                    </span>
                                </template>
                            </VueDatePicker>
                        </v-col>


                        <!-- <v-col cols="12" md="2">
                            <v-switch v-model="is_archived_leads" color="primary" hide-details>
                                <template #label>
                                    <span class="font-weight-medium text-body-2">Archived Leads</span>
                                </template>
                            </v-switch>
                        </v-col>
                        <v-col cols="12" md="2">
                            <v-switch v-model="is_date_exempt" color="primary" hide-details>
                                <template #label>
                                    <span class="font-weight-medium text-body-2">Exempt date filter to listings</span>
                                </template>
                            </v-switch>
                        </v-col> -->
                    </v-row>
                </div>
            </div>


            <v-card rounded="lg" variant="flat" v-if="!listings.loading" class="mt-5">
                <v-card-title>Overall Summary</v-card-title>
                <v-card-subtitle
                >Here's a concise summary of Listings, Leads, and Deals
                </v-card-subtitle
                >

                <v-row class="w-100 pa-4">
                    <v-col cols="4">
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
                                        Total Listings refer to the overall number of property listings published in our
                                        CRM across all platforms, including Property Finder, Bayut, Dubizzle, and other
                                        sources. <br>
                                        Note: The date range filter does not apply to the listing count.
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

                                    <span class="font-weight-bold"
                                    >Total Listings</span
                                    >
                                </v-chip>
                                <h3 class="text-h3 font-weight-bold mt-1 mb-0">
                                    {{ formatCurrency(getListingCount('total_listings').value) }}
                                </h3>

                                <v-row class="mt-2">
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span class="text-caption text-grey-darken-1 font-weight-bold">Property Finder</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ formatCurrency(getListingCount('total_property_finder').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span class="text-caption text-grey-darken-1 font-weight-bold">Bayut</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ formatCurrency(getListingCount('total_bayut').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span
                                                class="text-caption text-grey-darken-1 font-weight-bold">Dubizzle</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ formatCurrency(getListingCount('total_dubizzle').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span class="text-caption text-grey-darken-1 font-weight-bold">Other</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ formatCurrency(getListingCount('total_other').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="4">
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
                                        Total Leads represent the total number of inquiries recorded in our CRM from
                                        various platforms, including Property Finder, Bayut, Dubizzle, and other
                                        sources, based on the selected time duration.
                                    </v-card-text>
                                </v-card>
                            </v-menu>
                            <v-card-text>
                                <v-chip
                                    variant="tonal"
                                    color="info"
                                    size="small"
                                    label
                                >

                                    <span class="font-weight-bold"
                                    >Total Leads</span
                                    >
                                </v-chip>
                                <h3 class="text-h3 font-weight-bold mt-1 mb-0">
                                    {{ formatCurrency(getLeadsCount('total_leads').value) }}
                                </h3>

                                <v-row class="mt-2">
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span class="text-caption text-grey-darken-1 font-weight-bold">Property Finder</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ formatCurrency(getLeadsCount('total_property_finder').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span class="text-caption text-grey-darken-1 font-weight-bold">Bayut</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ formatCurrency(getLeadsCount('total_bayut').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span
                                                class="text-caption text-grey-darken-1 font-weight-bold">Dubizzle</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ formatCurrency(getLeadsCount('total_dubizzle').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span class="text-caption text-grey-darken-1 font-weight-bold">Other</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ formatCurrency(getLeadsCount('total_other').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="4">
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
                                        Total Deals Sales Value represents the total sales value from announced deals by
                                        the Secondary Department, including Secondary Sales, Primary Sales, Rentals, and
                                        other deal types, based on the selected time duration of announced deals.
                                    </v-card-text>
                                </v-card>
                            </v-menu>
                            <v-card-text>
                                <v-chip
                                    variant="tonal"
                                    color="warning"
                                    size="small"
                                    label
                                >

                                    <span class="font-weight-bold"
                                    >Total Deals Sales value</span
                                    >
                                </v-chip>
                                <h3 class="text-h3 font-weight-bold mt-1 mb-0">
                                    {{
                                        getHumanReadable(getDealsCount('total_sale_value').value)
                                    }}
                                </h3>

                                <v-row class="mt-2">
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span class="text-caption text-grey-darken-1 font-weight-bold">Secondary Sales</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{
                                                    getHumanReadable(getDealsCount('total_secondary_sales_sale_value').value)
                                                }}

                                            </h6>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span
                                                class="text-caption text-grey-darken-1 font-weight-bold">Primary</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ getHumanReadable(getDealsCount('total_primary_sale_value').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span class="text-caption text-grey-darken-1 font-weight-bold">Rental</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ getHumanReadable(getDealsCount('total_rental_sale_value').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-card class="pa-2 border" flat>
                                            <span class="text-caption text-grey-darken-1 font-weight-bold">Other</span>
                                            <h6 class="text-h6 font-weight-bold mt-1 mb-0">
                                                {{ getHumanReadable(getDealsCount('total_other_deals_value').value) }}
                                            </h6>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card>
            <v-card  v-if="!listings.loading" class="mt-5">
                <v-card-title>Listing Insights</v-card-title>
                <v-card-subtitle>Here are some insights about your listings.</v-card-subtitle>

                <v-tabs v-model="listingTab" content="align-center">
                    <v-tab value="overall" color="primary" class="text-body-2">Overall</v-tab>
                    <v-tab value="property-finder" color="primary" class="text-body-2 d-none">Property Finder</v-tab>
                    <v-tab value="bayut" color="primary" class="text-body-2">Bayut</v-tab>
                    <v-tab value="dubizzle" color="primary" class="text-body-2">Dubizzle</v-tab>
                    <!--                    <v-tab value="other" color="primary" class="text-body-2">Other</v-tab>-->
                </v-tabs>

                <v-container fluid>
                    <v-tabs-window v-model="listingTab">
                        <v-tabs-window-item value="overall" eager>
                            <ListingOverall :data="listings.data"/>

                            <v-row v-if="!listings.insightsLoading">
                                <v-col cols="12" md="12">
                                    <v-card variant="flat" border>
                                        <v-card-text class="p-0">
                                            <StackedBarChart
                                                info
                                                info-text="This chart displays the number of listings managed by each agent, categorized by their current status, such as Draft, Pending, Published, Unpublished, Archived, Refused, and Pocket. The data is filtered according to the selected date range in the CRM."
                                                title="Listings by Status"
                                                subtitle="Distribution of listings by status"
                                                :data="listings.data?.insights?.agent_listings_by_statuses"
                                            />
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-tabs-window-item>

                        <v-tabs-window-item value="property-finder" eager>
                            <ListingPropertyFinder
                                @get-listing-data="getListingData"
                                :data="listings.data?.property_finder"
                            />
                        </v-tabs-window-item>

                        <v-tabs-window-item value="bayut" eager>
                            <ListingBayut
                                @get-listing-data="getListingData"
                                :data="listings.data?.bayut"
                            />
                        </v-tabs-window-item>

                        <v-tabs-window-item value="dubizzle" eager>
                            <ListingDubizzle
                                :data="listings.data?.dubizzle"
                            />
                        </v-tabs-window-item>

                        <!--                        <v-tabs-window-item value="other" eager>

                                                    <ListingOthers :data="listings.data?.other"/>

                                                </v-tabs-window-item>-->
                    </v-tabs-window>
                </v-container>
            </v-card>

            <v-card rounded="lg" variant="flat" v-if="!leads.loading" class="mt-5">
                <v-card-title class="d-flex justify-space-between">
                    <span>Lead Insights</span>
                </v-card-title>
                <v-card-subtitle>Here are some insights about your leads.</v-card-subtitle>

                <v-container fluid>
                    <LeadOverall :data="leads.data"/>
                </v-container>
            </v-card>

            <v-card rounded="lg" variant="flat" v-if="!performance.loading" class="mt-5">
                <v-card-title>Performance Insights</v-card-title>
                <v-card-subtitle
                >Here are some insights about your
                    performance.
                </v-card-subtitle
                >

                <v-container fluid>
                    <v-row>
                        <v-col cols="12" md="6">

                        </v-col>

                        <v-col cols="12" md="6">

                        </v-col>

                        <v-col cols="12" md="6">

                        </v-col>

                        <v-col cols="12" md="6">
                           
                        </v-col>

                        <!--                        <v-col cols="12" md="12">
                                                    <LastEventSince :data="performance.data?.last_event_since"/>
                                                </v-col>-->
                    </v-row>
                </v-container>
            </v-card>


            <div v-if="isEnabled">


            <v-card rounded="lg" variant="flat" v-if="!agents.loading" class="mt-5">
                <v-card-title>Agents Insights</v-card-title>
                <v-card-subtitle
                >Here are some insights about your agents.
                </v-card-subtitle
                >

                <v-container fluid>
                    <v-row>
                        <v-col cols="12" md="6">
                            <HighestLeads
                                :data="agents.data?.highest_no_of_leads"
                            />
                        </v-col>
                        <v-col cols="12" md="6">
                            <HighestListings
                                :data="agents.data?.highest_no_of_listings"
                            />
                        </v-col>
                        <v-col cols="12" md="6">
                            <HighestQualityScore
                                :data="agents.data?.highest_no_of_quality_score"
                            />
                        </v-col>

                        <v-col v-if="!agents.callInsightLoading" cols="12" md="12">
                            <v-card variant="flat" border>
                                <StackedBarChart
                                    title="Agent Call Logs"
                                    subtitle="Overview of Agent Call Logs (Completed, Missed, No Answer.)"
                                    info
                                    info-text="This bar chart displays the number of call logs for agents, categorized by type, within the selected time frame."
                                    :data="agents.callInsightData?.call_log_by_status"
                                />
                            </v-card>
                        </v-col>

                        <v-col v-if="!agents.callInsightLoading" cols="12" md="12">
                            <v-card variant="flat" border>
                                <StackedBarChart
                                    title="AX Contacts Call Logs"
                                    subtitle="Overview of AX Contacts Call Logs (Completed, Missed, No Answer.)"
                                    info
                                    info-text="This bar chart displays the number of call logs for agents, categorized by type, within the selected time frame."
                                    :data="agents.callInsightData?.ax_contacts_call_log_by_status"
                                />
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card>


            <v-card rounded="lg" variant="flat" v-if="!activityInsight.loading" class="mt-5">
                <v-card-title class="d-flex justify-space-between">
                    <span>Activity Insights</span>
                </v-card-title>
                <v-card-subtitle>Here are some insights about your activities.</v-card-subtitle>

                <v-container fluid>
                    <v-card variant="flat" border>
                        <StackedBarChart
                            title="Agent Calendar Activities"
                            subtitle="Overview of Agent Activities (Viewing, Meeting, Follow-up, etc.)"
                            info
                            info-text="This bar chart displays the number of calendar activities for agents, categorized by type, within the selected time frame."
                            :data="activityInsight?.data?.agents_event"
                        />
                    </v-card>
                </v-container>
            </v-card>


                <v-card
                    variant="text"
                    class="mt-5"
                    v-if="
                    listings.loading || listings.insightsLoading ||
                    leads.loading ||
                    agents.loading || agents.callInsightLoading ||
                    performance.loading ||
                    activityInsight.loading
                "
                >
                    <v-card-text class="d-flex align-center justify-center gap-2">
                        <v-progress-circular
                            color="primary"
                            indeterminate
                        ></v-progress-circular>
                        <span class="text-primary">Loading, please wait...</span>
                    </v-card-text>
                </v-card>
            </div>
            <!--Loader-->

        </v-container>
    </v-app>
</template>

<style>
.v-application {
    font-family: "Nunito", sans-serif !important;
    border-radius: 10px !important;
    background: transparent !important;
}

.v-label {
    margin-bottom: 0 !important;
}

.chart {
    width: 100% !important;
    height: 400px;
}

.custom-card-overflow .v-card-item__content {
    overflow: visible !important;
}

.dp__calendar_header_item {
    font-size: 1rem !important;
    font-weight: 600 !important;
    font-family: "Roboto", sans-serif !important;
    color: #757575 !important;
}

.dp__calendar_item {
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    font-family: "Roboto", sans-serif !important;
    color: #757575 !important;
}

.dp--preset-range {
    font-size: 0.75rem !important;
    font-weight: 500 !important;
    line-height: 1.25 !important;
    letter-spacing: 0.0333333333em !important;
    font-family: "Roboto", sans-serif !important;
    text-transform: none !important;
    color: #757575 !important;
}

.dp__menu_content_wrapper {
    border-bottom: 1px solid lightgrey;
}

</style>
