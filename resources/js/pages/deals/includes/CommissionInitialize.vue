<template>

  <div v-if="property">
    <form @submit.prevent="onSubmit" enctype="multipart/form-data">
    <div v-if="property.ref_no" class="card border card-border-primary mt-1">
      <div class="card-header">

              <span class="card-title mb-0">
                <div class="row">
                   <div class="col-2">
                    <label>{{ deal.type == 'Rental' ? 'Renting' : 'Selling'}} Price</label>
                    </div>
                  <div class="col-4">
                    <div class="input-group">

                      {{ formatNumber(property.price.price) }}
                    </div>
                  </div>
                    <div class="col-2">
                    <label>Deal Price</label>
                    </div>
                  <div class="col-4">
                    <div class="input-group">
                      <span class="input-group-text" id="inputGroup-sizing-default">AED</span>
                      <input type="text" class="form-control" v-model="formData.deal_price"
                             name="deal_price" @keyup="doAllCalculations()">
                    </div>
                  </div>
                </div>
              </span>
      </div>

      <div class="card-body">


        <div :class=" isEven(index) ? 'even row mt-3' : 'odd row mt-3' " v-for="(item, index) in commissionsList"
             :key="index">
          <div class="offset-1 col-4">
            <label>Amount</label>
            <div class="input-group">
              <span class="input-group-text">AED</span>
              <input type="text" class="form-control commissionAmount" v-model="item.commission_amount"
                     :id="'commission_amount'+index" @keyup="calculateCommissionNew(item)"
                     name="deal_price">
            </div>
          </div>
          <div class="col-2">
            <label>Percentage</label>
            <div class="input-group">
              <span class="input-group-text">%</span>
              <input type="text" class="form-control commissionPercentage"
                     :id="'percentage'+index"
                     @keyup="calculateCommissionNew2(item)"
                     v-model="item.percentage"
              >
            </div>
          </div>
          <div class="col-2">
            <label>Source</label>
            <VueMultiselect
                v-model="item.commission_sources"
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
          <div class="col-2">
            <label class="form-check-label" for="formCheck6">
              VAT
            </label>
            <div class="form-check mt-3">
              <input class="form-check-input" v-model="item.vat" @change="calculateOverAllCommission()"
                     type="checkbox" id="formCheck6" checked>
              <button type="button" class="btn btn-sm btn-outline-danger float-end" @click="removeCommission()">
                <i class="mdi mdi-minus"></i>

              </button>
            </div>
          </div>
        </div>
        <div class="row mt-2">

          <div class="offset-1  col-2">

            <button type="button" class="btn btn-soft-success btn-sm" @click="addCommission()">
              <i class="ri-add-line align-bottom"></i>
            </button>

          </div>
          <div class="col-10"></div>
        </div>
        <hr>
        <div class="row mt-2">
          <div class="offset-8 col-2">
            Net commission
          </div>
          <div class="col-2">
            <div class="input-group">
              AED {{ formData.net_commission }}
            </div>
          </div>
        </div>
        <div class="row mt-2">
          <div class="offset-8 col-2">
            Total VAT
          </div>
          <div class="col-2">
            <div class="input-group">
              AED {{ formatNumber(formData.total_vat) }}
            </div>
          </div>
        </div>
        <hr>
        <div class="row mt-2">
          <div class="offset-8 col-2">
            Gross Commission
          </div>
          <div class="col-2">
            <div class="input-group">
              AED {{ formData.gross_commission }}
              <br>

            </div>
            <span v-if="formData.gross_commission !='' && formData.gross_commission != 0" class="text-danger fs-12">With 5% VAT</span>
          </div>
        </div>
        <div class="row"><div  class="col-8"></div><div  class="col-4"><button  type="submit" class="btn btn-success action-save float-end"><span>Save</span></button></div></div>
      </div>

    </div>
    </form>
  </div>
</template>
<script setup>
import {onBeforeMount, onMounted, ref, watch} from "vue";
import VueMultiselect from "vue-multiselect";
import 'vue-multiselect/dist/vue-multiselect.css';
import UserSelect from "../../../components/formElements/UserSelect.vue";
import { useDealStore } from "../store/dealState";

const deal_state = useDealStore();
import useDeals from "../composables/deals";

const {getDealCommissions, commissions, addDealCommissions} = useDeals();
// import PropertyByUser from "../../../../components/formElements/PropertyByUser.vue";
const emit = defineEmits(['commission_refreshed']);
const property = ref(null);
const deal = ref(null);

