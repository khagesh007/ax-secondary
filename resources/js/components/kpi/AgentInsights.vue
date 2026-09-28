<script setup>

import {ref} from "vue";
import moment from "moment";
const dialog = ref(false);
const selectedImageUrl = ref('');
const props = defineProps({
    tab: {
        type: String,
        required: true,
    },
    data: {
        type: Object,
        required: true,
    },
    negativeData: {
        type: Object,
        default: () => ({}),
    },
    date: {
        type: [String, Date],
        required: true,
    },
})

const emit = defineEmits(['refresh-members-data'])

const activePanel = ref([]);

const getColor = (percentage) => {
    if (percentage < 50) {
        return 'error'
    } else if (percentage < 75) {
        return 'warning'
    } else {
        return 'success'
    }
}

const selectedAgent = ref({
    loading: false,
    modal: false,
    data: null,
})

const setSelectedAgent = (agent, manager_id) => {
    selectedAgent.value.data = agent

    formData.value.agent_id = selectedAgent.value?.data?.agent?.id || null
    formData.value.manager_id = manager_id
    formData.value.sales_target = selectedAgent.value?.data?.targets[0]?.target || 0
    formData.value.live_listings = selectedAgent.value?.data?.targets[1]?.target || 0
    formData.value.new_listings = selectedAgent.value?.data?.targets[2]?.target || 0
    console.log(selectedAgent.value)
    //find target where kpi is "pf_credit_limit" selectedAgent.value?.data?.portal_target
    let pf_credit_target = selectedAgent.value?.data?.portal_target.find(target => target.kpi === 'pf_credit_limit')
    let bayut_credit_target = selectedAgent.value?.data?.portal_target.find(target => target.kpi === 'bayut_credit_limit')
    let dubizzle_credit_target = selectedAgent.value?.data?.portal_target.find(target => target.kpi === 'dubizzle_credit_limit')
    formData.value.pf_credit_limit = pf_credit_target?.target || 0
    formData.value.bayut_credit_limit = bayut_credit_target?.target || 0
    formData.value.dubizzle_credit_limit = dubizzle_credit_target?.target || 0

    formData.value.month = moment(props.date).format('YYYY-MM')

    selectedAgent.value.modal = true
}

const formData = ref({
    agent_id: null,
    manager_id: null,
    sales_target: null,
    live_listings: null,
    new_listings: null,
    pipelines: null,
    pf_credit_limit: null,
    bayut_credit_limit: null,
    dubizzle_credit_limit: null,
    month: null
})

const resetFormData = () => {
    formData.value.agent_id = null
    formData.value.manager_id = null
    formData.value.sales_target = null
    formData.value.live_listings = null
    formData.value.new_listings = null
    formData.value.month = null
}

const submit = async () => {
    selectedAgent.value.loading = true

    try {
        const response = await axios.post('/reports/kpi-v3/agent-target-update', formData.value)
        if (response.data.status) {
            selectedAgent.value.modal = false
            emit('refresh-members-data')
        }
    } catch (e) {
        console.error(e)
    } finally {
        selectedAgent.value.loading = false
        selectedAgent.value.modal = false
        resetFormData()
    }
}
const openModal = (imageUrl) => {
    selectedImageUrl.value = imageUrl;
    dialog.value = true;
};

</script>

