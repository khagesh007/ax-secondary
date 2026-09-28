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

const colors = ['#F0FDF4', '#FEFCE8', '#EFF6FF', '#FAF5FF', '#FEF2F2']

</script>

<template>
    <v-row>
        <v-col v-for="(data,key,index) in overview" cols="12" md="4">
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
                        <div class="text-body-1 text-center text-primary">
                            Target: {{ data?.target_text }}
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
</template>

<style scoped>

</style>
