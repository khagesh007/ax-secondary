<script setup>

import {ref} from "vue";
import moment from "moment/moment";

const props = defineProps({
    tab: {
        type: String,
        required: true,
    },
    date: {
        type: [String, Date],
        required: true,
    },
    overview: {
        type: Object,
        required: true,
    },
    summary: {
        type: Object,
        required: true,
    },
    members: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['refresh-data'])

const getColor = (percentage) => {
    if (percentage < 50) {
        return 'error'
    } else if (percentage < 75) {
        return 'warning'
    } else {
        return 'success'
    }
}

const colors = ['#F0FDF4', '#FEFCE8', '#EFF6FF', '#FAF5FF', '#FEF2F2']

const selectedGroup = ref({
    loading: false,
    modal: false,
})

const setSelectedGroup = (data) => {
    formData.value.group_id = data?.group?.id
    formData.value.sales_target = data?.team_targets?.sales_value?.target || 0
    formData.value.pf_credit_limit = data?.credit_targets?.pf_credit_limit?.target || 0
    formData.value.bayut_credit_limit = data?.credit_targets?.bayut_credit_limit?.target || 0
    formData.value.dubizzle_credit_limit = data?.credit_targets?.dubizzle_credit_limit?.target || 0
    formData.value.pipelines = data?.team_targets?.pipelines?.target || 0
    formData.value.month = moment(props.date).format('YYYY-MM')

    selectedGroup.value.modal = true
}

const formData = ref({
    group_id: null,
    sales_target: null,
    pf_credit_limit: null,
    bayut_credit_limit: null,
    dubizzle_credit_limit: null,
    pipelines:null,
    month: null
})

const resetFormData = () => {
    formData.value.group_id = null
    formData.value.sales_target = null
    formData.value.month = null
    formData.value.pf_credit_limit = null
    formData.value.bayut_credit_limit = null
    formData.value.dubizzle_credit_limit = null
    formData.value.pipelines = null
}

const submit = async () => {
    selectedGroup.value.loading = true

    try {
        const response = await axios.post('/reports/kpi-v3/team-target-update', formData.value)
        if (response.data.status) {
            selectedGroup.value.modal = false
            emit('refresh-data')
        }
    } catch (e) {
        console.error(e)
    } finally {
        selectedGroup.value.loading = false
        selectedGroup.value.modal = false
        resetFormData()
    }
}

const teamKpiReport = ref({
    loading: false,
    modal: false,
    group_id: null,
    data: null
})

const getTeamMemberReport = async () => {
    teamKpiReport.value.loading = true

    let params = {
        group_id: teamKpiReport.value.group_id,
        month: moment(props.date).format('YYYY-MM')
    }
    try {
        const response = await axios.get(`/reports/kpi-v3/team-member-reports`, {params})
        if (response.data.status) {
            teamKpiReport.value.data = response.data.data
        }
    } catch (e) {
        console.error(e)
    } finally {
        teamKpiReport.value.loading = false
    }
}
const handleTeamKpiReport = async (data, isModal) => {
    if (isModal) {
        teamKpiReport.value.group_id = data?.group?.id
        if (teamKpiReport.value.group_id) {
            await getTeamMemberReport()
        }
    } else {
        teamKpiReport.value.data = null
    }
    teamKpiReport.value.modal = isModal
}

const formatNumberMetricPrefixes =(number) => {
    if (number >= 1000000) {
        return (number / 1000000).toFixed(2) + 'M';
    } else if (number >= 1000) {
        return (number / 1000).toFixed(2) + 'K';
    } else {
        return number;
    }
}
</script>

<template>
    <v-row v-if="tab === 'Overall'">
        <v-col v-for="data in overview" cols="12" md="4">
            <v-card flat border>
                <v-card-item class="text-center">
                    <v-card-title class="text-primary py-0">{{ data?.kpi_title }}</v-card-title>
                    <v-card-subtitle>Percentage of Overall {{ data?.kpi_title }} Achieved</v-card-subtitle>
                    <v-progress-circular
                        class="my-4"
                        :model-value="data?.percentage"
                        :rotate="360"
                        :size="100"
                        :width="12"
                        :color="getColor(data?.percentage)"
                    >
                        <template v-slot:default>
                            <div
                                class="text-body-1 ma-0"
                                :class="`text-${getColor(data?.percentage)}`"
                            >
                                {{ data?.percentage }}%
                            </div>
                        </template>
                    </v-progress-circular>
                    <div class="d-flex justify-space-between">
                        <div class="text-body-1 text-center text-primary" >
                            <span v-if="data?.kpi == 'tru_checked_listings'" v-tooltip:top="'Total active listing on Bayut'">
                            Target: {{ data?.target_text }}
                                </span>
                            <span v-else>
                                 Target: {{ data?.target_text }}
                            </span>
                        </div>
                        <div
                            class="text-body-1 text-center"
                            :class="`text-${getColor(data?.percentage)}`"
                        >
                            Achieved: {{ data?.achieved_text }}
                        </div>
                    </div>
                </v-card-item>
            </v-card>
        </v-col>
    </v-row>

    <v-row>
    <!-- These are top horizontal progress bar cards -->
        <!--Sales Target-->
        <v-col v-if="tab === 'Sales Target'" cols="12">
            <v-card flat border>
                <v-card-text class="pa-3">
                    <v-row align="center">
                        <v-col cols="12" md="4">
                            <v-card-title class="text-primary py-0">Sales Value</v-card-title>
                            <v-card-subtitle>Percentage of Overall Sales Value Achieved</v-card-subtitle>
                        </v-col>
                        <v-col cols="12" md="4">
                            <div class="text-h6 text-center text-primary">
                                Target: {{ overview['sales_value']?.target_text }}
                            </div>
                            <div
                                class="text-h6 text-center"
                                :class="`text-${getColor(overview['sales_value']?.percentage)}`"
                            >
                                Achieved: {{ overview['sales_value']?.achieved_text }}
                            </div>
                        </v-col>
                        <v-col cols="12" md="4" class="d-flex justify-end">
                            <v-progress-circular
                                :model-value="overview['sales_value']?.percentage"
                                :rotate="360"
                                :size="80"
                                :width="12"
                                :color="getColor(overview['sales_value']?.percentage)"
                            >
                                <template v-slot:default>
                                    <div
                                        class="text-body-2 ma-0"
                                        :class="`text-${getColor(overview['sales_value']?.percentage)}`"
                                    >
                                        {{ overview['sales_value']?.percentage }}%
                                    </div>
                                </template>
                            </v-progress-circular>
                        </v-col>
                    </v-row>
                </v-card-text>

            </v-card>
        </v-col>

        <!--Live Listings-->
        <v-col v-if="tab === 'Live Listings'" cols="12">
            <v-card flat border>
                <v-card-text class="pa-3">
                    <v-row align="center">
                        <v-col cols="12" md="4">
                            <v-card-title class="text-primary py-0">Live Listings</v-card-title>
                            <v-card-subtitle>Percentage of Overall Live Listings Achieved</v-card-subtitle>
                        </v-col>
                        <v-col cols="12" md="4">
                            <div class="text-h6 text-center text-primary">
                                Target: {{ overview['live_listings']?.target_text }}
                            </div>
                            <div
                                class="text-h6 text-center"
                                :class="`text-${getColor(overview['live_listings']?.percentage)}`"
                            >
                                Achieved: {{ overview['live_listings']?.achieved_text }}
                            </div>
                        </v-col>
                        <v-col cols="12" md="4" class="d-flex justify-end">
                            <v-progress-circular
                                :model-value="overview['live_listings']?.percentage"
                                :rotate="360"
                                :size="80"
                                :width="12"
                                :color="getColor(overview['live_listings']?.percentage)"
                            >
                                <template v-slot:default>
                                    <div
                                        class="text-body-2 ma-0"
                                        :class="`text-${getColor(overview['live_listings']?.percentage)}`"
                                    >
                                        {{ overview['live_listings']?.percentage }}%
                                    </div>
                                </template>
                            </v-progress-circular>
                        </v-col>
                    </v-row>
                </v-card-text>

            </v-card>
        </v-col>

        <!--New Listings-->
        <v-col v-if="tab === 'New Listings'" cols="12">
            <v-card flat border>
                <v-card-text class="pa-3">
                    <v-row align="center">
                        <v-col cols="12" md="4">
                            <v-card-title class="text-primary py-0">New Listings</v-card-title>
                            <v-card-subtitle>Percentage of Overall New Listings Achieved</v-card-subtitle>
                        </v-col>
                        <v-col cols="12" md="4">
                            <div class="text-h6 text-center text-primary">
                                Target: {{ overview['new_listings']?.target_text }}
                            </div>
                            <div
                                class="text-h6 text-center"
                                :class="`text-${getColor(overview['new_listings']?.percentage)}`"
                            >
                                Achieved: {{ overview['new_listings']?.achieved_text }}
                            </div>
                        </v-col>
                        <v-col cols="12" md="4" class="d-flex justify-end">
                            <v-progress-circular
                                :model-value="overview['new_listings']?.percentage"
                                :rotate="360"
                                :size="80"
                                :width="12"
                                :color="getColor(overview['new_listings']?.percentage)"
                            >
                                <template v-slot:default>
                                    <div
                                        class="text-body-2 ma-0"
                                        :class="`text-${getColor(overview['new_listings']?.percentage)}`"
                                    >
                                        {{ overview['new_listings']?.percentage }}%
                                    </div>
                                </template>
                            </v-progress-circular>
                        </v-col>
                    </v-row>
                </v-card-text>

            </v-card>
        </v-col>
    <!-- Pipelines horizontal tab data-->
        <v-col v-if="tab === 'Pipelines'" cols="12">
            <v-card flat border>
                <v-card-text class="pa-3">
                    <v-row align="center">
                        <v-col cols="12" md="4">
                            <v-card-title class="text-primary py-0">Pipelines</v-card-title>
                            <v-card-subtitle>Number of pipelines</v-card-subtitle>
                        </v-col>
                        <v-col cols="12" md="4">
                            <div class="text-h6 text-center text-primary">
                                Target: {{ overview['pipelines']?.target_text }}
                            </div>
                            <div
                                class="text-h6 text-center"
                                :class="`text-${getColor(overview['pipelines']?.percentage)}`"
                            >
                                Achieved: {{ overview['pipelines']?.achieved_text }}
                            </div>
                        </v-col>
                        <v-col cols="12" md="4" class="d-flex justify-end">
                            <v-progress-circular
                                :model-value="overview['pipelines']?.percentage"
                                :rotate="360"
                                :size="80"
                                :width="12"
                                :color="getColor(overview['pipelines']?.percentage)"
                            >
                                <template v-slot:default>
                                    <div
                                        class="text-body-2 ma-0"
                                        :class="`text-${getColor(overview['pipelines']?.percentage)}`"
                                    >
                                        {{ overview['pipelines']?.percentage }}%
                                    </div>
                                </template>
                            </v-progress-circular>
                        </v-col>
                    </v-row>
                </v-card-text>

            </v-card>
        </v-col>

        <!--Active Tru Broker-->
        <v-col v-if="tab === 'Active TruBroker'" cols="12">
            <v-card flat border>
                <v-card-text class="pa-3">
                    <v-row align="center">
                        <v-col cols="12" md="4">
                            <v-card-title class="text-primary py-0">TruBroker</v-card-title>
                            <v-card-subtitle>Percentage of Overall TruBroker Achieved</v-card-subtitle>
                        </v-col>
                        <v-col cols="12" md="4">
                            <div class="text-h6 text-center text-primary">
                                Target: {{ overview['tru_broker']?.target_text }}
                            </div>
                            <div
                                class="text-h6 text-center"
                                :class="`text-${getColor(overview['tru_broker']?.percentage)}`"
                            >
                                Achieved: {{ overview['tru_broker']?.achieved_text }}
                            </div>
                        </v-col>
                        <v-col cols="12" md="4" class="d-flex justify-end">
                            <v-progress-circular
                                :model-value="overview['tru_broker']?.percentage"
                                :rotate="360"
                                :size="80"
                                :width="12"
                                :color="getColor(overview['tru_broker']?.percentage)"
                            >
                                <template v-slot:default>
                                    <div
                                        class="text-body-2 ma-0"
                                        :class="`text-${getColor(overview['tru_broker']?.percentage)}`"
                                    >
                                        {{ overview['tru_broker']?.percentage }}%
                                    </div>
                                </template>
                            </v-progress-circular>
                        </v-col>
                    </v-row>
                </v-card-text>

            </v-card>
        </v-col>

        <!--Active Super Agent-->
        <v-col v-if="tab === 'Active Super Agent'" cols="12">
            <v-card flat border>
                <v-card-text class="pa-3">
                    <v-row align="center">
                        <v-col cols="12" md="4">
                            <v-card-title class="text-primary py-0">Super Agent</v-card-title>
                            <v-card-subtitle>Percentage of Overall Super Agent Achieved</v-card-subtitle>
                        </v-col>
                        <v-col cols="12" md="4">
                            <div class="text-h6 text-center text-primary">
                                Target: {{ overview['super_agent']?.target_text }}
                            </div>
                            <div
                                class="text-h6 text-center"
                                :class="`text-${getColor(overview['super_agent']?.percentage)}`"
                            >
                                Achieved: {{ overview['super_agent']?.achieved_text }}
                            </div>
                        </v-col>
                        <v-col cols="12" md="4" class="d-flex justify-end">
                            <v-progress-circular
                                :model-value="overview['super_agent']?.percentage"
                                :rotate="360"
                                :size="80"
                                :width="12"
                                :color="getColor(overview['super_agent']?.percentage)"
                            >
                                <template v-slot:default>
                                    <div
                                        class="text-body-2 ma-0"
                                        :class="`text-${getColor(overview['super_agent']?.percentage)}`"
                                    >
                                        {{ overview['super_agent']?.percentage }}%
                                    </div>
                                </template>
                            </v-progress-circular>
                        </v-col>
                    </v-row>
                </v-card-text>

            </v-card>
        </v-col>
        <!-- TruChecked Listings -->
        <v-col v-if="tab === 'TruChecked Listings'" cols="12">
            <v-card flat border>
                <v-card-text class="pa-3">
                    <v-row align="center">
                        <v-col cols="12" md="4">
                            <v-card-title class="text-primary py-0">TruChecked Listings</v-card-title>
                            <v-card-subtitle>Percentage of Overall TruChecked Listings Achieved</v-card-subtitle>
                        </v-col>
                        <v-col cols="12" md="4">
                            <div class="text-h6 text-center text-primary">
                                <span v-tooltip:top="'Total active listing on Bayut'">
                                Target: {{ overview['tru_checked_listings']?.target_text }}
                                    </span>
                            </div>
                            <div
                                class="text-h6 text-center"
                                :class="`text-${getColor(overview['super_agent']?.percentage)}`"
                            >
                                Achieved: {{ overview['tru_checked_listings']?.achieved_text }}
                            </div>
                        </v-col>
                        <v-col cols="12" md="4" class="d-flex justify-end">
                            <v-progress-circular
                                :model-value="overview['tru_checked_listings']?.percentage"
                                :rotate="360"
                                :size="80"
                                :width="12"
                                :color="getColor(overview['tru_checked_listings']?.percentage)"
                            >
                                <template v-slot:default>
                                    <div
                                        class="text-body-2 ma-0"
                                        :class="`text-${getColor(overview['tru_checked_listings']?.percentage)}`"
                                    >
                                        {{ overview['tru_checked_listings']?.percentage }}%
                                    </div>
                                </template>
                            </v-progress-circular>
                        </v-col>
                    </v-row>
                </v-card-text>

            </v-card>
        </v-col>

        <v-col
            v-for="(data,key) in summary"
            :key="key"
            cols="12" xl="3" lg="4" md="6"
        >
            <v-card flat border style="color: #6B7280" class="rounded-lg">
                <v-card-title class="d-flex align-center justify-space-between">
                    <div class="d-flex align-center gap-2">
                        <VAvatar rounded color="primary" variant="tonal" size="48">
                            <VImg :src="data?.user?.image"/>
                        </VAvatar>
                        <div>
                            <div>
                                <span class="text-body-1 me-1">{{ data?.user?.name }}</span>
                                <span class="text-body-2 text-primary">({{ key }})</span>
                            </div>
                            <div style="font-size: 10px">Manager</div>
                        </div>
                    </div>
                    <v-menu v-if="data?.permissions?.view || data?.permissions?.edit">
                        <template v-slot:activator="{ props }">
                            <v-btn color="#6B7280" rounded icon="mdi-dots-vertical" variant="text"
                                   size="small" v-bind="props"></v-btn>
                        </template>

                        <v-list width="200px">
                            <v-list-item
                                v-if="data?.permissions?.edit"
                                @click="setSelectedGroup(data)"
                            >
                                <v-list-item-subtitle>
                                    <v-icon class="me-2">mdi-square-edit-outline</v-icon>
                                    Edit Targets
                                </v-list-item-subtitle>
                            </v-list-item>
                            <v-list-item
                                v-if="data?.permissions?.team_kpi_report"
                                @click="handleTeamKpiReport(data, true)">
                                <v-list-item-subtitle>
                                    <v-icon class="me-2">mdi-eye-outline</v-icon>
                                    Team KPI Report
                                </v-list-item-subtitle>
                            </v-list-item>
                            <v-list-item
                                v-if="data?.permissions?.view"
                                :href="data?.permissions?.view_url"
                                target="_blank"
                            >
                                <v-list-item-subtitle>
                                    <v-icon class="me-2">mdi-eye-outline</v-icon>
                                    View Targets
                                </v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </v-card-title>

                <v-divider class="my-0"/>

                <v-card-text>

                    <v-row align="end">

                        <!--Sales Target-->
                        <template v-if="tab === 'Sales Target' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">Sales Target ({{
                                        data?.targets?.sales_value?.percentage
                                    }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                        <span :class="`text-${getColor(data?.targets?.sales_value?.percentage)}`">
                                            {{ data?.targets?.sales_value?.achieved_text }}
                                        </span>
                                    /
                                    <span class="text-primary text-center">
                                            {{ data?.targets?.sales_value?.target_text }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.targets?.sales_value?.percentage)"
                                    height="8"
                                    :model-value="data?.targets?.sales_value?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>

                        <!--Target By HOD-->
                        <template v-if="tab === 'Sales Target' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">Target By HOD
                                    ({{ data?.team_targets?.sales_value?.percentage }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                        <span :class="`text-${getColor(data?.team_targets?.sales_value?.percentage)}`">
                                            {{ data?.team_targets?.sales_value?.achieved_text }}
                                        </span>
                                    /
                                    <span class="text-primary text-center">
                                            {{ data?.team_targets?.sales_value?.target_text }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.team_targets?.sales_value?.percentage)"
                                    height="8"
                                    :model-value="data?.team_targets?.sales_value?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>

                        <!--Live Listings-->
                        <template v-if="tab === 'Live Listings' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">Live Listings ({{
                                        data?.targets?.live_listings?.percentage
                                    }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                         <span :class="`text-${getColor(data?.targets?.live_listings?.percentage)}`">
                                            {{ data?.targets?.live_listings?.achieved_text }}
                                         </span>
                                    /
                                    <span class="text-primary text-center">
                                            {{ data?.targets?.live_listings?.target_text }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.targets?.live_listings?.percentage)"
                                    height="8"
                                    :model-value="data?.targets?.live_listings?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>

                        <!--New Listings-->
                        <template v-if="tab === 'New Listings' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">New Listings ({{
                                        data?.targets?.new_listings?.percentage
                                    }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                        <span :class="`text-${getColor(data?.targets?.new_listings?.percentage)}`">
                                            {{ data?.targets?.new_listings?.achieved_text }}
                                        </span>
                                    /
                                    <span class="text-primary text-center">
                                            {{ data?.targets?.new_listings?.target_text }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.targets?.new_listings?.percentage)"
                                    height="8"
                                    :model-value="data?.targets?.new_listings?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>

                        <!--New Listings-->
                        <template v-if="tab === 'TruChecked Listings' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">TruChecked Listings ({{
                                        data?.targets?.tru_checked_listings?.percentage
                                    }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                        <span :class="`text-${getColor(data?.targets?.tru_checked_listings?.percentage)}`">
                                            {{ data?.targets?.tru_checked_listings?.achieved_text }}
                                        </span>
                                    /
                                    <span class="text-primary text-center" v-tooltip:top="'Total active listing on Bayut'">
                                            {{ data?.targets?.tru_checked_listings?.target_text }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.targets?.tru_checked_listings?.percentage)"
                                    height="8"
                                    :model-value="data?.targets?.tru_checked_listings?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>

                        <!--Active Tru Broker-->
                        <template v-if="tab === 'Active TruBroker' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">TruBroker ({{
                                        data?.targets?.tru_broker?.percentage
                                    }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                        <span :class="`text-${getColor(data?.targets?.tru_broker?.percentage)}`">
                                          {{ data?.targets?.tru_broker?.achieved_text }}
                                        </span>
                                    /
                                    <span class="text-primary text-center" >
                                          {{ data?.targets?.tru_broker?.target_text }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.targets?.tru_broker?.percentage)"
                                    height="8"
                                    :model-value="data?.targets?.tru_broker?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>

                        <!--Active Super Agent-->
                        <template v-if="tab === 'Active Super Agent' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">Super Agent ({{
                                        data?.targets?.super_agent?.percentage
                                    }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                        <span :class="`text-${getColor(data?.targets?.super_agent?.percentage)}`">
                                            {{ data?.targets?.super_agent?.achieved_text }}
                                        </span>
                                    /
                                    <span class="text-primary text-center">
                                            {{ data?.targets?.super_agent?.target_text }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.targets?.super_agent?.percentage)"
                                    height="8"
                                    :model-value="data?.targets?.super_agent?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>

                        <!--Target By HOD-->
                        <template v-if="tab === 'Credit Limit' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">PF Credit Limit
                                    ({{ data?.credit_targets?.pf_credit_limit?.percentage }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                        <span :class="`text-${getColor(data?.credit_targets?.pf_credit_limit?.percentage)}`">
                                            {{ data?.credit_targets?.pf_credit_limit?.achieved || 0 }}
                                        </span>
                                    /
                                    <span class="text-primary text-center">
                                            {{ data?.credit_targets?.pf_credit_limit?.target || 0 }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.credit_targets?.pf_credit_limit?.percentage)"
                                    height="8"
                                    :model-value="data?.credit_targets?.pf_credit_limit?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>
                        <template v-if="tab === 'Credit Limit' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">Bayut Credit Limit
                                    ({{ data?.credit_targets?.bayut_credit_limit?.percentage }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                        <span :class="`text-${getColor(data?.credit_targets?.bayut_credit_limit?.percentage)}`">
                                            {{ data?.credit_targets?.bayut_credit_limit?.achieved || 0 }}
                                        </span>
                                    /
                                    <span class="text-primary text-center">
                                            {{ data?.credit_targets?.bayut_credit_limit?.target || 0 }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.credit_targets?.bayut_credit_limit?.percentage)"
                                    height="8"
                                    :model-value="data?.credit_targets?.bayut_credit_limit?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>
                        <template v-if="tab === 'Credit Limit' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">Dubizzle Credit Limit
                                    ({{ data?.credit_targets?.dubizzle_credit_limit?.percentage }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                        <span :class="`text-${getColor(data?.credit_targets?.dubizzle_credit_limit?.percentage)}`">
                                            {{ data?.credit_targets?.dubizzle_credit_limit?.achieved }}
                                        </span>
                                    /
                                    <span class="text-primary text-center">
                                            {{ data?.credit_targets?.dubizzle_credit_limit?.target }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.credit_targets?.dubizzle_credit_limit?.percentage)"
                                    height="8"
                                    :model-value="data?.credit_targets?.dubizzle_credit_limit?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>
                        <template v-if="tab === 'Pipelines' || tab === 'Overall'">
                            <v-col cols="12" md="7">
                                <div class="text-body-2">Pipelines
                                    ({{ data?.team_targets?.pipelines?.percentage }}%)
                                </div>
                            </v-col>
                            <v-col cols="12" md="5">
                                <div class="text-center">
                                        <span :class="`text-${getColor(data?.team_targets?.pipelines?.percentage)}`">
                                            {{ data?.team_targets?.pipelines?.achieved }}
                                        </span>
                                    /
                                    <span class="text-primary text-center">
                                            {{ data?.team_targets?.pipelines?.target }}
                                        </span>
                                </div>
                                <v-progress-linear
                                    :color="getColor(data?.team_targets?.pipelines?.percentage)"
                                    height="8"
                                    :model-value="data?.team_targets?.pipelines?.percentage"
                                    rounded
                                >
                                </v-progress-linear>
                            </v-col>
                        </template>


                    </v-row>

                    <v-divider/>

                    <div>
                        <p
                            v-for="(value, key) in data?.reports"
                            :key="key"
                            style="font-size: 12px"
                            class="ma-0"
                        >
                            {{ key }}: <b>{{ value }}</b>
                        </p>
                    </div>
                </v-card-text>

            </v-card>
        </v-col>
    </v-row>

    <v-dialog v-model="selectedGroup.modal" persistent width="600">
        <v-card>
            <v-card-title class="border-b">
                Edit Team KPI targets
            </v-card-title>
            <v-card-text>
                <v-text-field
                    v-model="formData.sales_target"
                    label="Sales Target Value (In Million)"
                    variant="outlined"
                    density="compact"
                    :messages="formatNumberMetricPrefixes(formData.sales_target || 0)"
                />
            </v-card-text>
            <v-card-text>
                <v-text-field
                    v-model="formData.pf_credit_limit"
                    label="PF Credit Limit"
                    variant="outlined"
                    density="compact"
                    messages="Note: It expire 2nd of every month"
                />
            </v-card-text>
            <v-card-text>
                <v-text-field
                    v-model="formData.bayut_credit_limit"
                    label="Bayut Credit Limit"
                    variant="outlined"
                    density="compact"
                    messages="Note: It expire 25th of every month"
                />
            </v-card-text>
            <v-card-text>
                <v-text-field
                    v-model="formData.dubizzle_credit_limit"
                    label="Dubizzle Credit Limit"
                    variant="outlined"
                    density="compact"
                    messages="Note: It expire 25th of every month"
                />
            </v-card-text>
            <v-card-text>
                <v-text-field
                    v-model="formData.pipelines"
                    label="Pipelines"
                    variant="outlined"
                    density="compact"
                    messages="No. of pipelines"
                />
            </v-card-text>

<!--            <v-card-text>-->
<!--                <v-text-field-->
<!--                    v-model="formData.dubizzle_credit_limit"-->
<!--                    label="Dubizzle Credit Limit"-->
<!--                    variant="outlined"-->
<!--                    density="compact"-->
<!--                    messages="Note: It expire 20th of every month"-->
<!--                />-->
<!--            </v-card-text>-->
            <v-card-actions class="border-t">
                <v-btn
                    size="small"
                    color="error"
                    variant="tonal"
                    @click="selectedGroup.modal = false; resetFormData()"
                >
                    Cancel
                </v-btn>
                <v-btn
                    @click="submit"
                    size="small"
                    color="primary"
                    variant="tonal"
                    :loading="selectedGroup.loading"
                >
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="teamKpiReport.modal" persistent width="1200">
        <v-card>
            <v-card-title class="border-b">
                Team KPI Report
            </v-card-title>
            <v-card-text>
                <v-table density="compact" class="border">
                    <thead>
                    <tr style="text-wrap: nowrap; font-size: 12px">
                        <th class="text-left">SR No.</th>
                        <th>Agent Name</th>
                        <th>Agent Start Date</th>
                        <th>Total Listings</th>
                        <th>No of Premium</th>
                        <th>No of Featured</th>
                        <th>Re-Listed</th>
                        <th>New Published</th>
                        <th>New Listings</th>
                        <th>Verified Listings</th>
                        <th>% of Verified Listings</th>
                        <th>Checked Listings</th>
                        <th>% of Checked Listings</th>
                        <th>TRU Checked Offplan</th>
                        <th>TRU Checked Ready</th>
                        <th>Total TRU Checked</th>
                        <th>% of TRU Checked</th>
                        <th>Eligible TRU Check</th>
                        <th>Super Agent</th>
                        <th>TRU Broker</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="(data, index) in teamKpiReport.data"
                        :key="index"
                        style="text-wrap: nowrap;font-size: 12px;"
                    >
                        <td>{{ index }}</td>
                        <td>{{ data?.name }}</td>
                        <td class="text-center">
                            {{ data?.agent_start_date }}
                        </td>
                        <td class="text-center">
                            {{ data?.total_listings }}
                        </td>
                        <td class="text-center">
                            {{ data?.no_of_premium }}
                        </td>
                        <td class="text-center">
                            {{ data?.no_of_featured }}
                        </td>
                        <td class="text-center">
                            {{ data?.re_listed }}
                        </td>
                        <td class="text-center">
                            {{ data?.new_published }}
                        </td>
                        <td class="text-center">
                            {{ data?.new_listings }}
                        </td>
                        <td class="text-center">
                            {{ data?.verified_listings }}
                        </td>
                        <td
                            class="text-center text-white font-weight-bold"
                            :style="`background: ${data?.percentage_of_verified_listings > 0 ? '#86EFAC' : '#FCA5A5'}`"
                        >
                            {{ data?.percentage_of_verified_listings }}
                        </td>
                        <td class="text-center">
                            {{ data?.checked_listings }}
                        </td>
                        <td
                            class="text-center text-white font-weight-bold"
                            :style="`background: ${data?.checked_listing_percentage > 0 ? '#86EFAC' : '#FCA5A5'}`"
                        >
                            {{ data?.checked_listing_percentage }}
                        </td>
                        <td class="text-center">
                            {{ data?.tru_checked_offplan }}
                        </td>
                        <td class="text-center">
                            {{ data?.tru_checked_ready }}
                        </td>
                        <td class="text-center">
                            {{ data?.total_tru_checked }}
                        </td>
                        <td
                            class="text-center text-white font-weight-bold"
                            :style="`background: ${data?.tru_checked_percentage > 0 ? '#86EFAC' : '#FCA5A5'}`"
                        >
                            {{ data?.tru_checked_percentage }}
                        </td>
                        <td class="text-center text-white font-weight-bold">
                            {{ data?.eligible_tru_check }}
                        </td>
                        <td
                            class="text-center text-white font-weight-bold"
                            :style="`background: ${data?.super_agent === 'Yes' ? '#86EFAC' : '#FCA5A5'}`"
                        >
                            {{ data?.super_agent }}
                        </td>
                        <td
                            class="text-center text-white font-weight-bold"
                            :style="`background: ${data?.tru_broker === 'Yes' ? '#86EFAC' : '#FCA5A5'}`"
                        >
                            {{ data?.tru_broker }}
                        </td>
                    </tr>
                    </tbody>
                </v-table>
            </v-card-text>
            <v-card-actions class="border-t">
                <v-btn
                    size="small"
                    color="error"
                    variant="tonal"
                    @click="teamKpiReport.modal = false;"
                >
                    Cancel
                </v-btn>
                <v-btn
                    size="small"
                    color="primary"
                    variant="tonal"
                    :loading="teamKpiReport.loading"
                >
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

</template>

<style scoped>

</style>
