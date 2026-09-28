<template>

  <div v-if="lead" class="card">
    <div class="card-header">

      <i v-if="meta.type == 'COMPANY'" class=" ri-building-2-line" style="color: #c48667; vertical-align: bottom;"></i>

      <i v-else class="  ri-user-3-line" style="color: #c48667; vertical-align: bottom;"></i>
      {{ props.lead.pivot?.lead_type }} {{ deals.type == 'Rental' ? 'Tenant' : 'Buyer' }} <span class=""><a
          :href="viewUrl" target="_blank" class="link-icon"> <i
          class="ri-external-link-line"></i> </a> </span>
      <div class="float-end">
        <!--        <button class="btn btn-sm btn-info" @click="showLead(lead)"><i-->
        <!--            class="ri-edit-box-line text-white cursor-pointer"></i></button>-->
        <button v-if="showLeadForm == false" :id="'leadEdit'+lead.id" class="btn btn-sm btn-info"
                @click="editLead(lead)"><i
            class="ri-edit-box-line text-white cursor-pointer"></i></button>
        <button v-if="showLeadForm == true" class="btn btn-sm btn-info" @click="showLeadForm = false"><i
            class="ri-close-fill text-white cursor-pointer"></i></button>
        <button class="btn btn-sm btn-danger ml-2" @click="removeLead(deals.id,lead.id)"><i
            class="ri-delete-bin-6-line text-white cursor-pointer"></i></button>
      </div>
    </div>
    <div class="card-body" v-if="showLeadForm == false">
      <div class="row">
        <div class="col-6">
          <ul class="list-group list-group-flush">
            <li class="list-group-item text-muted">Lead ID <span class="float-end">{{ lead.id }}<a
                :href="viewUrl" target="_blank" class="link-icon"> <i
                class="ri-external-link-line"></i> </a> </span>
            </li>
            <li v-if="meta.type == 'COMPANY'" class="list-group-item text-muted">Country of incorporation/registration
              <span
                  class="float-end">{{ meta.company_coi }}</span></li>
            <li v-else class="list-group-item text-muted">UAE National <span
                class="float-end">{{ meta.uae_national == 1 ? 'Yes' : 'No' }}</span></li>
            <li class="list-group-item text-muted">{{ deals.type == 'Rental' ? 'Tenant' : 'Buyer' }} Type<span
                class="float-end">{{ meta.type }}</span></li>
            <li class="list-group-item text-muted">Type<span
                class="float-end fs-12">{{ prepareFor(lead?.for) || '' }}</span>
            </li>
            <li class="list-group-item text-muted ">Responsible Agent<span class="float-end userDetails"
                                                                           :data-agent-id="lead?.agent?.id"><img
                :src="lead?.agent?.thumb_url"
                alt="" class="avatar-xs rounded agent-img"> {{ lead?.agent?.name }}</span>
            </li>

          </ul>
        </div>
        <div class="col-6">
          <ul class="list-group list-group-flush">
            <li class="list-group-item text-muted">Name <span
                class="float-end">{{ lead.first_name }} {{ lead.last_name }} </span>
            </li>

            <li v-if="meta.type == 'INDIVIDUAL'" class="list-group-item text-muted">Gender <span
                class="float-end">{{ lead.gender }}</span></li>
            <li v-else class="list-group-item text-muted">Company Former Name <span
                class="float-end">{{ meta.company_former_name }}</span></li>


            <li class="list-group-item text-muted">Mobile <span
                class="float-end">{{ lead.mobile }}</span></li>

            <li class="list-group-item text-muted">Email <span
                class="float-end">{{ lead.email }}</span></li>
            <li class="list-group-item text-muted d-none">Missing Docs <span
                class="float-end text-danger fs-11">
                               {{ missingDocuments }}

                            </span></li>
            <li v-if="meta.type !== 'COMPANY'" class="list-group-item text-muted">Nationality <span
                class="float-end">{{ getNationalityName(lead.nationality_id) }}</span></li>


          </ul>

        </div>
      </div>
      <div class="row collapse" :id="'moreDetails'+lead.id">
        <div class="col-6">
          <ul class="list-group list-group-flush">
            <li v-if="meta.type == 'COMPANY'" class="list-group-item text-muted"> Trade license issue date <span
                class="float-end">
                {{ meta.trade_license_issue_date }}
                </span>
            </li>
            <li v-if="meta.type == 'COMPANY'" class="list-group-item text-muted"> Trade license expiry date <span
                class="float-end">
                {{ meta.trade_license_expiry_date }}
                </span>
            </li>

            <li class="list-group-item text-muted">Residential Address <span
                class="float-end">
                {{ lead.address }}
                </span>
            </li>


            <li class="list-group-item text-muted">Date Of Birth <span
                class="float-end">{{ lead.dob }}</span></li>

            <li class="list-group-item text-muted">Place Of Birth <span
                class="float-end">
                {{ lead.pob }}
                </span>
            </li>

            <li class="list-group-item text-muted">Lead Created Date <span
                class="float-end">{{ formatDate(lead.created_at, 'DD/MM/YYYY') }}</span></li>


            <li class="list-group-item text-muted">Source<span
                class="float-end"><img
                v-if="lead?.lead_source?.thumbnail"
                :src="lead?.lead_source?.thumbnail"
                alt="" style="width: 20px;height: 20px;">{{ lead?.lead_source?.name }}</span></li>
            <li v-if="lead.medium_id != null" class="list-group-item text-muted">Medium<span
                class="float-end">
                               {{ lead?.medium?.name }}
                            </span></li>
            <li v-if="meta.type !== 'COMPANY'" class="list-group-item text-muted">Occupation<span
                class="float-end">
                               {{ meta.occupation }}
                            </span></li>
            <li v-if="meta.type !== 'COMPANY'" class="list-group-item text-muted">Years In Present Role<span
                class="float-end">
                               {{ meta.year_of_experience }}
                            </span></li>
            <li v-if="meta.type !== 'COMPANY'" class="list-group-item text-muted">Employer / Establishment Name<span
                class="float-end">
                               {{ meta.employer_name }}
                            </span></li>
            <li v-if="meta.type !== 'COMPANY'" class="list-group-item text-muted">Employer / Establishment Address<span
                class="float-end">
                               {{ meta.employer_address }}
                            </span></li>

          </ul>
        </div>
        <div class="col-6">
          <ul class="list-group list-group-flush">
            <li v-if="meta.type == 'COMPANY'" class="list-group-item text-muted">Trading Name (If any) <span
                class="float-end">{{ meta.company_trading_name }}</span>
            </li>
            <li v-else class="list-group-item text-muted">Alias Name <span
                class="float-end">{{ meta.aliases }}</span>
            </li>

            <li v-if="meta.type == 'COMPANY'" class="list-group-item text-muted"> Date of incorporation/registration
              <span
                  class="float-end">{{ meta.company_coi_date }}</span></li>
            <li class="list-group-item text-muted">Telephone Number <span
                class="float-end">{{ lead.phone }}</span></li>
            <li class="list-group-item text-muted">Passport Number <span
                class="float-end">{{ meta.passport_number }}</span></li>


            <li class="list-group-item text-muted">Name On Passport <span
                class="float-end">
                {{ meta.name_on_passport }}
                </span>
            </li>
            <li class="list-group-item text-muted">Passport Issue Date <span
                class="float-end">
                {{ meta.passport_issue_date }}
                </span>
            </li>

            <li class="list-group-item text-muted">Passport Expiry Date <span
                class="float-end">{{ meta.passport_expiry }}</span>
            </li>
            <li v-if="meta.type !== 'COMPANY'" class="list-group-item text-muted">Fund Source<span
                class="float-end">
                             {{ meta.fund_source }}
                            </span></li>

            <li v-if="meta.type !== 'COMPANY'" class="list-group-item text-muted">Annual Income<span
                class="float-end">
                               {{ meta.annual_income }}
                            </span></li>
            <li v-if="meta.type !== 'COMPANY'" class="list-group-item text-muted">Payment Method<span
                class="float-end">
                               {{ meta.payment_method }}
                            </span></li>
            <li v-if="meta.type !== 'COMPANY'" class="list-group-item text-muted">Politically Exposed Person (PEP)?<span
                class="float-end">
                               {{ meta.political }}
                            </span></li>


          </ul>

        </div>
      </div>
      <div class="row">
        <div class="col-12">
               <span @click="toggleAccordion('moreDetails'+lead.id)" style="float: right;
    color: green;font-size: 20px;" class="cursor-pointer">
                 <i class=" ri-aspect-ratio-line"></i>
               </span>
        </div>
      </div>


    </div>
    <LeadForm @refreshLeadList="refreshLeads" :mode="'view'" v-if="showLeadForm == true" :deals="deals" :leadProp="lead"
              :countries="countries"
              @closeLeadForm="showLeadForm = false" :lead_type="props.lead.pivot?.lead_type"></LeadForm>
  </div>
  <div v-else>
    <LeadForm @refreshLeadList="refreshLeads" :mode="'new'" v-if="showLeadForm == true" :deals="deals" :leadProp="{}"
              :countries="countries"
              @closeLeadForm="showLeadForm = false" :lead_type="props.lead.pivot?.lead_type"></LeadForm>
  </div>


