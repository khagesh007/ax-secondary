<template>

    <div class="modal fade" id="dealSplitModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Split Deal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="modalClose()"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <form @submit.prevent="onSubmit" enctype="multipart/form-data">

                            <div class="row g-3">

                                <div class="col-2">
                                    <input type="hidden" name="deal_id">
                                    Deal Price:
                                </div>
                                <div class="col-4">
                                    AED {{ formatNumber(deal.deal_price) }}

                                </div>
                                <div class="col-2">
                                    Deal Type

                                </div>
                                <div class="col-4">
                                    {{ deal.property.for }}
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">Gross Commission</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">
                                    AED {{ formatNumber(deal.gross_commission) }}
                                </div>
                                <div class="col-2">
                                    Net Commission:

                                </div>
                                <div class="col-4">
                                    AED {{ formatNumber(deal.net_commission) }}
                                </div>
                            </div>

                            <hr>
                            <div class="row" v-if="splits" v-for="(split, index) in splits" :key="index">
                                <div class="col-1 mt-2">
                                    Agent

                                </div>
                                <div class="col-4 mt-2">
                                    <img :src="split.user.thumb_url"  class="avatar-xs rounded-circle">
                                    {{ split.user.full_name }}
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">Amount</div>
                                <div class="col-md-5  col-sm-12 mt-2 pr-0">
                                    AED {{ formatNumber(split.split_amount) }}
                                </div>
                            </div>
                            <hr>
                            <div class="row p-2">

                                <h5>Create New Split</h5>

                                <div class="col-md-2  col-sm-12 mt-2">Agent</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">
                                    <div class="input-group">
                                        <UserSelect @userSelected="userSelected" :roles="['Agent']" :passedUsers="[]"
                                                    :multiple="false"
                                        />
                                        <div v-html="agent_error"></div>
                                    </div>
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">
                                    Source
                                </div>   <div class="col-md-4  col-sm-12 mt-2 pr-0">
                                    <VueMultiselect
                                        v-model="formData.source"
                                        :id="index"
                                        :allowEmpty="true"
                                        :searchable="true"
                                        :close-on-select="true"
                                        :show-label="true"
                                        label="name"
                                        :options="sourceList"
                                        :multiple="false"
                                        :taggable="true"
                                        :selectLabel="''"
                                        track-by="name"
                                        @Select="dynamicDocTypeSelected($event,item)"
                                        @tag="addDynamicDocType($event,item)"
                                    >
                                    </VueMultiselect>
                                </div>
                                <div class="col-md-3  col-sm-12 mt-2">Commission
                                <small>(From net commission)</small>
                                </div>
                                <div class="col-md-3  col-sm-12 mt-2 pr-0">

                                    <div class="input-group">
                                        <span class="input-group-text">%</span>
                                        <input type="text" @keyup="calculateCommission()" v-model="formData.commission"
                                               class="form-control" name="commission">
                                    </div>
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">Amount</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-sizing-default">AED</span>
                                        <input @keyup="calculateCommission2()" type="text" class="form-control" v-model="formData.split_amount"
                                               name="deal_price" aria-label="Sizing example input"
                                               aria-describedby="inputGroup-sizing-default">
                                    </div>
                                </div>

                            </div>


                            <div class="mt-4">

                                <div class="hstack gap-2 justify-content-right float-end">
                                    <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                       data-bs-dismiss="modal" @click="modalClose()"><i
                                        class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="submit" class="btn btn-success action-add">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import {ref, onMounted, watchEffect, onUnmounted} from 'vue'
import {useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
import useGlobalHelper from "../../../composables/helpers/global";
import axios from "axios";

const emit = defineEmits(['removeLeadModal']);
const modalLoader = ref(true);
const {getLeadTypes, leadTypes, createSplit,  updateDeal} = useDeals();
const {leadResources} = useDealStore();
const deal_state = useDealStore();
const leadTypeOptions = ref([]);
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';
import UserSelect from "../../../components/formElements/UserSelect.vue";
import VueMultiselect from "vue-multiselect";

const {timeAgo, getCountries, countries} = useGlobalHelper();
const deal = deal_state.data;
const agent_error = ref('');
const props = defineProps({
    splits: {
        type: Object,
        required: false
    }
});
const documents = ref([]);
const sourceList = [
    {name: 'Seller Side', id: 'Buyer Side'},
    {name: 'Buyer Side', id: 'Buyer Side'},
    {name: 'Cashback', id: 'Cashback'},
];

const formData = ref({
    deal_id: deal.id,
    deal_price: deal.deal_price,
    agent_id: '',
    commission: '',
    split_amount: '',
    source: '',
});
const addDynamicDocType = (newTag, target) => {
    const tag = {
        name: newTag,
        id: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
    }
    sourceList.push(tag)
    target.commission_sources = tag;
}
const dynamicDocTypeSelected = (option, target) => {
    // target.source = option.name;
}
const calculateCommission = () => {
    let comm_amount = deal.net_commission * formData.value.commission / 100;
    formData.value.split_amount = numberFormat(comm_amount);
}
const  calculateCommission2 = () =>{
    let netCommission = deal.net_commission.replace(/,/g, '');
    let commissionAmount =  formData.value.split_amount.replace(/,/g, '');

    let temp =   commissionAmount / (netCommission / 100);
    formData.value.commission = numberFormat(temp);

}
// const onFileChange = (name, title) => {
//     let file = document.getElementById('document_' + name).files[0];
//     formData.value[name] = URL.createObjectURL(file);
// }

const userSelected = (user) => {
    formData.value.agent_id = user.id;
    // if(props.splits){
    //     let check = props.splits.find((split) => split.user.id == user.id);
    //     if(check){
    //     console.log('Agent already selected')
    //     }
    // }

}

const modalClose = () => {
    console.log('close')
    emit('removeDealSplitModal');
    modalLoader.value = false;
}
const onSubmit = () => {
    //formdata remove comma from deal price and split amount

    formData.value.split_amount = formData.value.split_amount.replace(/,/g, '');
    const formRes = createSplit(formData.value).then(res => {
        deal.data = res.data.data;
        if (res.data.status === '1') {
            $('#dealModal').modal('hide');
            modalLoader.value = false;
            emit('removeDealSplitModal');
        }
    }).catch(err => {
        console.log(err);
    });

}
onMounted(() => {
    $('#dealSplitModal').modal('show');
    setTimeout(() => {
        modalLoader.value = false;
    }, 1000);
});

onUnmounted(() => {
    $('#dealSplitModal').modal('hide');
    modalLoader.value = true;
});
const formatNumber = (val) => {
    return numberFormat(val);
}
</script>
<style scoped>

.modal {
    position: fixed !important;

}
.v-select .vs__dropdown-toggle{
    min-height: 40px !important;
}
</style>
