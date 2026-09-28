<script setup>
import {nextTick, onMounted, ref, watch, computed} from "vue";
import moment from "moment";

import Barchart from "../../components/adminDashboard/BarChart.vue";

import StackedBarChart from "../../components/adminDashboard/StackedBarChart.vue";

const date = ref([
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


const listings = ref({
    data: [],
    loading: true,
});


const getListingData = async (loading = true) => {
    if (loading) {
        listings.value.loading = true;
    }

    let params = {
        date: date.value,
        start_date: date.value[0],
        end_date: date.value[1],
    };

    try {
        const {data} = await axios.get(
            "/reporting/admin/dashboard/listings-data",
            {params: params}
        );
        if (data.data) {
            listings.value.data = data.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        listings.value.loading = false;
    }
};



onMounted(async () => {
    await getListingData();
});

watch(date, async (newVal) => {
    await getListingData();
});

const callAllApis = async () => {
    await getListingData();
};
</script>

<template>
    <v-app>
        <v-container fluid>
            <div style="background: white; border-radius: 10px; padding: 20px">
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
                    </v-row>
                </div>
            </div>

            <div>
                <v-card rounded="lg" variant="flat" class="mt-5">
                    <v-card-title>Listings Activities</v-card-title>
                    <v-card-subtitle>This report includes listings, Notes, Listing Photos and Shoot Bookings.</v-card-subtitle>

                    <v-container fluid>
                        <v-row>
                            <v-col cols="12" md="12">
                                <StackedBarChart
                                    title="Listing Activities"
                                    subtitle="Number of Listing Activities"
                                    :data="listings.data?.listing_activity_data || []"
                                />
                            </v-col>
                            <v-col cols="12" md="12">
                                <Barchart
                                    title="Listing Requests"
                                    subtitle="Listing Requests(Publish, Republish and Unpublish)"
                                    :data="listings.data?.published_listings_data || []"
                                    itemNameKey="user_name"
                                />
                            </v-col>
                            <v-col cols="12" md="12">
                                <Barchart
                                    title="Listing Approval Execution Time"
                                    subtitle="Listing Approval Execution Time from Pending to Published(in minutes)"
                                    :data="listings.data?.average_approval_execution_time_data || []"
                                    itemNameKey="user_name"
                                />
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card>
            </div>
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
