<script setup>

const props = defineProps({
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

const getColor = (percentage) => {
    if (percentage < 50) {
        return 'error'
    } else if (percentage < 75) {
        return 'warning'
    } else {
        return 'success'
    }
}

</script>

<template>
    <v-row>
        <v-col cols="12">
            <v-card flat border>
                <v-card-text class="pa-3">
                    <v-row align="center">
                        <v-col cols="12" md="4">
                            <v-card-title class="text-primary py-0">Live Listings</v-card-title>
                            <v-card-subtitle>Percentage of Overall Live Listings Achieved</v-card-subtitle>
                        </v-col>
                        <v-col cols="12" md="4">
                            <div class="text-h6 text-center text-primary">
                                Target: {{ overview?.target_text }}
                            </div>
                            <div
                                class="text-h6 text-center"
                                :class="`text-${getColor(overview?.percentage)}`"
                            >
                                Achieved: {{ overview?.achieved_text }}
                            </div>
                        </v-col>
                        <v-col cols="12" md="4" class="d-flex justify-end">
                            <v-progress-circular
                                :model-value="overview?.percentage"
                                :rotate="360"
                                :size="80"
                                :width="12"
                                :color="getColor(overview?.percentage)"
                            >
                                <template v-slot:default>
                                    <div
                                        class="text-body-2 ma-0"
                                        :class="`text-${getColor(overview?.percentage)}`"
                                    >
                                        {{ overview?.percentage }}%
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
                <v-card color="#EFF6FF" flat
                        class="rounded-0 overflow-visible pa-3">
                    <v-row dense>
                        <v-col cols="12" md="10">
                            <div class="d-flex align-center gap-2">
                                <VAvatar rounded color="primary" variant="tonal" size="48">
                                    <VImg
                                        :src="data?.user?.image"/>
                                </VAvatar>
                                <div>
                                    <div class="text-body-1">{{ data?.user?.name }}</div>
                                    <div style="font-size: 10px">Manager</div>
                                </div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="2">
                            <h6 class="ma-0 text-h6 text-primary text-right">{{ key }}</h6>
                        </v-col>
                    </v-row>

                </v-card>
                <v-card-text>

                    <v-row>
                        <v-col cols="12">
                            <v-row align="end">
                                <v-col cols="12" md="7">
                                    <div class="text-body-2 font-weight-bold">Live Listings ({{
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
                            </v-row>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<style scoped>

</style>