</template>
<script setup>
import useGlobalHelpers from "../../../composables/helpers/global";
import {inject, onMounted, ref, watch, watchEffect, provide} from "vue";
import LeadModal from "./LeadModal.vue";

const {formatDate} = useGlobalHelpers();
import {useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
import LeadForm from "./LeadForm.vue";

const emit = defineEmits(['showLeadModal', 'leadDeleted', 'refreshLeadList']);
const {lead: leadData, getLead, removeLeadFromDeal} = useDeals();
const viewUrl = ref('');
const countries = inject('countries');
provide('countries', countries)
const deal = useDealStore();
const showLeadForm = ref(false);
const props = defineProps({
  deals: {
    type: Object,
    required: false
  },
  lead: {
    type: Object,
    required: true
  },
  loop_index: {
    type: Number,
    required: true
  }
})
const leadModalShow = ref(false);
const lead_id = ref(null);
const meta = ref({});

const showLead = (lead) => {
  emit('showLeadModal', lead);
}
const editLead = (lead) => {
  showLeadForm.value = true;
}
const individualDocuments = ref({
  passport: 'Passport copy',
  visa: 'Visa copy',
  eid: 'Emirates ID copy',
  signed_tenancy_contract: 'Signed Tenancy Contract',
});
const companyDocuments = ref({
  trade_license: 'Trade License',
  poa: 'Company POA',
  moa: 'Memorandum of Association',
  coi: 'Certificate of Incumbency',
  vat: 'VAT Certificate',
  authorized_signatory_doc: 'Authorized Signatory Document',
  signed_tenancy_contract: 'Signed Tenancy Contract',
});

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
    return str == null ? str : '';
  } else {

    return str == null ? str : '';
  }

}

