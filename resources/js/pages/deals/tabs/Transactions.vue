<template>
    <div class="tab-pane" id="nav-commission" role="tabpanel">
        <div class="row mb-2">
            <div class="col-4"><h4>Commissions</h4></div>
            <div class="col-8">
                <button class="btn btn-danger float-end mr-2" @click="addDeductible">Add Deductible</button>

                <button class="btn btn-primary action-add float-end mr-2" @click="addNewCommission">Add New Commission</button>
                <button class="btn btn-success float-end " @click="showDealSplitModal = true">Create Deal Split</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h4>Split 1</h4>
            </div>
        </div>
        <div class="row gy-2 mb-2">

            <div class="col-md-12 col-lg-12" v-if="commissions" v-for="(commission, index) in commissions" :key="index">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="d-lg-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded"><img src="assets/images/users/avatar-10.jpg" alt=""
                                                                    class="member-img img-fluid d-block rounded"></div>
                            </div>
                            <div class="ms-lg-3 my-3 my-lg-0"><a href="pages-profile.html"><h5 class="fs-16 mb-2">{{commission.user.full_name}}</h5></a>
                                <p class="text-muted mb-0">{{commission.user.position}}</p></div>

                            <div class="d-flex flex-wrap gap-2 align-items-center mx-auto my-3 my-lg-0">

                                <div class="text-muted">Type : {{commission.commission_category}} ({{commission.commission_value || "0"}}{{commission.commission_type == 'Percentage' ? '%' : '/-'}})</div>
                                <br>
                                <div class="text-muted">AED {{commission.commission}}</div>
                            </div>
                            <div> <a href="#!" @click="editCommission(commission)" class="btn btn-ghost-info btn-icon custom-toggle">
                                <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span>  </a>
                                <a href="#!" @click="deleteDealCommission(commission.id,commission.deal_id)" class="btn btn-ghost-danger btn-icon custom-toggle">
                                    <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span>  </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="row">
            <div class="col-12">
                <h4>Deductible</h4>
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
                            <div class="ms-lg-3 my-3 my-lg-0"><a href="pages-profile.html"><h5 class="fs-16 mb-2">{{deductible?.deductible_type?.name}}</h5></a>
                                <p class="text-muted mb-0"></p></div>

                            <div class="d-flex flex-wrap gap-2 align-items-center mx-auto my-3 my-lg-0">

                                <div class="text-muted">Type :  ({{deductible?.deductible_type?.type_value}}{{deductible?.deductible_type?.type == 'percentage' ? '%' : '/-'}})</div>
                                <br>
                                <div class="text-muted">AED {{deductible.amount}}</div>
                            </div>
                            <div>
                                <a href="#!" @click="editDeductible(deductible)" class="btn btn-ghost-info btn-icon custom-toggle">
                                <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span>  </a>
                                <a href="#!" @click="deleteDeductible(deductible.id,deductible.deal_id)" class="btn btn-ghost-danger btn-icon custom-toggle">
                                <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span>  </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <component @closeModal="removeCommissionModal" :is="showCommissionModal ? CommissionModal : null" :commission="selectedCommission"></component>
        <component @closeModal="removeDeductibleModal" :is="showDeductibleModal ? DeductibleModal : null"></component>
        <component @removeDealSplitModal="removeDealSplitModal" :is="showDealSplitModal ? DealSplitModal : null"></component>
    </div>
</template>
<script setup>
import {onMounted, ref} from 'vue'
import { useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
import DealSplitModal from "../includes/DealSplitModal.vue";
import CommissionModal from "../includes/CommissionModal.vue";
import DeductibleModal from "../includes/DeductibleModal.vue";
const { getDealCommissions, commissions , deleteDealCommission, getDealDeductibles, deductibles, deleteDeductible} = useDeals();
const deal = useDealStore();
const showCommissionModal = ref(false);
const showDealSplitModal = ref(false);
const showDeductibleModal = ref(false);
const deals = ref(deal.data);
const selectedCommission = ref(null);
const selectedDeductible = ref(null);
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

onMounted(() => {
    if(deal.data != null){
        getDealCommissions(deal.data.id);
        getDealDeductibles(deal.data.id);
    }

})
const removeDealSplitModal = () => {
    $("#dealSplitModal").modal('hide');
    showDealSplitModal.value = false;

}
const removeCommissionModal = () => {
    $("#commissionModal").modal('hide');
    showCommissionModal.value = false;
}
const removeDeductibleModal = () => {
    $("#deductibleModal").modal('hide');
    showDeductibleModal.value = false;
}
</script>
<style scoped>
.obligation {
    padding: 24px;
    text-align: center;
    border: 1px dashed;
    background: #F9FAFB;
}

.card-price-span {
    display: flex;
    width: 696px;
    padding: 12px 24px;
    justify-content: space-between;
    align-items: flex-start;
}
</style>

