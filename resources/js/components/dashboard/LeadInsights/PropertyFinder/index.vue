<script setup>
import {computed} from "vue";

import Piechart from "../../PieChart.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
})

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
    {value: props.data?.counter?.total_agents, name: "Agents"},
]);

</script>

<template>
    <v-row>
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
