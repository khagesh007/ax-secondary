<template>
    <div>
    <div class="tab-pane active show" id="nav-light-home" role="tabpanel">
        <div v-show="placeholder" class="row">
            <div class="col-6" v-for="index in 2" :key="index">
                <CardPlaceholder></CardPlaceholder>
            </div>
            <div class="col-12">
                <CardPlaceholder></CardPlaceholder>
            </div>
        </div>
        <div class="row" v-show="!placeholder">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        Deal Details

                        <div class="float-end">
                            <button @click="editDeal()" class="btn btn-sm btn-info"> <i class="ri-edit-box-line cursor-pointer"></i> </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item text-muted">Deal ID <span
                                class="float-end">#{{ deals.id }}</span>
                            </li>


                            <li class="list-group-item text-muted">Deal Type<span class="float-end">{{deals?.deal_type?.name}}</span>
                            </li>
                            <li class="list-group-item text-muted">Property Type<span class="float-end">{{deals.property_type}}</span>
                            </li>

                            <li class="list-group-item text-muted">Created at <span
                                class="float-end">{{ formatDate(deals.created_at, 'DD/MM/YYYY hh:mm A') }}</span></li>
                            <li class="list-group-item text-muted">Created By<span
                                class="float-end">{{deals.created_by.full_name}}</span>
                            </li>

                        </ul>
                            </div>
                            <div class="col-6">
                                <ul class="list-group list-group-flush">




                                    <li class="list-group-item text-muted">Deal Price<span
                                        class="float-end">AED {{ deals.deal_price }}</span></li>
                                    <li class="list-group-item text-muted">Initial Deposit<span
                                        class="float-end">AED {{ deals.initial_deposit }}</span></li>
                                    <li class="list-group-item text-muted">Gross Commission<span
                                        class="float-end">AED {{ deals.gross_commission }}</span></li>
                                    <li class="list-group-item text-muted">Net Commission<span
                                        class="float-end">AED {{ deals.net_commission }}</span></li>


                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6" v-show="!leads">
                <CardPlaceholder></CardPlaceholder>
            </div>
            <div v-if="contact" class="col-6">
                <div class="card">
                    <div class="card-header bg-soft-warning">
                        Owner Details
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item text-muted">Owner/Landlord<span
                                class="float-end">{{ contact.first_name }} {{ contact.last_name }}</span>
                            </li>
                            <li class="list-group-item text-muted">Gender<span
                                class="float-end">{{ contact?.gender || '-' }}</span></li>
                            <li class="list-group-item text-muted">Phone<span
                                class="float-end">{{ contact?.mobile }}</span>
                            </li>
                            <li class="list-group-item text-muted">Passport Number<span
                                class="float-end">{{ contact?.passport || '-' }}</span>
                            </li>


                        </ul>
                    </div>
                </div>
            </div>


        </div>
    </div>


    <keep-alive>
        <component :is="editDealModalShow ? DealModal : null" :deals="deals"></component>
    </keep-alive>
    </div>
</template>
<script setup>
import {onMounted, ref, inject, watchEffect} from "vue";
import CardPlaceholder from "../../../utilities/placeholder/CardPlaceholder.vue";
import useGlobalHelpers from "../../../composables/helpers/global";
import useDeals from "../composables/deals";

const {formatDate} = useGlobalHelpers();
const {getDealLeads, leads, lead} = useDeals();
import {useDealStore} from "../store/dealState";
import DealModal from "../includes/DealModal.vue";

const placeholder = ref(true);
const deal = useDealStore();
const deals = ref(deal.data);
const contact = deal.data?.contact;
const leadModalShow = ref(false);
const leadDocModalShow = ref(false);
const editDealModalShow = ref(false);
const currentModal = ref('');
// watchEffect(() => {
//
// })
const editDeal = () => {
    //is #dealModal is present in dom then show it
    if (document.getElementById('dealModal')) {
        editDealModalShow.value = true;
        $('#dealModal').modal('show');

    } else {
        editDealModalShow.value = true;
    }


}
setTimeout(() => {
    placeholder.value = false;
}, 3000);
onMounted(() => {
  //  getDealLeads(deals.id);
})

</script>