const removeLead = (deal_id, lead_id) => {
  Swal.fire({
    html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to remove this lead from this deal ?</p></div></div>',


    showCancelButton: true,
    cancelButtonText: 'Cancel',
    cancelButtonClass: 'btn btn-warning action-close w-xs',

    showCloseButton: true,
    confirmButtonText: 'Delete',
    confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

    buttonsStyling: false,

  }).then(function (result) {
    if (result.isConfirmed) {
      removeLeadFromDeal(deal_id, lead_id);
      emit('leadDeleted', lead_id);
    }
  })


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
const createMissingDocList = () => {
  if (props.lead?.documents) {
    if (props.lead?.lead_meta != null) {
      let type = props.lead.lead_meta.find((meta) => meta.meta_key == 'type');
      if (type != null && type.meta_value == "INDIVIDUAL") {
        //this is to check if the lead has all the required documents or not
        for (let [key, value] of Object.entries(individualDocuments.value)) {
          let check = props.lead.documents.find((doc) => doc.title == key);
          if (!check) {
            missingDocuments.value += value + ',';
          }
        }

      } else if (type != null && type.meta_value == "COMPANY") {
        for (let [key, value] of Object.entries(companyDocuments.value)) {
          let check = props.lead.documents.find((doc) => doc.title == key);
          if (!check) {
            missingDocuments.value += value + ',';
          }
        }
      }

    }
  }
}
const missingDocuments = ref('');
const toggleAccordion = (element) => {
  if ($("#" + element).hasClass('show')) {
    $("#" + element).collapse('toggle')
  } else {
    $("#" + element).collapse('toggle')
  }
}
onMounted(() => {
  viewUrl.value = route('admin.work.leads.index') + '?id=' + props.lead.id;
  createMissingDocList();
  if (props.lead.lead_meta != null) {
    for (let [key, value] of Object.entries(props.lead.lead_meta)) {
      meta.value[value.meta_key] = value.meta_value;

    }
    getLead(props.lead.id);
  }
  if (props.lead == {} || props.lead == null || props.lead == undefined) {
    showLeadForm.value = true;
    console.log('lead is null')
  }

})

const refreshLeads = () => {
  emit("refreshLeadList")
}

watch(() => showLeadForm.value, (val) => {
  if (props.lead.lead_meta != null) {
    for (let [key, value] of Object.entries(props.lead.lead_meta)) {
      meta.value[value.meta_key] = value.meta_value;
    }
  }
})

</script>
<style scoped>
.link-icon {
  margin-left: 5px;
}
.agent-img {
    opacity: 0.3;
}
.agent-img:hover {
    opacity: 1.0;
}
</style>
