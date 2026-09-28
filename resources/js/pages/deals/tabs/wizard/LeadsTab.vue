<template>
  <section id="wizard-p-0" role="tabpanel" aria-labelledby="wizard-h-0" class="body current"
           aria-hidden="false" style="">
      <div v-show="placeholder" class="row">

          <div class="col-12">
              <CardPlaceholder></CardPlaceholder>
          </div>

          <div class="col-12 mt-2">
              <CardPlaceholder></CardPlaceholder>
          </div>
      </div>

      <div class="row" v-show="!placeholder">

          <div class="col-12" v-if="leads" v-for="(lead, index) in leads" :key="index">
              <LeadCard @refreshLeadList="refreshLeads" @showLeadModal="showLeadModal" @leadDeleted="leadRemoved" :lead="lead"
                        :deals="deals" :loop_index="index"></LeadCard>
          </div>
      </div>
    <div class="row mb-2">
      <div class="col-9"><h4>{{ deal_state.data.type == 'Rental' ? 'Tenants' : 'Buyers' }}</h4></div>
      <div class="col-3">
        <button v-if="primaryLeadBtn" class="btn btn-primary action-add float-end" @click="leadSelectForm('Primary');">Attach Primary
          {{ deal_state.data.type == 'Rental' ? 'Tenant' : 'Buyer' }}
        </button>

          <button v-if="secondaryLeadBtn" class="btn btn-soft-success waves-effect waves-light float-end" @click="leadSelectForm('Secondary');">Attach Secondary
              {{ deal_state.data.type == 'Rental' ? 'Tenant' : 'Buyer' }}
          </button>
      </div>
    </div>
    <div class="" v-if="newLeadCard">
      <div class="">
        <div v-if="lead" class="">
          <div class="card-body">
            <div class="row mb-2">
              <div class="col-11 mt-2">
                <v-select
                    v-if="!addNew"
                    v-model="selectedLeadTemp"
                    :options="searchedLeads"
                    label="text"
                    :filterable="true"
                    :clearable="true"
                    :placeholder="'Type to search leads'"
                    @option:selected="onLeadChange($event)"
                    @search="searchLeads($event)"
                    :reduce="option => option.id"
                    :get-option-label="option => option.text"
                    :get-option-value="option => option.id"
                >
                  <template #option="option">
                    <span> #{{ option.id }} {{
                        option.title
                      }} {{ option?.lead_source != null ? '(' + option?.lead_source?.name + ')' : '' }}</span>

                  </template>
                </v-select>
              </div>
              <div class="col-1">


                <button class="btn btn-sm btn-light action-close fw-medium mt-2 ml-1 d-none"
                        @click="newLeadCard = false">
                  Cancel
                </button>
                <div class="btn-group float-end">
                  <button type="button" class="btn btn-sm btn-light dropdown-toggle mt-2" data-bs-toggle="dropdown"
                          aria-haspopup="true" aria-expanded="false">More
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="javascript:void(0)" v-if="!addNew" @click="addNew = true; newSelectedLead = false;">Add New</a>
                    <a class="dropdown-item" href="javascript:void(0)" v-if="addNew" @click="addNew = false; newSelectedLead = false;">Select Existing</a>
                    <a class="dropdown-item" href="javascript:void(0)" @click="inviteModalShow = true">Invite Other
                      Agent</a>

                  </div>
                </div><!-- /btn-group -->

              </div>
            </div>
            <div class="row">

              <div class="col-12">
                <NewLead v-if="addNew"
                          :deals="deals" @leadAdded="newLeadAdded"></NewLead>
              </div>
            </div>

            <div class="row" v-if="newSelectedLead">
              <div class="col-12">
                <LeadCardNew @leadSelected="leadSelected" :lead="lead" loop_index="null"
                             :deals="deals" :newLeadType="newLeadType"></LeadCardNew>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>

    <div class="actions clearfix mt-5">
      <div class="row">
        <div class="col-8"></div>
        <div class="col-4">
          <button type="button" @click="nextStepBtn" :disabled="leads.length == 0"
                  class="btn btn-success action-save float-end">
                         <span v-if="showBtnLoader" class="d-flex align-items-center">
                                                            <span class="spinner-border flex-shrink-0" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </span>
                                                            <span class="flex-grow-1 ms-2">
                                                                Loading...
                                                            </span>
                                                        </span>
            <span v-else>Next</span>
          </button>
        </div>
      </div>

    </div>
    <component @removeLeadModal="removeLeadModal" @refreshLeadList="refreshLeads" :is="leadModalShow ? LeadModal : null"
               :leadProp="lead"
               :deals="deals"></component>
    <component @removeInviteModal="removeInviteModal" :is="inviteModalShow ? InviteAgent : null" :dealProp="deals"
               :deals="deals"></component>
  </section>
