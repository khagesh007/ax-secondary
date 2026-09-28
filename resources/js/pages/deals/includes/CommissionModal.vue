<template>

    <div class="modal fade" id="commissionModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">{{commission !=null ? 'Update' : 'Add New'}} Commission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="modalClose()"></button>
                </div>
                <div class="modal-body">
                    <div >
                        <form @submit.prevent="onSubmit" enctype="multipart/form-data">

                            <div class="row g-3">

                                <div class="col-2">
                                    <input type="hidden" name="deal_id">
                                    Deal Price:
                                </div>
                                <div class="col-4">
                                    AED {{deal.deal_price}}

                                </div>
                                <div class="col-2">
                                    Gross Commission :

                                </div>
                                <div class="col-4">
                                    AED {{deal.gross_commission}}
                                </div>
                                <div class="col-2">
                                    Net Commission:

                                </div>
                                <div class="col-4">
                                    AED {{deal.net_commission}}
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">Deal Type</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">
                                    Secondary
                                </div>
                            </div>
                                <hr>
                                <h4>Commission</h4>
                                <div v-if="commission" class="row">
                                    <div class="col-3">Commission Category</div>
                                    <div class="col-3">{{commission.commission_category}}</div>
                                    <div class="col-3">Commission Type</div>
                                    <div class="col-3"> {{commission.commission_value || "0"}}{{commission.commission_type == 'Percentage' ? '%' : '/-'}}</div>
                                </div>
                                <div class="row">

                                <div class="col-md-2  col-sm-12 mt-2">Agent</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                    <div class="input-group">
                                        <UserSelect @userSelected="userSelected" :passedUsers="[formData.commission_user]" :multiple="false"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">Amount</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-sizing-default">AED</span>
                                        <input type="text" class="form-control" v-model="formData.commission" name="commission" >
                                    </div>
                                </div>

                            </div>




                            <div class="mt-4">

                                <div class="hstack gap-2 justify-content-right float-end">
                                    <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                       data-bs-dismiss="modal" @click="modalClose()"><i
                                        class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="submit" class="btn btn-success action-add">Save</button>
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
import {useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
import axios from "axios";
import UserSelect from "../../../components/formElements/UserSelect.vue";
const emit = defineEmits(['removeLeadModal']);
const modalLoader = ref(true);
const {getLeadTypes, leadTypes, updateDeal} = useDeals();
const {leadResources} = useDealStore();
const deal_state = useDealStore();
const leadTypeOptions = ref([]);
const props = defineProps({
    commission: {
        type: Object,
        required: false
    },
    // deals: {
    //     type: Object,
    //     required: false
    // }
});
const documents = ref([]);
const deal = deal_state.data;

const formData = ref({
    deal_id: deal_state.data.id,
    deal_price: deal_state.data.deal_price,
    commission: 0,
    commission_user : [],

});

const onFileChange = (name,title) => {
    let file = document.getElementById('document_'+name).files[0];
    formData.value[name] = URL.createObjectURL(file);



}

const userSelected = (user) => {
     console.log(user);
    // formData.value.agent_id = user.id;
}
watchEffect(() => {

    if (props.leads) {
        setTimeout(() => {
            modalLoader.value = false;
            $('#dealModal').modal('show');
        }, 2000);
    } else {
        modalLoader.value = true;
    }

})
const modalClose = () => {
    emit('closeModal');
    modalLoader.value = false;
}
const onSubmit = () => {
    const formRes = updateDeal(formData.value).then(res => {
        deal.data = res.data.data;
        if(res.data.status === '1') {
            $('#dealModal').modal('hide');
            modalLoader.value = false;
            emit('closeModal');
        }
    }).catch(err => {
        console.log(err);
    });

}
onMounted(() => {
    $('#commissionModal').modal('show');
    setTimeout(() => {
        modalLoader.value = false;
    }, 1000);
    if(props.commission){
        formData.value.commission = props.commission.commission;
        formData.value.commission_user = props.commission.user;
    }

//$("#agent_id").getAgentSelect2("#commissionModal")


});

onUnmounted(() => {
    $('#commissionModal').modal('hide');
    modalLoader.value = true;
});

</script>
<style scoped>
.modal {
    position: fixed !important;

}
</style>
