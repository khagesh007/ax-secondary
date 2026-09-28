<script setup>
import {onMounted, ref, watch} from "vue";
import moment from "moment";


import ChartView from "../../components/listingVerifications/ChartView.vue";
import TableView from "../../components/listingVerifications/TableView.vue";
import AddNewSubmissions from "../../components/listingVerifications/AddNewSubmissions.vue";

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

const submissions = ref({
    data: [],
    loading: true,
    perPage: 10,
    total: 0,
    search: "",
    page: 1,
    order_by: 'portal_created_at',
    order: 'desc'
});

const userAndStatusCount = ref({
    data: {},
    loading: true,
});

const currentViewTab = ref("table");
const currentStatusTab = ref("all");

const getSubmissions = async (event) => {
    submissions.value.loading = true;
    submissions.value.page = event?.page || 1;
    submissions.value.order_by = event?.sortBy[0]?.key || 'portal_created_at';
    submissions.value.order = event?.sortBy[0]?.order || 'desc';
    let params = {
        date: date.value,
        start_date: date.value[0],
        end_date: date.value[1],
        per_page: submissions.value.perPage,
        term: submissions.value.search,
        page: submissions.value.page,
        order_by: submissions.value.order_by,
        order: submissions.value.order,
        status: currentStatusTab.value === "all" ? null : currentStatusTab.value,
    };

    try {
        const {data} = await axios.get(
            "reporting/admin/listing-verifications/list",
            {params: params}
        );
        if (data.data) {
            submissions.value.data = data.data.data;
            submissions.value.total = data.data.total;
        }
    } catch (error) {
        console.error(error);
    } finally {
        submissions.value.loading = false;
    }
};

const getUserAndStatusWiseCount = async (loading = true) => {
    if (loading) {
        userAndStatusCount.value.loading = true;
    }

    let params = {
        date: date.value,
        start_date: date.value[0],
        end_date: date.value[1],
    };

    try {
        const {data} = await axios.get(
            "reporting/admin/listing-verifications/user-and-status-wise-count",
            {params: params}
        );
        if (data.data) {
            userAndStatusCount.value.data = data.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        userAndStatusCount.value.loading = false;
    }
};

onMounted(async () => {
    await getUserAndStatusWiseCount();
});

watch(date, async (newVal) => {
    await getSubmissions();
    await getUserAndStatusWiseCount();
});
watch(currentStatusTab, async (newValue) => {
    await getSubmissions();
})

const showingAddSubmissions = ref(false)

const snackbar = ref({
    text: "",
    type: "success",
    showing: false
})

const toggleSnackbar = (text, type = "success", showing = true) => {
    snackbar.value.text = text;
    snackbar.value.type = type;
    snackbar.value.showing = showing;
}
</script>

<template>

    <v-snackbar
        v-model="snackbar.showing"
        :color="snackbar.type"
    >
        {{ snackbar.text }}
        <template v-slot:actions>
            <v-btn
                variant="text"
                @click="snackbar.showing = false"
            >
                Close
            </v-btn>
        </template>
    </v-snackbar>

    <AddNewSubmissions
        v-if="showingAddSubmissions"
        :on-close-modal="() => showingAddSubmissions = false"
        :get-submissions="getSubmissions"
        :toggle-snackbar="toggleSnackbar"
    />

    <v-app>
        <v-container fluid>
            <div style="background: white; border-radius: 5px; padding: 20px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
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
                    </div>
                    <v-btn @click="showingAddSubmissions = true" variant="tonal">New Submission</v-btn>
                </div>
            </div>

            <v-card variant="flat" class="mt-3">
                <v-tabs v-model="currentStatusTab">
                    <v-tab value="all">All</v-tab>
                    <v-tab value="pending">Pending</v-tab>
                    <v-tab value="approved">Approved</v-tab>
                    <v-tab value="expired">Expired</v-tab>
                    <v-tab value="rejected">Rejected</v-tab>
                </v-tabs>
            </v-card>

            <v-card variant="flat" class="mt-3">
                <v-card-title class="d-flex justify-content-between align-items-center">
                    Listing Verification
                    <v-tabs v-model="currentViewTab" content="align-center">
                        <v-tab value="table" color="primary" class="text-body-2">Table</v-tab>
                        <v-tab value="chart" color="primary" class="text-body-2">Chart</v-tab>
                    </v-tabs>
                </v-card-title>

                <v-container fluid>
                    <v-tabs-window v-model="currentViewTab">
                        <v-tabs-window-item value="table" eager>
                            <TableView
                                :data="submissions"
                                :get-submissions="getSubmissions"
                                :toggle-snackbar="toggleSnackbar"
                            />
                        </v-tabs-window-item>
                        <v-tabs-window-item value="chart" eager>
                            <ChartView :data="userAndStatusCount.data"/>
                        </v-tabs-window-item>
                    </v-tabs-window>
                </v-container>
            </v-card>

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