</template>
<script setup>
import {inject, onMounted, provide, ref, watch} from "vue";
import UserSelect from "../../../../components/formElements/UserSelect.vue";
import PropertySelect from "../../../../components/formElements/PropertySelect.vue";
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';
import useDeals from "../../composables/deals";
import {useDealStore} from "../../store/dealState";
import useGlobalHelpers from "../../../../composables/helpers/global";
import CardPlaceholder from "../../../../utilities/placeholder/CardPlaceholder.vue";
import LeadCard from "../../includes/LeadCard.vue";
import LeadModal from "../../includes/LeadModal.vue";
import InviteAgent from "../../includes/InviteAgent.vue";
import LeadForm from "../../includes/LeadForm.vue";
import LeadCardNew from "../../includes/LeadCardNew.vue";
import NewLead from "../../includes/NewLead.vue";
const countries = inject('countries');
provide('countries', countries)
const emit = defineEmits(['next_step']);
const {getDealLeads, leads, lead, searchedLeads, getLeadSelect2, getLead, addNewLead, updateDealState} = useDeals();
const {formatDate} = useGlobalHelpers();
const deal_state = useDealStore();
const leadModalShow = ref(false);
const inviteModalShow = ref(false);
const editDealModalShow = ref(false);
const currentModal = ref('');
const newLeadCard = ref(false);
const newSelectedLead = ref(false);
const placeholder = ref(true);
const showBtnLoader = ref(false);
const deals = deal_state.data;
const selectedLeadTemp = ref(null);
const addNew = ref(false);
const    primaryLeadBtn = ref(true);
const secondaryLeadBtn = ref(false);
const newLeadType = ref('');

const onLeadChange = (leadopt) => {
  getLead(leadopt.id);
  newSelectedLead.value = true;
}
const removeInviteModal = () => {
  inviteModalShow.value = false;

}

const leadSelectForm = (type) => {
    console.log(type)
  newLeadCard.value = !newLeadCard.value;
  newLeadType.value  = type;
  // lead.value = null;
  getLeadSelect2('', deal_state.data.agent_id);

}
const searchLeads = (search) => {
 // getLeadSelect2(search, deal_state.data.agent_id);
}
const removeLeadModal = () => {
  leadModalShow.value = false;

}
const leadRemoved = (lead) => {
  getDealLeads(deal_state.data.id);
  //this will be called if a lead is removed
  //leads.value = leads.value.filter(item => item.id !== lead.id);
}
const newLeadAdded =() => {
    getDealLeads(deal_state.data.id);
}
const showLeadModal = (selectedLead) => {
  leadModalShow.value = true;
  lead.value = selectedLead;


}
const leadSelected = (selected,new_lead_type) => {
  pushNewLead(selected,new_lead_type);
}
const pushNewLead = (newLead,new_lead_type) => {

  //first check if leads.value object has this lead already if yes then return
  if (leads.value.find(item => item.id === newLead.id)) {
    showToastify('Lead already added.', 'error')
    return;
  }
  leads.value.push(newLead);
  newLeadCard.value = false;
  newSelectedLead.value = false;
  lead.value = newLead;
  // // leadModalShow.value = true;
  //
  // editDealModalShow.value = false;
  // currentModal.value = 'leadModal';
  addNewLead(deal_state.data.id, newLead.id,new_lead_type);
    refreshLeads();
  setTimeout(() => {
      //click on button with id leadEdit{{newlead.id}}
      document.getElementById('leadEdit' + newLead.id).click();
      document.getElementById('leadEdit' + newLead.id).focus();

    placeholder.value = false;
  }, 1000);
}
const userSelected = (user) => {

  // formData.value.agent_id = user.id;
}
const documents = ref({
  passport: 'Passport copy',
  visa: 'Visa copy',
  deed: 'Property Deed Documents',
  rera: 'Rera Documents'
});
const editDeal = () => {
  //is #dealModal is present in dom then show it
  if (document.getElementById('dealModal')) {
    editDealModalShow.value = true;
    $('#dealModal').modal('show');
  } else {
    editDealModalShow.value = true;
  }


}
watch(()=> leads.value, (val) => {
    for (let i = 0; i < val.length; i++) {
        if (val[i].pivot.lead_type == 'Primary') {
            primaryLeadBtn.value = false;
            secondaryLeadBtn.value = true;
            break;
        } else {
            primaryLeadBtn.value = true;
            secondaryLeadBtn.value = false;
        }
    }
  // if (val.length > 0) {
  //  primaryLeadBtn.value = false;
  //  secondaryLeadBtn.value = true;
  // } else {
  //   primaryLeadBtn.value = true;
  //   secondaryLeadBtn.value = false;
  // }
})
const nextStepBtn = () => {
  showBtnLoader.value = true;
  updateDealState(deal_state.data.id, 'attachments')
  setTimeout(() => {
    showBtnLoader.value = false;
    emit('next_step', 'attachments');
  }, 2000);


}
const refreshLeads = async () => {
  await getDealLeads(deal_state.data.id);
}
onMounted(() => {
  getLeadSelect2();
  if (deal_state.data) {

    getDealLeads(deal_state.data.id);
    // leads.value = deal_state.data.leads;
    //  placeholder.value = false;
    setTimeout(() => {
      placeholder.value = false;
    }, 800);
  }


})
</script>