<template>

    <v-expansion-panels v-model="activePanel" elevation="0" class="border rounded">
        <v-expansion-panel
            v-for="(group, index) in data"
            :key="group.group_id"
            :value="index"
        >
            <v-expansion-panel-title>
                <div class="d-flex align-center justify-space-between w-100">
                    <div class="d-flex align-center gap-2">
                        <VAvatar rounded color="primary" variant="tonal" size="36">
                            <VImg :src="group?.user_image"/>
                        </VAvatar>
                        <div>
                            <div>{{ group?.user_name }}</div>
                            <div style="font-size: 10px">Manager</div>
                        </div>
                    </div>
                    <p class="text-body-1 font-weight-medium text-primary ma-0">
                        {{ group?.group_name }}
                    </p>
                </div>
            </v-expansion-panel-title>
            <v-expansion-panel-text class="border-b py-5">
                <v-row>
                    <v-col v-for="member in group.members || []" cols="12" xl="3" lg="4" md="6">
                        <v-card flat border>

                            <v-card-title class="d-flex align-center justify-space-between">
                                <div class="d-flex align-center gap-2">
                                    <VAvatar rounded color="primary" variant="tonal" size="36" @click="openModal(member?.agent?.image_url)" style="cursor: pointer;">
                                        <VImg :src="member?.agent?.image_url"/>
                                    </VAvatar>
                                    <div>
                                        <div class="d-flex align-center gap-2">
                                            <div class="text-body-2 font-weight-regular">
                                                {{ member?.agent?.name }}
                                            </div>
                                            <v-icon v-if="member?.flags?.is_truBroker" size="18"
                                                    v-tooltip:top="`TruBroker`" icon="mdi-check-decagram-outline"
                                                    color="#22C55E"/>
                                            <v-icon v-if="member?.flags?.is_super_agent" size="18"
                                                    v-tooltip:top="`Super Agent`" icon="mdi-star-check-outline"
                                                    color="#EAB308"/>
                                        </div>
                                        <div style="font-size: 10px">{{ member?.agent?.role_list[0] }}</div>
                                    </div>
                                </div>
                                <v-menu v-if="member?.permissions?.view || member?.permissions?.edit">
                                    <template v-slot:activator="{ props }">
                                        <v-btn color="#6B7280" rounded icon="mdi-dots-vertical" variant="text"
                                               size="small" v-bind="props"></v-btn>
                                    </template>

                                    <v-list width="200px">
                                        <v-list-item
                                            @click="setSelectedAgent(member,group.group_id)"
                                            v-if="member?.permissions?.edit"
                                        >
                                            <v-list-item-subtitle>
                                                <v-icon class="me-2">mdi-square-edit-outline</v-icon>
                                                Edit Targets
                                            </v-list-item-subtitle>
                                        </v-list-item>
                                        <v-list-item
                                            v-if="member?.permissions?.view"
                                            :href="member?.permissions?.view_url"
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
                                <template v-for="target in member.targets">
                                    <v-row
                                        v-if="target.kpi_title === tab || tab === 'Overall'"
                                        dense
                                        align="end"
                                    >
                                        <v-col cols="12" md="6">
                                            <p style="font-size: 12px" class="ma-0">
                                                {{ target?.kpi_title }}
                                                ({{ target?.percentage }}%)
                                            </p>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <p class="text-center ma-0" style="font-size: 12px">
                                                <span :class="`text-${getColor(target?.percentage)}`">
                                                    {{ target?.achieved_text }}
                                                </span>
                                                /
                                                <span class="text-primary text-center">
                                                     <span v-if="target?.kpi == 'tru_checked_listings'" v-tooltip:top="'Total active listing on Bayut'">
                                                         {{ target?.target_text }}
                                                            </span>
                                                        <span v-else>
                                                        {{ target?.target_text }}
                                                        </span>
                                                </span>
                                            </p>
                                            <v-progress-linear
                                                :color="getColor(target?.percentage)"
                                                height="6"
                                                :model-value="target?.percentage"
                                                rounded
                                            >
                                            </v-progress-linear>
                                        </v-col>
                                    </v-row>
                                </template>
                                <template v-for="targetP in member.portal_target">
                                    <v-row
                                        v-if="targetP.kpi_title === tab || tab === 'Overall'"
                                        dense
                                        align="end"
                                    >
                                        <v-col cols="12" md="6">
                                            <p style="font-size: 12px" class="ma-0">
                                                {{ targetP?.kpi_title }}
                                                ({{ targetP?.percentage }}%)
                                            </p>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <p class="text-center ma-0" style="font-size: 12px">
                                                <span :class="`text-${getColor(targetP?.percentage)}`">
                                                    {{ targetP?.achieved }}
                                                </span>
                                                /
                                                <span class="text-primary text-center">

                                                        <span>
                                                        {{ targetP?.target }}
                                                        </span>
                                                </span>
                                            </p>
                                            <v-progress-linear
                                                :color="getColor(targetP?.percentage)"
                                                height="6"
                                                :model-value="targetP?.percentage"
                                                rounded
                                            >
                                            </v-progress-linear>
                                        </v-col>
                                    </v-row>
                                </template>

                                <v-divider/>

                                <v-row dense>
                                    <v-col v-for="other in member?.other_reports" cols="12" md="4"
                                           class="d-flex justify-center">
                                        <div class="d-flex gap-1" v-tooltip:top="other?.kpi_title">
                                            <v-icon color="#6B7280" :icon="other?.icon" size="small"/>
                                            <p class="ma-0" style="color: #6B7280">{{ other?.total }}</p>
                                        </div>
                                    </v-col>
                                    <v-col v-for="other in member?.credit_data" cols="12" md="4"
                                           class="d-flex justify-center">
                                        <div class="d-flex gap-1" v-tooltip:top="other?.kpi_title">
                                            <v-icon color="#6B7280" :icon="other?.icon" size="small"/>
                                            <p class="ma-0" style="color: #6B7280">{{ other?.total }}</p>
                                        </div>
                                    </v-col>
                                </v-row>

                                <v-divider/>

                                <div v-if="member?.flags?.prev_truBroker || member?.flags?.prev_super_agent"
                                     class="d-flex justify-space-between align-center">
                                    <span style="font-size: 12px; color: #6B7280">
                                        Previous Month:
                                    </span>
                                    <div>
                                        <v-icon v-if="member?.flags?.prev_truBroker" v-tooltip:top="`TruBroker`"
                                                icon="mdi-check-decagram-outline" class="me-2" color="#22C55E"/>
                                        <v-icon v-if="member?.flags?.prev_super_agent" v-tooltip:top="`Super Agent`"
                                                icon="mdi-star-check-outline" color="#EAB308"/>
                                    </div>
                                </div>
                                <div v-for="(prev_data , key2) in member?.previous_month_data" :key="key2">
                                    <p
                                        v-tooltip:top="`Previous month data`"
                                        style="font-size: 12px; color: #6B7280"
                                        class="ma-0"
                                    >
                                        {{ prev_data?.title }}: <b>{{ prev_data?.achieved }}</b>
                                    </p>
                                </div>

                                <div>
                                    <p
                                        v-for="(value, key) in member?.reports"
                                        :key="key"
                                        style="font-size: 12px; color: #6B7280"
                                        class="ma-0"
                                    >
                                        {{ key }}: <b>{{ value }}</b>
                                    </p>
                                </div>

                            </v-card-text>

                        </v-card>
                    </v-col>
                </v-row>
                <hr>
                <v-row>
                    <v-col v-for="member in group.negativeMembers || []" cols="12" xl="3" lg="4" md="6">
                        <v-card flat border>

                            <v-card-title class="d-flex align-center justify-space-between" style="background: rgb(248 235 235);">
                                <div class="d-flex align-center gap-2">
                                    <VAvatar rounded color="primary" variant="tonal" size="36" @click="openModal(member?.agent?.image_url)" style="cursor: pointer;">
                                        <VImg :src="member?.agent?.image_url"/>
                                    </VAvatar>
                                    <div>
                                        <div class="d-flex align-center gap-2">
                                            <div class="text-body-2 font-weight-regular">
                                                {{ member?.agent?.name }}
                                            </div>
                                            <v-icon v-if="member?.flags?.is_truBroker" size="18"
                                                    v-tooltip:top="`TruBroker`" icon="mdi-check-decagram-outline"
                                                    color="#22C55E"/>
                                            <v-icon v-if="member?.flags?.is_super_agent" size="18"
                                                    v-tooltip:top="`Super Agent`" icon="mdi-star-check-outline"
                                                    color="#EAB308"/>
                                        </div>
                                        <div style="font-size: 10px">{{ member?.agent?.role_list[0] }}</div>
                                    </div>
                                </div>
                                <v-menu v-if="member?.permissions?.view || member?.permissions?.edit">
                                    <template v-slot:activator="{ props }">
                                        <v-btn color="#6B7280" rounded icon="mdi-dots-vertical" variant="text"
                                               size="small" v-bind="props"></v-btn>
                                    </template>

                                    <v-list width="200px">
                                        <v-list-item
                                            @click="setSelectedAgent(member,group.group_id)"
                                            v-if="member?.permissions?.edit"
                                        >
                                            <v-list-item-subtitle>
                                                <v-icon class="me-2">mdi-square-edit-outline</v-icon>
                                                Edit Targets
                                            </v-list-item-subtitle>
                                        </v-list-item>
                                        <v-list-item
                                            v-if="member?.permissions?.view"
                                            :href="member?.permissions?.view_url"
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
                                <template v-for="target in member.targets">
                                    <v-row
                                        v-if="target.kpi_title === tab || tab === 'Overall'"
                                        dense
                                        align="end"
                                    >
                                        <v-col cols="12" md="6">
                                            <p style="font-size: 12px" class="ma-0">
                                                {{ target?.kpi_title }}
                                                ({{ target?.percentage }}%)
                                            </p>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <p class="text-center ma-0" style="font-size: 12px">
                                                <span :class="`text-${getColor(target?.percentage)}`">
                                                    {{ target?.achieved_text }}
                                                </span>
                                                /
                                                <span class="text-primary text-center">
                                                     <span v-if="target?.kpi == 'tru_checked_listings'" v-tooltip:top="'Total active listing on Bayut'">
                                                         {{ target?.target_text }}
                                                            </span>
                                                        <span v-else>
                                                        {{ target?.target_text }}
                                                        </span>
                                                </span>
                                            </p>
                                            <v-progress-linear
                                                :color="getColor(target?.percentage)"
                                                height="6"
                                                :model-value="target?.percentage"
                                                rounded
                                            >
                                            </v-progress-linear>
                                        </v-col>
                                    </v-row>
                                </template>
                                <template v-for="target in member.portal_target">
                                    <v-row
                                        v-if="target.kpi_title === tab || tab === 'Overall'"
                                        dense
                                        align="end"
                                    >
                                        <v-col cols="12" md="6">
                                            <p style="font-size: 12px" class="ma-0">
                                                {{ target?.kpi_title }}
                                                ({{ target?.percentage }}%)
                                            </p>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <p class="text-center ma-0" style="font-size: 12px">
                                                <span :class="`text-${getColor(target?.percentage)}`">
                                                    {{ target?.achieved_text }}
                                                </span>
                                                /
                                                <span class="text-primary text-center">
                                                     <span v-if="target?.kpi == 'tru_checked_listings'" v-tooltip:top="'Total active listing on Bayut'">
                                                         {{ target?.target_text }}
                                                            </span>
                                                        <span v-else>
                                                        {{ target?.target_text }}
                                                        </span>
                                                </span>
                                            </p>
                                            <v-progress-linear
                                                :color="getColor(target?.percentage)"
                                                height="6"
                                                :model-value="target?.percentage"
                                                rounded
                                            >
                                            </v-progress-linear>
                                        </v-col>
                                    </v-row>
                                </template>

                                <v-divider/>

                                <v-row dense>
                                    <v-col v-for="other in member?.other_reports" cols="12" md="4"
                                           class="d-flex justify-center">
                                        <div class="d-flex gap-1" v-tooltip:top="other?.kpi_title">
                                            <v-icon color="#6B7280" :icon="other?.icon" size="small"/>
                                            <p class="ma-0" style="color: #6B7280">{{ other?.total }}</p>
                                        </div>
                                    </v-col>
                                </v-row>

                                <v-divider/>

                                <div v-if="member?.flags?.prev_truBroker || member?.flags?.prev_super_agent"
                                     class="d-flex justify-space-between align-center">
                                    <span style="font-size: 12px; color: #6B7280">
                                        Previous Month:
                                    </span>
                                    <div>
                                        <v-icon v-if="member?.flags?.prev_truBroker" v-tooltip:top="`TruBroker`"
                                                icon="mdi-check-decagram-outline" class="me-2" color="#22C55E"/>
                                        <v-icon v-if="member?.flags?.prev_super_agent" v-tooltip:top="`Super Agent`"
                                                icon="mdi-star-check-outline" color="#EAB308"/>
                                    </div>
                                </div>
                                <div v-for="(prev_data , key2) in member?.previous_month_data" :key="key2">
                                    <p
                                        v-tooltip:top="`Previous month data`"
                                        style="font-size: 12px; color: #6B7280"
                                        class="ma-0"
                                    >
                                        {{ prev_data?.title }}: <b>{{ prev_data?.achieved }}</b>
                                    </p>
                                </div>

                                <div>
                                    <p
                                        v-for="(value, key) in member?.reports"
                                        :key="key"
                                        style="font-size: 12px; color: #6B7280"
                                        class="ma-0"
                                    >
                                        {{ key }}: <b>{{ value }}</b>
                                    </p>
                                </div>

                            </v-card-text>

                        </v-card>
                    </v-col>
                </v-row>
            </v-expansion-panel-text>
        </v-expansion-panel>
    </v-expansion-panels>

    <v-dialog v-model="selectedAgent.modal" persistent width="600">
        <v-card>
            <v-card-title class="border-b">
                Edit Agent KPI targets
            </v-card-title>
            <v-card-text>
                <v-text-field
                    v-model="formData.sales_target"
                    label="Sales Target"
                    variant="outlined"
                    density="compact"
                    messages="Note: It must be like this 4300000 for 4.3M"
                />
                <v-text-field
                    v-model="formData.live_listings"
                    class="mt-4"
                    label="Number of Listings (Live)"
                    variant="outlined"
                    density="compact"
                />
                <v-text-field
                    v-model="formData.new_listings"
                    class="mt-2"
                    label="Number of Listings (New)"
                    variant="outlined"
                    density="compact"
                />

                <v-text-field
                    v-model="formData.pipelines"
                    class="mt-2"
                    label="Number of Pipelines"
                    variant="outlined"
                    density="compact"
                />

                <!-- <v-text-field
                    v-model="formData.pf_credit_limit"
                    class="mt-2"
                    label="Property Finder Credit Limit"
                    variant="outlined"
                    density="compact"
                    messages="Note: It expire 25th of every month"
                />
                <v-text-field
                    v-model="formData.bayut_credit_limit"
                    class="mt-2"
                    label="Bayut Credit Limit"
                    variant="outlined"
                    density="compact"
                    messages="Note: It expire 20th of every month"
                />
                <v-text-field
                    v-model="formData.dubizzle_credit_limit"
                    class="mt-2"
                    label="Dubizzle Credit Limit"
                    variant="outlined"
                    density="compact"
                    messages="Note: It expire 20th of every month"
                /> -->
            </v-card-text>
            <v-card-actions class="border-t">
                <v-btn
                    size="small"
                    color="error"
                    variant="tonal"
                    @click="selectedAgent.modal = false; resetFormData()"
                >
                    Cancel
                </v-btn>
                <v-btn
                    @click="submit"
                    size="small"
                    color="primary"
                    variant="tonal"
                    :loading="selectedAgent.loading"
                >
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <v-dialog v-model="dialog" max-width="800px">
        <v-card>
            <v-card-text>
                <v-img :src="selectedImageUrl" :aspect-ratio="16/9" contain />
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="dialog = false">Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style scoped>

</style>
