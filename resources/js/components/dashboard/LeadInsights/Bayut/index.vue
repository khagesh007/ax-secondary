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

// Product-related data
const signatureData = computed(() => [
    {value: props.data?.counter?.signature, name: "Signature"},
    {value: props.data?.counter?.hot, name: "Hot"},
    {value: props.data?.counter?.basic, name: "Basic"},
]);

// Verification-related data
const verificationData = computed(() => [
    {value: props.data?.counter?.listing_truChecked, name: "TruChecked Listings"},
    {value: props.data?.counter?.listings_live, name: "Live Listings"},
]);

// Agent-related data
const agentData = computed(() => [
    {value: props.data?.counter?.agents_truBroker, name: "TruBroker Agent"},
    {value: props.data?.counter?.agents_total , name: "Agents"},
]);

</script>

<template>
    <v-row>
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
                        :colors="['#45cb85', '#FB8B00', '#299cdb']"
                    />
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="12" md="4">
            <v-card variant="flat" border>
                <v-card-text>
                    <Piechart
                        title="Verified"
                        subtitle="Distribution of listing levelssss"
                        icon="✔️"
                        :data="verificationData"
                        :colors="['#45cb85', '#FB8B00', '#299cdb']"
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
                        :colors="['#45cb85', '#FB8B00', '#299cdb']"
                    />
                </v-card-text>
            </v-card>
        </v-col>

    </v-row>
</template>

<style scoped>

</style>
