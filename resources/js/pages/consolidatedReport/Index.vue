<template>
    <v-container>
        <div style="background: white; border-radius: 10px; padding: 20px" class="mb-4">

<!--                <h3>Good Morning, <span class="text-primary">Muhammad Uzair Sohail!</span></h3>-->
                <v-row align="center">
                    <v-col cols="12" md="3">
                        <v-row>
                            <v-col cols="6">
                                <VueDatePicker
                                    v-model="startMonth"
                                    month-picker
                                    placeholder="Start Month"
                                    :enable-time-picker="false"
                                    auto-apply
                                />
                            </v-col>
                            <v-col cols="6">
                                <VueDatePicker
                                    v-model="endMonth"
                                    month-picker
                                    placeholder="End Month"
                                    :enable-time-picker="false"
                                    auto-apply
                                />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" md="3">
                            <v-autocomplete
                                v-model="department"
                                @update:model-value="handleDepartmentChange"
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
 <UsersAutocomplete
                        :filters="filters"
                        :model="filters.user_id || null"
                        label="Select Agent"
                        return-object
                        @update:model="(value) => filters.user_id = value"
                        hide-details
                    />
                    </v-col>

                    <v-col cols="12" md="3">
                       <button type="button"

                       class="btn btn-ax-color float-end" @click="refreshCalculation()">
                           Refresh Calculation
                       </button>
                    </v-col>

                </v-row>

        </div>

        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>Agent Performance Report</v-card-title>
                    <v-card-subtitle v-if="reports.length > 0">
                        Showing data for {{ reports.length }} agent(s) and {{ getUniqueMonths().length }} month(s)
                    </v-card-subtitle>
                    <v-card-text class="pa-0">
                        <!-- Debug info -->
                        <div v-if="false" class="p-3 bg-light">
                            <small>
                                <strong>Debug Info:</strong><br>
                                Reports count: {{ reports.length }}<br>
                                Processed reports count: {{ processedReports.length }}<br>
                                Unique months: {{ getUniqueMonths().join(', ') }}<br>
                                Department: {{ department }}<br>
                                Date range: {{ date[0] }} to {{ date[1] }}<br>
                                Raw reports data: {{ JSON.stringify(reports, null, 2) }}
                            </small>
                        </div>

                        <!-- No data state -->
                        <div v-if="reports.length === 0" class="text-center p-5">
                            <v-icon size="64" color="grey">mdi-chart-line</v-icon>
                            <h4 class="mt-3 text-grey">No Data Available</h4>
                            <p class="text-grey">No agent performance data found for the selected criteria.</p>
                            <p class="text-muted small">
                                Try adjusting the date range or department filter.
                            </p>
                        </div>

                        <!-- Table with data -->
                        <div v-else class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-start" style="min-width: 200px;">AGENT</th>
                                        <th class="text-center" v-for="month in getUniqueMonths()" :key="month" colspan="3">
                                            {{ formatMonthYear(month) }}
                                        </th>
                                        <th class="text-center" colspan="2">TOTAL</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <template v-for="month in getUniqueMonths()" :key="month">
                                            <th class="text-center bg-primary text-white">TARGET</th>
                                            <th class="text-center bg-success text-white">ACHIEVEMENT</th>
                                            <th class="text-center bg-info text-white">%</th>
                                        </template>
                                        <th class="text-center bg-warning text-dark">TARGET</th>
                                        <th class="text-center bg-danger text-white">ACHIEVEMENT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="agentData in processedReports" :key="agentData.agent.id">
                                        <td class="d-flex align-items-center">
                                            <img :src="agentData.agent.image"
                                                 class="rounded-circle me-2"
                                                 style="width: 40px; height: 40px; object-fit: cover;"
                                                 :alt="agentData.agent.name">
                                            <div>
                                                <div class="fw-bold">{{ agentData.agent.name }}</div>
                                                <small class="text-muted">{{ agentData.agent.position }}</small>
                                            </div>
                                        </td>
                                        <template v-for="month in getUniqueMonths()" :key="month">
                                            <td class="text-center">
                                                {{ numberFormat(getMonthData(agentData, month, 'target')) }}
                                            </td>
                                            <td class="text-center">
                                                {{ numberFormat(getMonthData(agentData, month, 'achieved')) }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge" :class="getPercentageBadgeClass(getMonthData(agentData, month, 'target'), getMonthData(agentData, month, 'achieved'))">
                                                    {{ calculatePercentage(getMonthData(agentData, month, 'target'), getMonthData(agentData, month, 'achieved')) }}%
                                                </span>
                                            </td>
                                        </template>
                                        <td class="text-center fw-bold">
                                            {{ numberFormat(getTotalTarget(agentData)) }}
                                        </td>
                                        <td class="text-center fw-bold">
                                            {{ numberFormat(getTotalAchieved(agentData)) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>


    </v-container>
</template>

<script setup>
import {onBeforeMount, onMounted, ref, watch, computed, nextTick} from "vue";
import axios from 'axios';
import UsersAutocomplete from "../../components/common/UsersAutocomplete.vue";

const isAgent = ref(false);
const departments = ref({
    data: [],
    loading: true,
})
const department = ref();
const overview = ref([]);
const reports = ref([]);
const startMonth = ref(moment().startOf("month").toDate());
const endMonth = ref(moment().endOf("month").toDate());
const date = ref([
    moment().startOf("month").format("YYYY-MM-DD"),
    moment().endOf("month").format("YYYY-MM-DD"),
]);

const pipeline_facts = ref([]);
const group_deal_values = ref([]);

const start_date = ref(null);
const end_date = ref(null);
const filters = ref({
    department: department.value,
    agent: null,
    user_id: null
});

const params = ref({
    tab: "overall",
    date: date.value,
    start_date: null,
    end_date: null,
    group: department.value,
    agent_id: null,
})
const handleDepartmentChange = async () => {
    // Reset all dependent values
    filters.value.user_id = null;
    filters.value.agent = null;
    filters.value.department = department.value;
    params.value.agent_id = null;
    // Wait for the next tick to ensure reactivity
    await nextTick();

  // await callAllApis();
};

const getOverview = async () => {
    console.log('User id:', filters.value.user_id)
    console.log('Agent ID being sent:', params.value.agent_id)
    params.value.tab = "overall";
    params.value.start_date = start_date.value;
    params.value.end_date = end_date.value;
    params.value.group = department.value;
    // Don't override agent_id here, let it be set by the watchers
    // params.value.agent_id should already be set by the filters watcher

    try {
        let response = await axios.get('/reports/agent-consolidate-report/landing', { params: params.value })
        let responseData = response?.data || {};


        // Try different data structures
        let reportData = responseData?.data?.reports || responseData?.data || responseData?.reports || [];
        reports.value = reportData;
    } catch (error) {
        console.error('API Error:', error);
        reports.value = [];
    }
}

// Process reports data to group by agent
const processedReports = computed(() => {
    const agentMap = new Map();

    reports.value.forEach(item => {
        const agentId = item.agent.id;
        if (!agentMap.has(agentId)) {
            agentMap.set(agentId, {
                agent: item.agent,
                monthlyData: []
            });
        }
        agentMap.get(agentId).monthlyData.push(...item.report);
    });

    const result = Array.from(agentMap.values());
    return result;
});

// Get unique months from all reports
const getUniqueMonths = () => {
    const months = new Set();

    if (!Array.isArray(reports.value)) {
        console.warn('Reports is not an array:', reports.value);
        return [];
    }

    reports.value.forEach(item => {

        if (item && Array.isArray(item.report)) {
            item.report.forEach(report => {
                if (report) {
                    // Handle both 'date' and 'month' fields
                    const monthValue = report.date || report.month;
                    if (monthValue) {
                     //   console.log('Adding month:', monthValue);
                        months.add(monthValue);
                    }
                }
            });
        } else {
            console.warn('Invalid report structure:', item);
        }
    });

    const result = Array.from(months).sort();
   // console.log('Unique months result:', result);
    return result;
};// Get data for specific agent and month
const getMonthData = (agentData, month, field) => {
    const monthData = agentData.monthlyData.find(data => {
        // Handle both 'date' and 'month' fields
        const dataMonth = data.date || data.month;
        return dataMonth === month;
    });
    return monthData ? (parseFloat(monthData[field]) || 0) : 0;
};

// Get total target for an agent
const getTotalTarget = (agentData) => {
    const total = agentData.monthlyData.reduce((sum, data) => {
        return sum + parseFloat(data.target || 0);
    }, 0);
    return Math.round(total * 100) / 100; // Round to 2 decimal places
};

const getTotalAchieved = (agentData) => {
    const total = agentData.monthlyData.reduce((sum, data) => {
        return sum + parseFloat(data.achieved || 0);
    }, 0);
    return Math.round(total * 100) / 100; // Round to 2 decimal places
};
// Format month-year for display
const formatMonthYear = (monthStr) => {
  //  console.log('Formatting month string:', monthStr);
    if(!monthStr) return '';

    // Handle different date formats
    let year, month;

    if (monthStr.includes('-')) {
        [year, month] = monthStr.split('-');
    } else if (monthStr.length === 6) {
        // Handle YYYYMM format
        year = monthStr.substring(0, 4);
        month = monthStr.substring(4, 6);
    } else {
        console.warn('Unknown date format:', monthStr);
        return monthStr;
    }

    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                       'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const monthIndex = parseInt(month) - 1;

    if (monthIndex < 0 || monthIndex > 11) {
        console.warn('Invalid month:', month);
        return monthStr;
    }

    return `${monthNames[monthIndex]} ${year}`;
};

// Get badge class based on percentage
const getPercentageBadgeClass = (target, achieved) => {
    const percentage = parseFloat(calculatePercentage(target, achieved));
    if (percentage >= 100) return 'bg-success';
    if (percentage >= 75) return 'bg-warning';
    if (percentage >= 50) return 'bg-info';
    return 'bg-danger';
};



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

const refreshCalculation = async () => {
    try {
        const { data } = await axios.get("/reports/agent-consolidate-report/refresh");
        showToastify('Calculation is running in background it may take some time.', 'success');
    } catch (error) {
        showToastify("Error refreshing report:", 'error');
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


}, { immediate: true });

// Watch for month picker changes
watch([startMonth, endMonth], async ([newStartMonth, newEndMonth]) => {
    if (departments.value.data.length === 0) {
        return;
    }

    if (newStartMonth && newEndMonth) {
        // Convert month picker dates to start/end of month
        const startDate = moment(newStartMonth).startOf("month").format("YYYY-MM-DD");
        const endDate = moment(newEndMonth).endOf("month").format("YYYY-MM-DD");

        // Update the date array for compatibility
        date.value = [startDate, endDate];

        params.value.start_date = startDate;
        params.value.end_date = endDate;
        params.value.group = department.value;

        start_date.value = moment(startDate).format("DD-MM-YYYY");
        end_date.value = moment(endDate).format("DD-MM-YYYY");

        await getOverview();
    }
}, { immediate: false });

watch(
    filters,
     async () => {
        console.log('Filters changed:', filters.value);
        // Handle both object and direct ID cases
        if (filters.value.user_id) {
            params.value.agent_id = typeof filters.value.user_id === 'object'
                ? filters.value.user_id.id
                : filters.value.user_id;
        } else {
            params.value.agent_id = null;
        }
        console.log('Setting agent_id to:', params.value.agent_id);
        await getOverview();
    },
    {deep: true}
);

const numberFormat = (value) => {
    if (value === null || value === undefined || value === 0) {
        return '0';
    }

    // Convert to number if string
    const numValue = Number(value);

    // Format large numbers with suffixes
    if (numValue >= 1000000) {
        return (numValue / 1000000).toFixed(1) + 'M';
    } else if (numValue >= 1000) {
        return (numValue / 1000).toFixed(1) + 'k';
    }

    return numValue.toLocaleString();
};
const callAllApis = async () => {
    params.value.group = department.value;
    await getOverview();


};
const calculatePercentage = (value1, value2) => {
    if (!value1 || !value2) return 0;
    let val = ((Number(value2) / Number(value1)) * 100).toFixed(2);
    return  isNaN(val) ? 0 : val;
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

.table-responsive {
    max-height: 70vh;
    overflow-x: auto;
    overflow-y: auto;
}

.table th {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #f0e4dd !important;
    color: black !important;
    border-color: #b8906f !important;
}

.table th:first-child {
    position: sticky;
    left: 0;
    z-index: 11;
    background-color: #f0e4dd !important;
    color: black !important;
}

.table td:first-child {
    position: sticky;
    left: 0;
    background-color: white;
    z-index: 1;
    box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1);
}

.table-striped tbody tr:nth-of-type(odd) td:first-child {
    background-color: #f8f9fa;
}

.table td, .table th {
    padding: 8px 12px;
    vertical-align: middle;
    border: 1px solid #dee2e6;
    white-space: nowrap;
}

.badge {
    font-size: 11px;
    padding: 4px 8px;
}
</style>
