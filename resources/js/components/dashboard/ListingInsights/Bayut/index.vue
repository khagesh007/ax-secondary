<script setup>
import {nextTick, onMounted, ref, watch, computed} from "vue";

import Barchart from "../../BarChart.vue";
import Piechart from "../../PieChart.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(["get-listing-data"]);

// Product-related data
const signatureData = computed(() => [
    {value: props.data?.counter?.signature, name: "Signature"},
    {value: props.data?.counter?.hot, name: "Hot"},
    {value: props.data?.counter?.basic, name: "Basic"},
]);

// Verification-related data
const verificationData = computed(() => [
    {value: props.data?.counter?.listing_checked ?? 0, name: "Checked Listings"},
    {value: props.data?.counter?.listing_truChecked ?? 0, name: "TruChecked Listings"},
    {value: props.data?.counter?.listing_other, name: "Other Listings"},
]);

// Agent-related data
const agentData = computed(() => [
    {value: props.data?.counter?.agents_truBroker, name: "TruBroker Agent"},
    {value: props.data?.counter?.agents_total, name: "Agents"},
]);

const loading = ref(false);
const refresh = async () => {

    loading.value = true;

    let params = {
        flag: "bayut-listings",
    };

    try {
        const response = await axios.get("/reporting/dashboard/refresh", {params: params});
        if (response.data.status) {
            console.log(response.data.message);
            emit("get-listing-data", false);
        }

    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="d-flex align-center justify-end gap-2 text-primary">
        <v-btn
            @click="refresh"
            icon
            size="small"
            color="info"
            variant="text"
            :disabled="loading"
            :loading="loading"
        >
            <v-icon icon="mdi-refresh" size="20"/>
        </v-btn>
        <span>Last Fetched: &nbsp;</span>
        {{ data?.counter?.last_updated_at }}
    </div>
    <v-row>
        <v-col cols="12" md="3">
            <v-card color="secondary" variant="tonal">
                <v-card-text class="d-flex justify-space-between align-items-end">
                    <div>
                        <h6 class="text-h6 mb-0">
                            Active Listings
                        </h6>
                        <h4 class="text-h4 font-weight-bold mb-0">
                            {{ data?.counter?.active_listings || 0 }}
                        </h4>
                    </div>

                    <div>
                        <div>Rent: {{ data?.counter?.active_rental_listings }}</div>
                        <div>Sale: {{ data?.counter?.active_sale_listings }}</div>
                    </div>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" md="3">
            <v-card color="success" variant="tonal">
                <v-card-text>
                    <v-chip variant="flat" color="success" size="small" label>
                        <v-icon icon="mdi-star-outline" start></v-icon>
                        <span class="font-weight-bold">Signature</span>
                    </v-chip>
                    <h6 class="text-h4 font-weight-bold mt-1 mb-0">
                        {{ data?.counter?.signature }}
                    </h6>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" md="3">
            <v-card color="warning" variant="tonal">
                <v-card-text>
                    <v-chip variant="flat" color="warning" size="small" label>
                        <v-icon icon="mdi-crown-outline" start></v-icon>
                        <span class="font-weight-bold">Hot</span>
                    </v-chip>
                    <h6 class="text-h4 font-weight-bold mt-1 mb-0">
                        {{ data?.counter?.hot }}
                    </h6>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" md="3">
            <v-card color="info" variant="tonal">
                <v-card-text>
                    <v-chip variant="flat" color="info" size="small" label>
                        <v-icon icon="mdi-pin" start></v-icon>
                        <span class="font-weight-bold">Basic</span>
                    </v-chip>
                    <h6 class="text-h4 font-weight-bold mt-1 mb-0">
                        {{ data?.counter?.basic }}
                    </h6>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
    <v-row>

        <v-col cols="12" md="4">
            <v-card variant="flat" border>
                <v-card-text>
                    <Piechart
                        title="Product"
                        subtitle="Distribution of listing levels"
                        icon="⭐"
                        :data="signatureData"
                    />
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" md="4">
            <v-card variant="flat" border>
                <v-card-text>
                    <Piechart
                        title="Verified"
                        subtitle="Distribution of listing levels"
                        icon="✔️"
                        :data="verificationData"
                    />
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" md="4">
            <v-card variant="flat" border>
                <v-card-text>
                    <Piechart
                        title="TruBroker Agents"
                        subtitle="Distribution of listing levels"
                        icon="👤"
                        :data="agentData"
                    />
                </v-card-text>
            </v-card>
        </v-col>

    </v-row>
</template>

<style scoped>

</style>
