<template>

  <div class="card" v-if="showLeadForm">
    <div class="card-header text-black" style="    background-color: #e9eaf1!important;">
     Add New {{deals.type =='Rental' ? 'Tenant' : 'Buyer'}}
      <div class="float-end">

        <button  v-if="showLeadForm == true" @click="showLeadForm = false" class="btn btn-sm btn-light">Cancel</button>

      </div>
    </div>

    <LeadForm v-if="showLeadForm"  :mode="'new'" :deals="deals" :leadProp="{}" :countries="countries"
              @closeLeadForm="closeLeadForm()"></LeadForm>
  </div>



</template>
<script setup>
import useGlobalHelpers from "../../../composables/helpers/global";
import {inject, onMounted, ref, watch, watchEffect} from "vue";
import LeadModal from "./LeadModal.vue";

const {formatDate} = useGlobalHelpers();
import {useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
import LeadForm from "./LeadForm.vue";

const emit = defineEmits(['showLeadModal', 'leadDeleted','leadAdded']);
const {lead: leadData, getLead, removeLeadFromDeal} = useDeals();
const viewUrl = ref('');
const countries = inject('countries');
const deal = useDealStore();
const showLeadForm = ref(true);
const props = defineProps({
  deals: {
    type: Object,
    required: false
  },
    newLeadType: {
        type: String,
        required: false
    }
})
const leadModalShow = ref(false);
const lead_id = ref(null);
const meta = ref({});

const showLead = (lead) => {
  emit('showLeadModal', lead);
}

const closeLeadForm = () => {
  showLeadForm.value = false;
  emit('leadAdded');
}
const getNationalityName = (id) => {
  if (id == null) {
    return '';
  }
  if (countries != null) {
    let countryData = countries.value.find((country) => country.id == id);
    if (countryData) {
      return countryData.nationality;
    } else {
      return '';
    }
  } else {
    return '';
  }
}
const prepareFor = (str) => {
  str = JSON.stringify(str);
  if (typeof str == 'string') {
    let res = str.replace(/[\[\]"']+/g, '');
    return res;
  } else {

    return str;
  }

}


watchEffect(() => {
  //this code was used to push lead agent to deal agents list which is under state management
  // if (props.lead) {
  //     if (props.lead?.agent) {
  //         deal.agents.push(props.lead.agent);
  //     }
  //
  // }
})
watch(() => leadData.value, (val) => {
  if (val != null && val != undefined) {

  }
});

const toggleAccordion = (element) => {
  if ($("#" + element).hasClass('show')) {
    $("#" + element).collapse('toggle')
  } else {
    $("#" + element).collapse('toggle')
  }
}
onMounted(() => {


})

</script>
<style scoped>
.link-icon {
  margin-left: 5px;
}
</style>
