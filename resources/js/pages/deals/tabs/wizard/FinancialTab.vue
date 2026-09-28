<template>
  <div id="wizard-p-0" role="tabpanel" aria-labelledby="wizard-h-0" class="body current"
           aria-hidden="false" style="">
    <div class="row" v-if="showInitialForm">
      <div class="col-12">
        <button type="button" v-if="commissions.length > 0" class="btn btn-sm btn-outline-primary float-end" @click="showInitialForm = false">
          <i class="mdi mdi-close"></i>
          Close
        </button>
      </div>
    </div>
      <Transition name="fade">
          <CommissionInitialize @commission_refreshed="commission_refreshed" v-if="showInitialForm" ></CommissionInitialize>
      </Transition>

    <div v-if="!showInitialForm" class="row">
      <div class="col-12">
        <div class="row row-cols-md-3 row-cols-1">
          <div class="col col-lg border-end">
            <div class="py-4 px-3">
              <h5 class="text-muted text-uppercase fs-13">Deal Price <i
                  class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
              </h5>
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <i class="ri-space-ship-line display-6 text-muted"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h4 class="mb-0">AED <span class="counter-value"
                                             data-target="197">{{ formatNumber(deal_state.data.deal_price) }}</span>
                  </h4>
                </div>
              </div>
            </div>
          </div><!-- end col -->
          <div class="col col-lg border-end">
            <div class="mt-3 mt-md-0 py-4 px-3">
              <h5 class="text-muted text-uppercase fs-13">Gross Income <i
                  class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h4 class="mb-0">AED <span class="counter-value" data-target="489.4">{{
                      formatNumber(deal_state.data.gross_commission)
                    }}</span></h4>
                </div>
              </div>
            </div>
          </div><!-- end col -->

          <div class="col col-lg">
            <div class="mt-3 mt-lg-0 py-4 px-3">
              <h5 class="text-muted text-uppercase fs-13">
                Net Income <i class="ri-arrow-down-circle-line text-success fs-18 float-end align-middle"></i>
              </h5>
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <i class=" ri-bank-card-2-line display-6 text-muted"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h4 class="mb-0">AED <span class="counter-value" data-target="2659">{{
                      formatNumber(deal_state.data.net_commission)
                    }}</span></h4>
                </div>
              </div>
            </div>
          </div><!-- end col -->
          <div class="col col-lg">
            <div class="mt-3 mt-lg-0 py-4 px-3">
              <h5 class="text-muted text-uppercase fs-13">
                Company Income <i class="ri-arrow-down-circle-line text-success fs-18 float-end align-middle"></i>
              </h5>
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <i class=" ri-wallet-3-line display-6 text-muted"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h4 class="mb-0">AED <span class="counter-value" data-target="2659">{{ totalCompanyIncome }}</span>
                  </h4>
                </div>
              </div>
            </div>
          </div><!-- end col -->
        </div>
      </div>
    </div>

    <div class="row mb-2" v-if="!showInitialForm">

      <div class="col-4"><h4>Commissions</h4></div>
      <div class="col-8">


        <button class="btn btn-default float-end ml-2" @click="refreshFinancialData">
          <i class="mdi mdi-refresh"></i>
        </button>
        <button type="button" class="btn btn-outline-primary float-end" @click="showInitialForm = true">
          <i class="mdi mdi-pencil"></i>
          Commissions
        </button>
        <button class="btn btn-success float-end mr-2" style="margin-right: 2px;"
                @click="showDealSplitModal = true">New Deal Split
        </button>

        <button class="btn btn-primary action-add float-end d-none" @click="addNewCommission">New Commission
        </button>

      </div>
    </div>
    <div v-show="placeholder" class="row">

      <div class="col-12">
        <CardPlaceholder></CardPlaceholder>
      </div>
    </div>
    <div v-show="!placeholder" v-if="splits" v-for="(split, index) in splits" :key="index">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-12">
              <h4 class="card-title mb-0">{{ split.commission_source}}'s Split </h4>
              <span class="float-end ml-2" style="    margin-left: 6px;">
                                <button @click="removeSplit(split)" class="btn btn-sm btn-outline-danger"><i
                                    class="ri-delete-bin-2-line"></i> </button>
                            </span>
              <span class="float-end">AED {{ formatNumber(split.split_amount) }}</span>
              <br>
              <span
                  class="text-muted float-end fs-12"> Created By {{ split?.created_by?.full_name }}</span>
            </div>
          </div>
        </div>


        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <div class="" v-if="split"
                   v-for="(commission, index2) in split.commissions" :key="index2">
                <div class="row finRow" v-if="index2 == 0" @click="toggleAccordion('collapseExample'+index)">
                  <div class="d-flex col-6">
                    <div @click="toggleAccordion('collapseExample45')" class="flex-shrink-0">
                      <div class="avatar-sm rounded">
                        <img :src="commission.user.thumbnail" alt=""
                             class="member-img img-fluid d-block rounded">
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="fs-14 mb-1">{{ commission.user.full_name }}</h6>
                      <p class="text-muted mb-0">{{ commission.user.position }}</p>
                    </div>
                  </div>
                  <div class="col">
                    <div class="flex-shrink-0 text-left ">

                      <p :class="commission.commission_category === 'INDIRECT' ? 'text-danger fs-12 mb-0' : 'text-success fs-12 mb-0'">
                                         <span
                                             class="ms-1 fs-14">{{
                                             commission.commission_value || "0"
                                           }}{{ commission.commission_type == 'Percentage' ? '%' : '/-' }} </span>
                      </p>
                    </div>
                  </div>
                  <div class="col-sm-auto las-col">
                    <div
                        class="d-flex flex-wrap gap-2 li-action-button align-items-center mx-auto my-3 my-lg-0 ">
                      <a href="#!" @click="editCommission(commission)"
                         class="hide btn btn-ghost-info btn-icon custom-toggle">
                        <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span> </a>
                      <a href="#!" @click="deleteDealCommission(commission.id,commission.deal_id)"
                         class="hide btn btn-ghost-danger btn-icon custom-toggle">
                        <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span> </a>

                    </div>
                    <div class="flex-shrink-0 text-end">
                      <h6 class="fs-14 mb-1">AED {{ formatNumber(commission.commission) }}</h6>

                    </div>
                  </div>
                </div>
              </div>
              <div class="collapse" :id="'collapseExample'+index">
              <div  v-if="split" v-for="(commission, index2) in split.commissions" :key="index2" >
                <div v-if="index2 > 0" class="row finRow" >
                  <div class="d-flex col-6">
                    <div  class="flex-shrink-0">
                      <div class="avatar-sm rounded">
                        <img :src="commission.user.thumbnail" alt=""
                             class="member-img img-fluid d-block rounded">
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="fs-14 mb-1">{{ commission.user.full_name }}</h6>
                      <p class="text-muted mb-0">{{ commission.user.position }}</p>
                    </div>
                  </div>
                  <div class="col">
                    <div class="flex-shrink-0 text-left ">

                      <p :class="commission.commission_category === 'INDIRECT' ? 'text-danger fs-12 mb-0' : 'text-success fs-12 mb-0'">
                                         <span
                                             class="ms-1 fs-14">{{
                                             commission.commission_value || "0"
                                           }}{{ commission.commission_type == 'Percentage' ? '%' : '/-' }} </span>
                      </p>
                    </div>
                  </div>
                  <div class="col-sm-auto las-col">
                    <div
                        class="d-flex flex-wrap gap-2 li-action-button align-items-center mx-auto my-3 my-lg-0 ">
                      <a href="#!" @click="editCommission(commission)"
                         class="hide btn btn-ghost-info btn-icon custom-toggle">
                        <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span> </a>
                      <a href="#!" @click="deleteDealCommission(commission.id,commission.deal_id)"
                         class="hide btn btn-ghost-danger btn-icon custom-toggle">
                        <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span> </a>

                    </div>
                    <div class="flex-shrink-0 text-end">
                      <h6 class="fs-14 mb-1">AED {{ formatNumber(commission.commission) }}</h6>

                    </div>
                  </div>
                </div>


              </div>
                <div v-if="split" class="row finRow">
                  <div class="col-6 d-flex">

                    <div class="flex-shrink-0">
                      <div class="avatar-sm rounded bg-dark">
                        <img src="/assets/images/logo/logo.png" style="height: 80%" alt=""
                             class="member-img img-fluid d-block rounded p-2">
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="fs-14 mb-1">AX CAPITAL</h6>
                      <p class="text-muted mb-0">Real Estate Agency</p>
                    </div>

                  </div>
                  <div class="col">-</div>
                  <div class="col-sm-auto">

                    <div class="flex-shrink-0 text-end">
                      <h6 class="fs-14 mb-1">AED {{ formatNumber(split?.company_earning?.amount) }}</h6>

                    </div>
                  </div>

                </div>
              </div>



            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="row mb-2 " v-if="!showInitialForm">
      <div class="col-10">
        <h4>Deductible</h4>
      </div>
      <div class="col-2">
        <button class="btn btn-danger float-end mr-2" @click="addDeductible">Add Fee</button>
      </div>
    </div>
    <div class="row gy-2 mb-2">

      <div class="col-md-12 col-lg-12" v-if="deductibles" v-for="(deductible, index) in deductibles" :key="index">
        <div class="card mb-0">
          <div class="card-body">
            <div class="d-lg-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="avatar-sm rounded"><img src="assets/images/users/avatar-10.jpg" alt=""
                                                    class="member-img img-fluid d-block rounded"></div>
              </div>
              <div class="ms-lg-3 my-3 my-lg-0"><a href="pages-profile.html"><h5 class="fs-16 mb-2">
                {{ deductible?.deductible_type?.name }}</h5></a>
                <p class="text-muted mb-0"></p></div>

              <div class="d-flex flex-wrap gap-2 align-items-center mx-auto my-3 my-lg-0">

                <div class="text-muted">Type :
                  ({{
                    deductible?.deductible_type?.type_value
                  }}{{ deductible?.deductible_type?.type == 'percentage' ? '%' : '/-' }})
                </div>
                <br>
                <div class="text-muted">AED {{ formatNumber(deductible.amount) }}</div>
              </div>
              <div>
                <a href="#!" @click="editDeductible(deductible)"
                   class="btn btn-ghost-info btn-icon custom-toggle">
                  <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span> </a>
                <a href="#!" @click="deleteDeductible(deductible.id,deductible.deal_id)"
                   class="btn btn-ghost-danger btn-icon custom-toggle">
                  <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span> </a>

              </div>
            </div>
          </div>
        </div>
      </div>


    </div>

    <div class="actions clearfix mt-5 d-none">
      <div class="row">
        <div class="col-8"></div>
        <div class="col-4">
          <button type="submit" class="btn btn-success action-save float-end">Next</button>
        </div>
      </div>

    </div>
    <div>
      <component @closeModal="removeCommissionModal" :is="showCommissionModal ? CommissionModal : null"
                 :commission="selectedCommission"></component>
      <component @closeModal="removeDeductibleModal" :is="showDeductibleModal ? DeductibleModal : null"
                 @deductibleChanged="deductibleChanged" :deductible="selectedDeductible"></component>
      <component @removeDealSplitModal="removeDealSplitModal" :is="showDealSplitModal ? DealSplitModal : null"
                 :splits="splits"></component>
    </div>
  </div>