const commissionsList = ref([
  {id: '', commission_amount: '', percentage: '0', commission_sources: '', vat: true, vat_amount: ''},

]);
const sourceList = [
  {name: 'Seller Side', id: 'Buyer Side'},
  {name: 'Buyer Side', id: 'Buyer Side'},
  {name: 'Cashback', id: 'Cashback'},
];
const formData = ref({
  deal_id: deal_state.data.id,
  deal_price: String(deal_state.data.deal_price),
  gross_commission: deal_state.data.gross_commission,
  net_commission: deal_state.data.net_commission,
  commissions: commissionsList,
  total_vat: 0,
  commission_sources: sourceList,
});

const calculateCommissionNew = (item) => {

  let deal_price = formData.value.deal_price ?? 0;
  deal_price = String(deal_price).replace(/,/g, '')
  let commission_amount = item.commission_amount.replace(/,/g, '');
  let percentage = commission_amount / (deal_price / 100);
  item.percentage = numberFormat(percentage);
  calculateOverAllCommission();
}
const calculateCommissionNew2 = (item) => {

  let deal_price = String(formData.value.deal_price).replace(/,/g, '');

  let percentage = item.percentage.replace(/,/g, '');
  let commission_amount = deal_price * percentage / 100;
  item.commission_amount = numberFormat(commission_amount);
  calculateOverAllCommission();
}
const calculateOverAllCommission = () => {
  let grossCommission = 0;
  let netCommission = 0;
  let vat = 0;
  formData.value.net_commission = 0;
  formData.value.total_vat = 0;
  for (let i = 0; i < commissionsList.value.length; i++) {

    let commission_amount = commissionsList.value[i].commission_amount ?? 0;
    commission_amount = commission_amount != '' ? commissionsList.value[i].commission_amount.replace(/,/g, '') : 0;
    netCommission = parseFloat(netCommission) + parseFloat(commission_amount);
    if (commissionsList.value[i].vat) {
      let temp = (commission_amount * 5) / 100;
      vat = parseFloat(vat) + parseFloat(temp);
      commissionsList.value[i].vat_amount = temp;
    }

  }
  grossCommission = parseFloat(netCommission) + parseFloat(vat);
  formData.value.gross_commission = numberFormat(grossCommission);
  formData.value.net_commission = numberFormat(netCommission);
  formData.value.total_vat = vat;
}
const doAllCalculations = () => {

    let commissionPercentage = document.getElementsByClassName('commissionPercentage');
    for (let i = 0; i < commissionPercentage.length; i++) {
        commissionPercentage[i].dispatchEvent(new Event('keyup'));
    }
    //trigger changes on element .commissionAmount
    let commissionAmount = document.getElementsByClassName('commissionAmount');
    for (let i = 0; i < commissionAmount.length; i++) {
        commissionAmount[i].dispatchEvent(new Event('keyup'));
    }
}
const removeCommission = () => {
  commissionsList.value.pop();
  calculateOverAllCommission();
}
const addCommission = () => {

  commissionsList.value.push({commission_amount: '', percentage: '0', commission_sources: '', vat: true}); //this code is not working
}
watch(() => deal_state.data, (val) => {
  if (val != null) {
    deal.value = val;
    property.value = val.property;
    property.value.price = numberFormat(val.property.price);
    formData.value.deal_id = val.id;
    formData.value.gross_commission = numberFormat(val.gross_commission);
    formData.value.net_commission = numberFormat(val.net_commission);
  }
});
watch(() => commissions.value, (val) => {

  if (val != null && val != undefined && val.length > 0) {
      commissionsList.value = [];
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
const isEven = (index) => {
  return index % 2 === 0;
}
const formatNumber = (val) => {
  return numberFormat(val);
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
const onSubmit = async () => {
  // let formData1 = new FormData();
  // for (let [key, value] of Object.entries(formData.value)) {
  //     formData1.append(key, value);
  // }
  await addDealCommissions(formData.value,deal_state.data.id);
  emit('commission_refreshed');
}
onBeforeMount(() => {
  if (deal_state.data != null) {



    property.value = deal_state.data.property;
    deal.value = deal_state.data;
    deal.value.net_commission = deal?.value?.net_commission ? deal?.value?.net_commission : 0;
    getDealCommissions(deal_state.data.id);
      if(deal_state.data.type == 'Rental'){
          sourceList.splice(0, 2);
          sourceList.push({name: 'Owner Side', id: 'Owner Side'});
          sourceList.push({name: 'Tenant Side', id: 'Tenant Side'});

      }
  }
});
onMounted(() => {

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
  border-color: #c48667 !important;
}
</style>

