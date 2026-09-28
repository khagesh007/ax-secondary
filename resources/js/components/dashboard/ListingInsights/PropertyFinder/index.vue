<script setup>
import {computed, ref} from "vue";

import Piechart from "../../PieChart.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(["get-listing-data"]);

// Product-related data
const productData = computed(() => [
    {value: props.data?.counter?.standard, name: "Standard"},
    {value: props.data?.counter?.featured, name: "Featured"},
    {value: props.data?.counter?.premium, name: "Premium"},
]);

// Verification-related data
const verificationData = computed(() => [
    {value: props.data?.counter?.verified, name: "Verified"},
    {value: props.data?.counter?.unverified, name: "Unverified"},
]);

// Agent-related data
const agentData = computed(() => [
    {value: props.data?.counter?.super_agents, name: "Super Agents"},
    {value: props.data?.counter?.non_super_agents, name: "Non Super Agents"},
]);

const loading = ref(false);
const refresh = async () => {

    loading.value = true;

    let params = {
        flag: "pf-listings",
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

        <v-col cols="12" md="3" v-if="data?.counter?.active_listings">
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
                        <span class="font-weight-bold">Featured</span>
                    </v-chip>
                    <h6 class="text-h4 font-weight-bold mt-1 mb-0">
                        {{ data?.counter?.featured }}
                    </h6>
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" md="3">
            <v-card color="warning" variant="tonal">
                <v-card-text>
                    <v-chip variant="flat" color="warning" size="small" label>
                        <v-icon icon="mdi-crown-outline" start></v-icon>
                        <span class="font-weight-bold">Premium</span>
                    </v-chip>
                    <h6 class="text-h4 font-weight-bold mt-1 mb-0">
                        {{ data?.counter?.premium }}
                    </h6>
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" md="3">
            <v-card color="info" variant="tonal">
                <v-card-text>
                    <v-chip variant="flat" color="info" size="small" label>
                        <v-icon icon="mdi-pin" start></v-icon>
                        <span class="font-weight-bold">Standard</span>
                    </v-chip>
                    <h6 class="text-h4 font-weight-bold mt-1 mb-0">
                        {{ data?.counter?.standard }}
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
                        :data="productData"
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
                        title="Super Agents"
                        subtitle="Agent status at Property Finder"
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
