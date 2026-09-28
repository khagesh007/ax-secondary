<template>
  <div>
    <section id="wizard-p-0" role="tabpanel" aria-labelledby="wizard-h-0" class="body current"
             aria-hidden="false" style="">
      <form @submit.prevent="onSubmit" enctype="multipart/form-data">
        <div class="">
          <div class="row" v-if="!Object.keys(deal).length > 0">
            <div class="form-group col-md-6">
              <label>Select Agent</label>
              <UserSelect @userSelected="userSelected" :roles="['Agent']" :passedUsers="[]" :multiple="false"
              />

            </div>
            <div class="form-group col-md-6">
              <label>Property</label>
              <PropertyByUser @propertySelected="propertySelected" :user="formData.agent_id || null"
                              :refresh="refreshProperty" :multiple="false"></PropertyByUser>
            </div>
          </div>
          <div class="row" v-if="property.ref_no !=''">


            <div class="col-lg-12" v-if="property.ref_no">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fs-14 text-primary mb-0">Property Details</h5>
              </div>
              <div class="row">
                <div class="col-6" v-if="property.ref_no">
                  <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Reference #</h6>
                      </div>
                      <span class="text-muted" id="property_reference_no">{{ property.ref_no }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Unit #</h6>
                      </div>
                      <span class="text-muted" id="property_unit_no">{{ property.unit_no }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Category</h6>
                      </div>
                      <span class="text-muted" id="property_category">{{ property.category }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Location</h6>
                      </div>
                      <span class="text-muted" id="property_location">{{ property.location }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Type</h6>
                      </div>
                      <span class="text-muted" id="property_for">{{ property.for }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Price</h6>
                      </div>
                      <span class="text-muted" id="property_price">AED&nbsp;{{ property.price }}</span>
                    </li>
                  </ul>
                </div>
                <div class="col-6" v-if="property.ref_no">
                  <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Bed</h6>
                      </div>
                      <span class="text-muted" id="property_reference_no">{{ property?.bed }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Bath</h6>
                      </div>
                      <span class="text-muted" id="property_unit_no">{{ property?.bath }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Built Up Area</h6>
                      </div>
                      <span class="text-muted" id="property_category">{{ property.bua }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Completion Status</h6>
                      </div>
                      <span class="text-muted" id="property_price">{{ property.completion_status }}</span>
                    </li>
                    <li v-if="property.completion_date" class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Completion Date</h6>
                      </div>
                      <span class="text-muted" id="property_location">{{ property.completion_date }}</span>
                    </li>
                    <li v-if="deal?.agent" class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Agent</h6>
                      </div>
                      <span class="text-muted cursor-pointer" id="deal_agent">
                            <span
                                class="float-end userDetails"
                                :data-agent-id="deal?.agent?.id">
                                        <img v-if="deal?.agent?.thumb_url"
                                             :src="deal?.agent?.thumb_url"
                                             alt="" class="avatar-xs rounded-circle d-none">{{
                                deal?.agent?.name
                              }}</span>
                          </span>
                    </li>
                    <li  class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">Deal Type</h6>
                      </div>
                      <span  class="text-muted" >{{formData.deal_type}}</span>

                    </li>
                  </ul>
                </div>
              </div>


            </div>

          </div>



  </div>
  <div class="actions clearfix mt-5">
    <div class="row">
      <div class="col-8"></div>
      <div class="col-4">
        <button v-if="deal.id" type="submit" class="btn btn-success action-save float-end">
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
        <button v-else type="submit" class="btn btn-success action-save float-end">
                    <span v-if="showBtnLoader" class="d-flex align-items-center">
                                                            <span class="spinner-border flex-shrink-0" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </span>
                                                            <span class="flex-grow-1 ms-2">
                                                                Loading...
                                                            </span>
                                                        </span>
          <span v-else>Submit</span>

        </button>
      </div>
    </div>

  </div>
  </form>
  </section>
  </div>
</template>
<script setup>
import {onBeforeMount, onMounted, ref, watch} from "vue";
import UserSelect from "../../../../components/formElements/UserSelect.vue";
import {useDealStore} from "../../store/dealState";
const deal_state = useDealStore();
const emit = defineEmits(['next_step']);
import useDeals from "../../composables/deals";
import PropertyByUser from "../../../../components/formElements/PropertyByUser.vue";
import VueMultiselect from "vue-multiselect";
import 'vue-multiselect/dist/vue-multiselect.css';

const {createDeal, deal, updateDeal, getDealCommissions, commissions} = useDeals();
const refreshProperty = ref(false);
const showBtnLoader = ref(false);
const userSelected = (user) => {

  formData.value.agent_id = user.id;
  refreshProperty.value = true;
  property.value = {};
  setTimeout(() => {
    refreshProperty.value = false;
  }, 1000);
}
const property = ref({
  ref_no: '',
  unit_no: '',
  category: '',
  location: '',
  price: '',
  id: '',
  bed: '',
  bath: '',
  completion_date: '',
  completion_status: '',
  type: '',
  bua: '',
  for: '',
});
const commissionsList = ref([
  {id:'',commission_amount: '', percentage: '0', commission_sources: '', vat: true, vat_amount: ''},

]);

const dealTypes = ref([
  {name: 'Rental', id: 'Rental'},
  {name: 'Secondary Sale', id: 'Secondary Sale'},
  {name: 'Primary Sale', id: 'Primary Sale'},
]);
const sourceList = [
  {name: 'Seller Side', id: 'Buyer Side'},
  {name: 'Buyer Side', id: 'Buyer Side'},
];
const formData = ref({
  deal_id: null,
  agent_id: '',
  owner_id: '',
  property_id: '',
  deal_price: '',
  gross_commission: '',
  net_commission: deal.value.net_commission,
  total_vat: '',
  deal_type: deal.type,
});
const propertySelected = (val) => {

  if (val == null) {
    //set all value to null if no property selected
    property.value = {};
  } else {
    property.value.id = val.id;
    property.value.ref_no = val.ref_no;
    property.value.unit_no = val.unit_no;
    property.value.category = val.category;
    property.value.location = val.location;
    property.value.price = numberFormat(val.price.price);
    property.value.for = val.for;
    property.value.bed = val.bed?.name;
    property.value.bath = val.bath?.name;
    property.value.completion_date = '';
    property.value.completion_status = val.completion_status;
    property.value.bua = val.built_up_area;
    formData.value.property_id = val.id;
    formData.value.owner_id = val.owner_id;
    formData.value.deal_price = numberFormat(val.price.price);
  }
  dealTypeChecker();
  // formData.value.commission = val.price.price;
  // formData.value.gross_commission = val.price.price;
  // formData.value.net_commission = val.price.price;


}

function restrictInput(event) {
  const allowedCharacters = /[0-9,]/g;
  const input = event.target;
  const value = input.value;
  const newValue = value.match(allowedCharacters) || [];
  input.value = newValue.join('');
}

document.addEventListener('DOMContentLoaded', function () {
  const numberOnlyInputs = document.querySelectorAll('.numberOnly');
  numberOnlyInputs.forEach(function (input) {
    input.addEventListener('input', restrictInput);
  });
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

watch(() => deal.value, (val) => {

  if (val != null && val != undefined && val.property != null && val.property != undefined) {
    setTimeout(() => {
      deal_state.data = val;
      deal_state.deal_type = val.type;
      //  emit('next_step', 'owner');
    }, 2000);
  }
});
watch(() => commissions.value, (val) => {

  if (val != null && val != undefined && val.length > 0) {
    for (let i = 0; i < val.length; i++) {
      let source = {
        name: val[i].commission_source,
        id: val[i].commission_source,
      }
      commissionsList.value.push({
        id: val[i].id,
        commission_amount: val[i].commission_amount,
        percentage: val[i].commission_percentage,
        commission_sources: source,
        vat: val[i].is_vat_applicable == 1 ? true : false,
      });
    }
  }
});
const dealTypeChecker = (dealType) => {
    let type = ''
    if((property.value.for == 'Residential for rent' || property.value.for == 'Commercial for rent') && (property.value.completion_status == 'Ready Primary' || property.value.completion_status == 'Ready Secondary')) {
        type = 'Rental';
    }else if((property.value.for == 'Residential for sale' || property.value.for == 'Commercial for sale') && (property.value.completion_status == 'Off-Plan Secondary' || property.value.completion_status == 'Off-Plan Primary')) {
        type = 'Primary Sale';
    }else if ((property.value.for == 'Residential for sale' || property.value.for == 'Commercial for sale') && (property.value.completion_status == 'Ready Primary' || property.value.completion_status == 'Ready Secondary')) {
        type = 'Secondary Sale';
    }else {
        type = '';
    }
    formData.value.deal_type = type;
    return type;
}
const onSubmit = () => {
  showBtnLoader.value = true;
  if (deal.value != null && deal.value != undefined && deal.value.property != null && deal.value.property != undefined) {
    //updateDeal(formData.value);
    setTimeout(() => {
      deal_state.data = deal;
      showBtnLoader.value = false;
      emit('next_step', 'owner');
    }, 1000);
  } else {
      let type = dealTypeChecker();


    let currentUrl = location.href;
      Swal.fire({
          html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to create the deal with deal type '+type+' ?</p></div></div>',

        icon: "warning",
          showCancelButton: true,
          cancelButtonText: 'Cancel',
          cancelButtonClass: 'btn btn-warning action-close w-xs',

          showCloseButton: true,
          confirmButtonText: 'Confirm',
          confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

          buttonsStyling: false,

      }).then(function (result) {
          if (result.isConfirmed) {
              createDeal(formData.value);
          }
      });
    if (deal) {
      setTimeout(() => {
        deal_state.data = deal;
        deal_state.deal_type = deal.type;
        showBtnLoader.value = false;
        //redirect to currentUrl with ?id=deal.id
        currentUrl = currentUrl + '?id=' + deal.value.id;
       // window.location.replace(currentUrl);
        // emit('next_step', 'owner');
      }, 2000);

    }
  }
}
onBeforeMount(() => {
  if (deal_state.data) {
    deal.value = deal_state.data;
  } else if (deal) {
    deal_state.data = deal;
  } else {
    deal.value = null;
  }
});
onMounted(() => {

// let redirectTo = new URL(location.href).searchParams.get('step');
//   emit('next_step', redirectTo);
  if (deal.value != null && deal.value != undefined && deal.value.property != null && deal.value.property != undefined) {
   getDealCommissions(deal.value.id);
    commissionsList.value = [];
    propertySelected(deal.value.property);
    formData.value.deal_id = deal.value.id;
    formData.value.gross_commission = numberFormat(deal.value.gross_commission);
    formData.value.net_commission = numberFormat(deal.value.net_commission);

  }
});
</script>
<style scoped>
.odd {
  background: #f4f4f4;
}

.even {
  background: #fff;
}
.card-border-primary .card-footer, .card-border-primary .card-header {
  border-color: #c48667;
}
.card-border-primary {
  border-color: #c48667!important;
}
</style>
