<template>
    <v-select
        :reduce="commission => commission.id"
        :options="commissionTypes"
        label="name"
        @search="commissionTypes"
        placeholder="Type to search commission"
    ></v-select>
</template>
<script setup>
import {onMounted, ref} from 'vue';


import useCommissionType from "../../composables/deals/dealsCommissionType";
const {commissionTypes, getCommissionTypes} = useCommissionType();
//vue-select
import "vue-select/dist/vue-select.css";
//import { VueSelect } from "vue-select";
import vSelect from 'vue-select';
onMounted(() => {
    getCommissionTypes();
});
</script>
