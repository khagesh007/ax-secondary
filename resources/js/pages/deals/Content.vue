<template>
    <div>
        <div class="row">
            <div class="col-12">

                <div class="input-group pos">
                    <!--                <CommissionTypeSelect2/>-->
                </div>

            </div>
        </div>
        <ul class="nav nav-pills nav-custom nav-custom-light bg-white p-2 mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" @click="contentTab = 'overview'" href="#nav-light-home"
                   role="tab" aria-selected="true">
                    Overview
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" @click="contentTab = 'leads'" href="#nav-leads"
                   role="tab" aria-selected="true">
                    Leads
                </a>
            </li>
            <li v-if="formStep !== 1" class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" @click="contentTab = 'task'" href="#nav-light-profile"
                   role="tab"
                   aria-selected="false"
                   tabindex="-1">
                    Tasks
                </a>
            </li>
            <li v-if="formStep !== 1" class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" @click="contentTab = 'transactions'" href="#nav-commission"
                   role="tab" aria-selected="false"
                   tabindex="-1">
                    Transactions
                </a>
            </li>
            <li v-if="formStep !== 1" class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" @click="contentTab = 'attachments'" href="#nav-attachments"
                   role="tab" aria-selected="false"
                   tabindex="-1">
                    Attachments
                </a>
            </li>
            <li class="nav-item" v-if="showActivity" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" @click="contentTab = 'activities'" href="#nav-activities"
                   role="tab" aria-selected="false"
                   tabindex="-1">
                    Activities
                </a>
            </li>
            <li class="nav-item" v-if="showActivity" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" @click="contentTab = 'comments'" href="#nav-comments"
                   role="tab"
                   aria-selected="false"
                   tabindex="-1">
                    Comments
                </a>
            </li>
        </ul>
        <div class="tab-content text-muted">
            <TabOverview v-show="contentTab === 'overview'"></TabOverview>
            <Leads v-show="contentTab === 'leads'"></Leads>
            <Tasks v-show="contentTab === 'task'"></Tasks>
            <Transactions v-show="contentTab === 'transactions'"></Transactions>

            <Activities v-if="showActivity" v-show="contentTab === 'activities'"></Activities>
            <Comments v-if="showActivity" v-show="contentTab === 'comments'"></Comments>
        </div>
    </div>
</template>
<script setup>
import {ref, inject, onMounted} from 'vue';
import TabOverview from './tabs/OverView.vue'
import Tasks from "./tabs/Tasks.vue";
import Transactions from "./tabs/Transactions.vue";
import Activities from "./tabs/Activities.vue";
import Comments from "./tabs/Comments.vue";
import CommissionTypeSelect2 from "../../components/deals/CommissionTypeSelect2.vue";

const contentTab = ref('overview');
import {useDealStore} from "./store/dealState";
import Leads from "./tabs/Leads.vue";

const deal = useDealStore();
const {formStep} = useDealStore();
const showActivity = ref(false);


onMounted(() => {


})
</script>
<style>
.v-select {
    width: 100%;
}
</style>