</template>
<script setup>
import {onBeforeMount, onMounted, ref, watch} from "vue";
import UserSelect from "../../../../components/formElements/UserSelect.vue";
import PropertySelect from "../../../../components/formElements/PropertySelect.vue";
import {useDealStore} from "../../store/dealState";

const deal_state = useDealStore();
import useDeals from "../../composables/deals";

const {
  getDealSplits,
  splits,
    deal,
  getDeal,
  deleteDealCommission,
  getDealDeductibles,
  deductibles,
  deleteDeductible,
  deleteDealSplit,
    commissions, getDealCommissions
} = useDeals();
import CommissionModal from "../../includes/CommissionModal.vue";
import DeductibleModal from "../../includes/DeductibleModal.vue";
import DealSplitModal from "../../includes/DealSplitModal.vue";
import CardPlaceholder from "../../../../utilities/placeholder/CardPlaceholder.vue";
import CommissionInitialize from "../../includes/CommissionInitialize.vue";
const showInitialForm = ref(false);
const showCommissionModal = ref(false);
const showDealSplitModal = ref(false);
const showDeductibleModal = ref(false);
const selectedCommission = ref(null);
const selectedDeductible = ref(null);
const placeholder = ref(true);
const totalCompanyIncome = ref(0);
const accordIndex = ref('');


