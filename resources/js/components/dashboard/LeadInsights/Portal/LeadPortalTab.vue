<template>
    <div>
        <v-tabs v-model="mainTab" class="mb-4">
            <v-tab value="property-finder">Property Finder</v-tab>
            <v-tab value="bayut">Bayut</v-tab>
        </v-tabs>
        <v-tabs-window v-model="mainTab">
            <v-tabs-window-item value="property-finder">
                <v-tabs v-model="subTab['property-finder']" class="mb-2">
                    <v-tab value="chart">Chart</v-tab>
                    <v-tab value="table">Date Wise</v-tab>
                    <v-tab value="day-wise">Day Wise</v-tab>
                </v-tabs>
                <v-tabs-window v-model="subTab['property-finder']">
                    <v-tabs-window-item value="chart">
                        <div>

                            <BarChartSimple
                                v-if="propertyFinderData.length > 0"
                                title="Property Finder Leads"
                                subtitle="Leads by Date"
                                :data="propertyFinderData"
                                info
                                info-text="This chart shows the number of Property Finder leads per day."
                            />
                        </div>
                    </v-tabs-window-item>
                    <v-tabs-window-item value="table">
                        <v-card>
                            <v-card-title>Property Finder Leads Data</v-card-title>
                            <v-card-text class="pa-0">
                                <div class="table-container">
                                    <v-table density="compact">
                                        <thead>
                                        <tr>
                                            <th class="text-left">Date</th>
                                            <th class="text-left">Lead Count</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="propertyFinderData.length === 0">
                                            <td colspan="2" class="text-center py-4 text-grey">
                                                No data available
                                            </td>
                                        </tr>
                                        <tr v-for="item in propertyFinderData" :key="item.date" v-else>
                                            <td>{{ formatDate(item.date) }}</td>
                                            <td>{{ item.count }}</td>
                                        </tr>
                                        </tbody>
                                    </v-table>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-tabs-window-item>
                    <v-tabs-window-item value="day-wise">
                        <div class="mb-4">
                            <BarChartSimple
                                v-if="propertyFinderDayWiseData.length > 0"
                                title="Property Finder Leads by Day"
                                subtitle="Leads grouped by day of week"
                                :data="propertyFinderDayWiseData"
                                color="#d4a188"
                                info
                                info-text="This chart shows the total number of Property Finder leads grouped by day of the week."
                            />
                        </div>
                        <v-card>
                            <v-card-title>Property Finder Leads by Day of Week</v-card-title>
                            <v-card-text class="pa-0">
                                <div class="table-container">
                                    <v-table density="compact">
                                        <thead>
                                        <tr>
                                            <th class="text-left">Day</th>
                                            <th class="text-left">Total Lead Count</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="propertyFinderDayWiseData.length === 0">
                                            <td colspan="2" class="text-center py-4 text-grey">
                                                No data available
                                            </td>
                                        </tr>
                                        <tr v-for="item in propertyFinderDayWiseData" :key="item.date" v-else>
                                            <td>{{ item.date }}</td>
                                            <td>{{ item.count }}</td>
                                        </tr>
                                        </tbody>
                                    </v-table>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-tabs-window-item>
                </v-tabs-window>
            </v-tabs-window-item>
            <v-tabs-window-item value="bayut">
                <v-tabs v-model="subTab['bayut']" class="mb-2">
                    <v-tab value="chart">Chart</v-tab>
                    <v-tab value="table">Date Wise</v-tab>
                    <v-tab value="day-wise">Day Wise</v-tab>
                </v-tabs>
                <v-tabs-window v-model="subTab['bayut']">
                    <v-tabs-window-item value="chart">
                        <div>

                            <BarChartSimple
                                v-if="bayutData.length > 0"
                                title="Bayut Leads"
                                subtitle="Leads by Date"
                                :data="bayutData"
                                info
                                info-text="This chart shows the number of Bayut leads per day."
                            />
                        </div>
                    </v-tabs-window-item>
                    <v-tabs-window-item value="table">
                        <v-card>
                            <v-card-title>Bayut Leads Data</v-card-title>
                            <v-card-text class="pa-0">
                                <div class="table-container">
                                    <v-table density="compact">
                                        <thead>
                                        <tr>
                                            <th class="text-left">Date</th>
                                            <th class="text-left">Lead Count</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="bayutData.length === 0">
                                            <td colspan="2" class="text-center py-4 text-grey">
                                                No data available
                                            </td>
                                        </tr>
                                        <tr v-for="item in bayutData" :key="item.date" v-else>
                                            <td>{{ formatDate(item.date) }}</td>
                                            <td>{{ item.count }}</td>
                                        </tr>
                                        </tbody>
                                    </v-table>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-tabs-window-item>
                    <v-tabs-window-item value="day-wise">
                        <div class="mb-4">
                            <BarChartSimple
                                v-if="bayutDayWiseData.length > 0"
                                title="Bayut Leads by Day"
                                subtitle="Leads grouped by day of week"
                                :data="bayutDayWiseData"
                                color="#d4a188"
                                info
                                info-text="This chart shows the total number of Bayut leads grouped by day of the week."
                            />
                        </div>
                        <v-card>
                            <v-card-title>Bayut Leads by Day of Week</v-card-title>
                            <v-card-text class="pa-0">
                                <div class="table-container">
                                    <v-table density="compact">
                                        <thead>
                                        <tr>
                                            <th class="text-left">Day</th>
                                            <th class="text-left">Total Lead Count</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="bayutDayWiseData.length === 0">
                                            <td colspan="2" class="text-center py-4 text-grey">
                                                No data available
                                            </td>
                                        </tr>
                                        <tr v-for="item in bayutDayWiseData" :key="item.date" v-else>
                                            <td>{{ item.date }}</td>
                                            <td>{{ item.count }}</td>
                                        </tr>
                                        </tbody>
                                    </v-table>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-tabs-window-item>
                </v-tabs-window>
            </v-tabs-window-item>
        </v-tabs-window>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import BarChartSimple from '../../BarChartSimple.vue';

