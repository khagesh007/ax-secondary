<template>

    <div class="modal fade" id="deductibleModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Add New Deductible</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="modalClose()"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <form @submit.prevent="onSubmit" enctype="multipart/form-data">

                            <div class="row g-3">

                                <div class="col-3">
                                    <input type="hidden" name="deal_id">
                                    Deal Price:
                                </div>
                                <div class="col-3">
                                    AED {{ deal.data.deal_price }}

                                </div>
                                <div class="col-3">
                                    Gross Commission

                                </div>
                                <div class="col-3">
                                    AED {{ deal.data.gross_commission }}
                                </div>
                                <div class="col-3">
                                    Net Commission:

                                </div>
                                <div class="col-3">
                                    AED {{ deal.data.net_commission }}
                                </div>
                                <div class="col-md-3  col-sm-12 mt-2">Deal Type</div>
                                <div class="col-md-3  col-sm-12 mt-2 pr-0">
                                    {{ deal.data.property.for }}
                                </div>
                                <hr>
                                <div class="col-md-2  col-sm-12 mt-2">Deductible Type</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                    <v-select
                                        :options="deductibleType"
                                        label="text"
                                        :filterable="true"
                                        :clearable="true"
                                        :placeholder="'Select Deductible type'"
                                        name="deductible_type_id"
                                        :on-change="calculateDeductible()"
                                        :value="formData.deductible_type_id"
                                        :reduce="option => option.id"
                                        :get-option-label="option => option.name"
                                        :get-option-value="option => option.id"
                                        v-model="formData.deductible_type_id"
                                            >
                                    </v-select>
<!--                                    <v-select-->

<!--                                        :options="deductibleType"-->
<!--                                        label="text"-->
<!--                                        :filterable="true"-->
<!--                                        :clearable="true"-->
<!--                                        :placeholder="'Select Deductible type'"-->
<!--                                        name="deductible_type_id"-->
<!--                                        :on-change="calculateDeductible()"-->
<!--                                        :value="formData.deductible_type_id"-->
<!--                                        :reduce="option => option.id"-->
<!--                                        :get-option-label="option => option.name"-->
<!--                                        :get-option-value="option => option.id"-->
<!--                                        v-model="formData.deductible_type_id"-->
<!--                                    >-->
<!--                                        <template #option="option">-->
<!--                                            <span>{{ option.name }} ({{option.type_value}} {{option.type =='percentage' ? '%' : '/-'}})</span>-->

<!--                                        </template>-->
<!--                                    </v-select>-->


                                    <div class="input-group d-none">

                                        <UserSelect @userSelected="userSelected" :passedUsers="[]" :multiple="false"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">Amount</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-sizing-default">AED</span>
                                        <input type="text" class="form-control" name="amount"
                                               v-model="formData.amount" >
                                    </div>
                                </div>

                            </div>


                            <div class="mt-4">

                                <div class="hstack gap-2 justify-content-right float-end">
                                    <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                       data-bs-dismiss="modal" @click="modalClose()"><i
                                        class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="submit" class="btn btn-success action-add">
                                         <span v-if="showBtnLoader" class="d-flex align-items-center">
                                                            <span class="spinner-border flex-shrink-0" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </span>
                                                            <span class="flex-grow-1 ms-2">
                                                                Loading...
                                                            </span>
                                                        </span>
                                        <span v-else>Save</span>
                                    </button>
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
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';
import {useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
import axios from "axios";
import UserSelect from "../../../components/formElements/UserSelect.vue";

const emit = defineEmits(['deductibleChanged', 'closeModal']);
const modalLoader = ref(true);
const {getLeadTypes, leadTypes, updateDeal, storeDealDeductible, deductibleType, getDealDeductibleType} = useDeals();
const deal = useDealStore();
const leadTypeOptions = ref([]);
const showBtnLoader = ref(false);

const props = defineProps({
    deductible: {
        type: Object,
        required: false
    }
});
const documents = ref([]);


const formData = ref({
    id : '',
    deal_id: deal.data.id,
    deductible_type_id: '',
    gross_commission: deal.data.gross_commission,
    net_commission: deal.data.net_commission,
    title: '',
    amount: '',
});

const userSelected = (user) => {
    // console.log(user);
    // formData.value.agent_id = user.id;
}


watchEffect(() => {

})
const modalClose = () => {
    modalLoader.value = false;
    emit('closeModal');
}
const calculateDeductible = () => {

    let selectedDeductibleType = deductibleType.value.find((type) => type.id == formData.value.deductible_type_id);

    if (selectedDeductibleType) {
        formData.value.amount = selectedDeductibleType.amount;

        if(selectedDeductibleType.type == 'percentage'){
            formData.value.amount = (formData.value.gross_commission / 100) * selectedDeductibleType.type_value ;

        }else{
            formData.value.amount = selectedDeductibleType.type_value;
        }

    }
}
const onSubmit = () => {
    showBtnLoader.value = true;

    storeDealDeductible(formData.value);
    setTimeout(() => {
        modalClose();
        emit('deductibleChanged')
        showBtnLoader.value = false;
    }, 1000);
}
onMounted(() => {
    $('#deductibleModal').modal('show');
    getDealDeductibleType();
    if(props.deductible != null){
        formData.value.deductible_type_id = props.deductible.deal_deductible_type_id;
        formData.value.amount = props.deductible.amount;
        formData.value.id = props.deductible.id;
    }

});

onUnmounted(() => {
    $('#deductibleModal').modal('hide');
    modalLoader.value = true;
});

</script>
<style scoped>
.modal {
    position: fixed !important;

}
</style>
