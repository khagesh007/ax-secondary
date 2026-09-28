<template>
<div>
    <div class="tab-pane" id="nav-leads" role="tabpanel">
        <div v-show="placeholder" class="row">

            <div class="col-12">
                <CardPlaceholder></CardPlaceholder>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-9"><h4>Leads</h4></div>
            <div class="col-3">
                <button class="btn btn-primary action-add float-end" @click="leadSelectForm()">Add Lead</button>
            </div>
        </div>
        <div class="row" v-if="newLeadCard">
            <div class="col-12">


                <div v-if="lead" class="card">
                    <div class="card-header bg-success text-white">
                        Add new lead
                        <div class="float-end">

                                <span class="ml-2" @click="newLeadCard = false"> <i
                                    class="las la-times text-white cursor-pointer"></i> </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <label>Select Lead</label>
                                <button class="btn btn-sm btn-primary action-add float-end" @click="pushNewLead(lead)">
                                    Select Lead
                                </button>
                            </div>
                            <div class="col-12 mt-2">
                                <v-select
                                    :reduce="lead => lead"
                                    :options="searchedLeads"
                                    :create-option="lead => ({ id: lead, first_name: lead })"
                                    label="title"
                                    name="lead_id"
                                    placeholder="Type to search leads"
                                    @option:selected="onLeadChange($event)"
                                >
                                </v-select>
                            </div>
                        </div>
                        <div class="row" v-if="newSelectedLead">
                            <div class="col-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item text-muted">Lead ID <span class="float-end">{{ lead.id }}<a
                                        href="javascript:voit(0)" class="link-icon"> <i
                                        class="ri-external-link-line"></i> </a> </span>
                                    </li>
                                    <li class="list-group-item text-muted">Created Date <span
                                        class="float-end">{{ formatDate(lead.created_at, 'DD/MM/YYYY') }}</span></li>
                                    <li class="list-group-item text-muted ">Responsible agent<span
                                        class="float-end userDetails"
                                        :data-agent-id="lead?.agent?.id"><img
                                        :src="lead?.agent?.thumb_url"
                                        alt="" class="avatar-xs rounded-circle">{{ lead?.agent?.name }}</span>
                                    </li>
                                    <li class="list-group-item text-muted">For<span class="float-end">
                                        {{ lead?.for }}
                                    </span>
                                    </li>
                                    <li class="list-group-item text-muted">Source<span
                                        class="float-end"><img
                                        :src="lead?.lead_source?.thumbnail"
                                        alt="" style="width: 20px;height: 20px;">{{ lead?.lead_source?.name }}</span>
                                    </li>

                                </ul>
                            </div>
                            <div class="col-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item text-muted">Contact Name <span
                                        class="float-end">{{ lead.first_name }} {{ newSelectedLead.last_name }} </span>
                                    </li>
                                    <li class="list-group-item text-muted">Gender <span
                                        class="float-end">{{ lead.gender }}</span></li>
                                    <li class="list-group-item text-muted">Mobile <span
                                        class="float-end">{{ lead.mobile }}</span></li>

                                    <li class="list-group-item text-muted">Email <span
                                        class="float-end">{{ lead.email }}</span></li>


                                </ul>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>
        <div class="row" v-show="!placeholder">

            <div class="col-12" v-show="leads" v-for="lead in leads">
                <LeadCard @showLeadModal="showLeadModal" :lead="lead"
                          :deals="deals"></LeadCard>
            </div>


        </div>
    </div>


    <div>
        <component @removeLeadModal="removeLeadModal" :is="leadModalShow ? LeadModal : null" :lead="lead"
                   :deals="deals"></component>

<!--        <keep-alive>-->
<!--            <component :is="editDealModalShow ? DealModal : ''" :deals="deals"></component>-->
<!--        </keep-alive>-->
    </div>
</div>
</template>
<script setup>
import {onMounted, ref, inject, watchEffect} from "vue";
import CardPlaceholder from "../../../utilities/placeholder/CardPlaceholder.vue";
import useGlobalHelpers from "../../../composables/helpers/global";
import useDeals from "../composables/deals";
import LeadCard from "../includes/LeadCard.vue";
const {formatDate} = useGlobalHelpers();
const {getDealLeads, leads, lead, searchedLeads, getLeadSelect2, getLead,addNewLead} = useDeals();
import {useDealStore} from "../store/dealState";
import LeadModal from "../includes/LeadModal.vue";
import DealModal from "../includes/DealModal.vue";
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';

const deals = inject('deal');
const placeholder = ref(true);
const deal = useDealStore();
const contact = deal.data?.contact;
const leadModalShow = ref(false);
const editDealModalShow = ref(false);
const currentModal = ref('');
const newLeadCard = ref(false);
const newSelectedLead = ref(false);
// watchEffect(() => {
//
// })
const removeLeadModal = () => {
    leadModalShow.value = false;

}
const showLeadModal = (selectedLead) => {
    leadModalShow.value = true;
    lead.value = selectedLead;


}


const editDeal = () => {
    //is #dealModal is present in dom then show it
    if (document.getElementById('dealModal')) {
        editDealModalShow.value = true;
        $('#dealModal').modal('show');
    } else {
        editDealModalShow.value = true;
    }


}

const onLeadChange = (leadopt) => {

    getLead(leadopt.id);
    newSelectedLead.value = true;


}
const leadSelectForm = () => {
    newLeadCard.value = true;
    getLeadSelect2('',deals.agent_id);

}
const pushNewLead = (newLead) => {
    leads.value.push(newLead);
    newLeadCard.value = false;
    newSelectedLead.value = false;
    lead.value = newLead;
    leadModalShow.value = true;

    editDealModalShow.value = false;
    currentModal.value = 'leadModal';
    addNewLead(deals.id,newLead.id);
    setTimeout(() => {
        placeholder.value = false;
    }, 3000);
}

setTimeout(() => {
    placeholder.value = false;
}, 3000);

onMounted(() => {
    getDealLeads(deals.id);
})

</script>