const mainTab = ref('property-finder');
const subTab = ref({
    'property-finder': 'chart',
    'bayut': 'chart'
});
const props = defineProps({
    data: {
        type: Object,
        required: true
    }
});
const propertyFinderData = computed(() => {
    return props.data?.property_finder || [];
});
const propertyFinderDayWiseData = computed(() => {
    const data = props.data?.property_finder || [];

    // Initialize days with 0 count
    const dayWiseData = {
        'Sunday': 0,
        'Monday': 0,
        'Tuesday': 0,
        'Wednesday': 0,
        'Thursday': 0,
        'Friday': 0,
        'Saturday': 0
    };

    // Group data by day of week
    data.forEach(item => {
        if (item.date) {
            const date = new Date(item.date);
            const dayName = date.toLocaleDateString('en-US', { weekday: 'long' });
            if (dayWiseData.hasOwnProperty(dayName)) {
                dayWiseData[dayName] += parseInt(item.count) || 0;
            }
        }
    });

    // Convert to array format for chart
    return Object.keys(dayWiseData).map(day => ({
        date: day,
        count: dayWiseData[day]
    }));
});

const bayutData = computed(() => {
    return props.data?.bayut || [];
});

const bayutDayWiseData = computed(() => {
    const data = props.data?.bayut || [];

    // Initialize days with 0 count
    const dayWiseData = {
        'Sunday': 0,
        'Monday': 0,
        'Tuesday': 0,
        'Wednesday': 0,
        'Thursday': 0,
        'Friday': 0,
        'Saturday': 0
    };

    // Group data by day of week
    data.forEach(item => {
        if (item.date) {
            const date = new Date(item.date);
            const dayName = date.toLocaleDateString('en-US', { weekday: 'long' });
            if (dayWiseData.hasOwnProperty(dayName)) {
                dayWiseData[dayName] += parseInt(item.count) || 0;
            }
        }
    });

    // Convert to array format for chart
    return Object.keys(dayWiseData).map(day => ({
        date: day,
        count: dayWiseData[day]
    }));
});

// Format date function
const formatDate = (dateString) => {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (error) {
        console.error('Error formatting date:', error);
        return dateString;
    }
};


</script>

<style scoped>
.table-container {
    max-height: 400px;
    overflow-y: auto;
}
</style>