const editCommission = (commission) => {
  selectedCommission.value = commission;
  showCommissionModal.value = true;
}
const addNewCommission = () => {
  selectedCommission.value = null;
  showCommissionModal.value = true;
}
const addDeductible = () => {
  showDeductibleModal.value = true;
}
const editDeductible = (deductible) => {
  selectedDeductible.value = deductible;
  showDeductibleModal.value = true;
}
const refreshFinancialData = () => {
  getDealSplits(deal_state.data.id, true);
  getDealDeductibles(deal_state.data.id);
}


const removeDealSplitModal = () => {
  $("#dealSplitModal").modal('hide');
  showDealSplitModal.value = false;
  if (deal_state.data) {
    getDealSplits(deal_state.data.id);
    getDealDeductibles(deal_state.data.id);
  }

}

const removeCommissionModal = () => {
  $("#commissionModal").modal('hide');
  showCommissionModal.value = false;
}
const removeDeductibleModal = () => {
  $("#deductibleModal").modal('hide');
  showDeductibleModal.value = false;
  selectedDeductible.value = null;
}
const deductibleChanged = () => {
  getDealDeductibles(deal_state.data.id);
}
const removeSplit = (split) => {
  Swal.fire({
    html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>' +
        '<div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4>' +
        '<p class="text-muted mx-4 mb-0">Are you Sure You want to remove this deal split from this deal ?</p>' +
        '</div></div>',


    showCancelButton: true,
    cancelButtonText: 'Cancel',
    cancelButtonClass: 'btn btn-warning action-close w-xs',

    showCloseButton: true,
    confirmButtonText: 'Delete',
    confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

    buttonsStyling: false,

  }).then(function (result) {
    if (result.isConfirmed) {
      deleteDealSplit(split.id, deal_state.data.id);

    }
  })

}
const formatNumber = (val) => {
  return numberFormat(val);
}
const calculateCompanyTotalIncome = (splits) => {
  let total = 0;
  splits.value.forEach((split) => {
    total = parseFloat(total) + parseFloat(split?.company_earning?.amount);

  })
  console.log(total)
  totalCompanyIncome.value = formatNumber(total);
}
const toggleAccordion = (element) => {
  if ($("#" + element).hasClass('show')) {
    // //if open close it
    // $("#"+element).removeClass('collapse show')
    // $("#"+element).addClass('collapsing')
    // setTimeout(function(){
    //   $("#"+element).removeClass('collapsing')
    //   $("#"+element).addClass('collapse')
    // }, 500);

    $("#" + element).collapse('toggle')
  } else {
    $("#" + element).collapse('toggle')
  }

  // $('.collapse').collapse('toggle')

}
const commission_refreshed = () => {
  getDeal(deal_state.data.id)
  getDealCommissions(deal_state.data.id);
  getDealSplits(deal_state.data.id);
}

watch(() => deal.value, (val) => {
  if (val != null) {
    console.log('deal changed', val)
    deal_state.data = val;
  }
});
watch(commissions, (newVal, oldVal) => {
    if (newVal.length > 0) {
        showInitialForm.value = false;
    } else {
        showInitialForm.value = true;
    }
    })
onBeforeMount( () => {
  if (deal_state.data) {
      getDealCommissions(deal_state.data.id);
  }
})
onMounted(() => {
  if (deal_state.data) {
    getDealSplits(deal_state.data.id);
    getDealDeductibles(deal_state.data.id);

    setTimeout(() => {
      calculateCompanyTotalIncome(splits);
      placeholder.value = false;
    }, 800);

    if(splits.value.length > 0){
      showInitialForm.value = false;
    }else{
      showInitialForm.value = true;
    }
  }
})
</script>
<style scoped>
.hide {
  display: none;
}

.las-col:hover .hide {
  display: block;
  color: red;
}

.finRow {
  border-style: dashed !important;
  border: 1px #f1f1f1;
  padding: 7px;
}

 .fade-enter-active,
 .fade-leave-active {
     transition: opacity 0.1s ease;
 }

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
