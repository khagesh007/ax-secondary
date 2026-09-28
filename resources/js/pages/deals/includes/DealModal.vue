<template>

    <div class="modal fade" id="dealModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">View/Update Deal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="modalClose()"></button>
                </div>
                <div class="modal-body">
                    <div v-if="!modalLoader">
                        <form @submit.prevent="onSubmit" enctype="multipart/form-data">

                                    <div class="row g-3">

                                            <div class="col-2">
                                                <input type="hidden" name="deal_id" v-model="deals.id">
                                                Property
                                            </div>
                                            <div class="col-4">
                                                {{deals.property.ref_no}}
                                            </div>
                                        <div class="col-2">
                                                Contact
                                            </div>
                                            <div class="col-4">
                                                {{deals.contact.first_name}} {{deals.contact.last_name}}
                                            </div>
                                        <div class="col-md-2  col-sm-12 mt-2">Deal Type</div>
                                        <div class="col-md-4  col-sm-12 mt-2 pr-0">
                                            {{deals?.deal_type?.name}}
                                        </div>




                                        <div class="col-md-6  col-sm-12 mt-2"></div>
                                        <div class="col-md-2  col-sm-12 mt-2">Amount</div>
                                        <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                            <div class="input-group">
                                                <span class="input-group-text" id="inputGroup-sizing-default">AED</span>
                                                <input type="text" class="form-control" name="deal_price" @keyup="onPriceChange()" v-model="formData.deal_price" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                            </div>
                                        </div>
                                        <div class="col-md-2  col-sm-12 mt-2">Initial Deposit</div>
                                        <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                            <div class="input-group">
                                                <span class="input-group-text" id="inputGroup-sizing-default">AED</span>
                                                <input type="text" class="form-control" name="initial_deposit" v-model="formData.initial_deposit" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                            </div>
                                        </div>
                                        <div class="col-md-2  col-sm-12 mt-2">Commission Percentage</div>
                                        <div class="col-md-2  col-sm-12 mt-2 pr-0">

                                            <div class="input-group">
                                                <span class="input-group-text" >%</span>
                                                <input type="text" class="form-control" v-model="formData.commission_percentage" @keyup="onPriceChange()" id="input_commission_percentage" name="input_commission_percentage" >
                                            </div>
                                        </div>
                                        <div class="col-md-8  col-sm-12 mt-2 pr-0"></div>

                                        <div class="col-md-2  col-sm-12 mt-2">Gross Commission</div>
                                        <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                            <div class="input-group">
                                                <span class="input-group-text" >AED</span>
                                                <input type="text" class="form-control" id="input_gross_commission" name="gross_commission" v-model="formData.gross_commission" >
                                            </div>
                                        </div>
                                        <div class="col-md-2  col-sm-12 mt-2">Net Commission</div>
                                        <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                            <div class="input-group">
                                                <span class="input-group-text" >AED</span>
                                                <input type="text" class="form-control" id="input_net_commission" name="net_commission" v-model="formData.net_commission" >
                                            </div>
                                        </div>

                                        <div class="col-md-2  col-sm-12 mt-2">Status</div>
                                        <div class="col-md-4  col-sm-12 mt-2 pr-0">
                                            {{deals.status}}
                                        </div>
                                    </div>

                            <div class="row mt-2">
                                <div class="col-6" v-for="(item, index) in documentsType" :key="index">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 mt-2">
                                            {{ item }}
                                        </div>
                                        <div class="col-md-8 col-sm-12 mt-2">


                                            <div class="input-group">
                                                <input type="file" class="form-control inputs"
                                                       :name="'document_'+index"
                                                       :id="'document_'+index"
                                                       placeholder="Enter first name"
                                                       @change="onFileChange(index,item)"
                                                       aria-label="Upload"
                                                >

<!--                                                <a v-if="leadDocumentFromDb[index]" :href="leadDocumentFromDb[index]" target="_blank"-->
<!--                                                   class="btn btn-outline-success btn-icon waves-effect waves-light"-->
<!--                                                   style="padding: 0.3rem;"><i class="ri-eye-line"></i></a>-->
<!--                                                <button v-if="leadDocumentFromDb[index]"-->
<!--                                                        class="btn btn-outline-danger btn-icon waves-effect waves-light"-->
<!--                                                        style="padding: 0.3rem;" type="button"-->
<!--                                                        @click="deleteLeadDocument(lead.id,index)"-->
<!--                                                ><i class=" ri-delete-bin-line"></i></button>-->

                                            </div>
                                        </div>
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
                    <div v-else>
                        <!-- Border spinner -->
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
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

const emit = defineEmits(['removeLeadModal']);
const modalLoader = ref(true);
const {getLeadTypes, leadTypes, updateDeal} = useDeals();
const {leadResources} = useDealStore();
const deal = useDealStore();
const leadTypeOptions = ref([]);

const props = defineProps({
    deals: {
        type: Object,
        required: false
    }
});
const documents = ref([]);
const documentsType = ref({
    passport: 'Passport copy',
    visa: 'Visa copy',
});
const selectedDocument = ref({
    other_documents: []
});
const formData = ref({
    deal_id: deal.data.id,
    deal_price: numberFormat(deal.data.deal_price),
    commission_percentage: 0,
    initial_deposit: numberFormat(deal.data.initial_deposit),
    net_commission: numberFormat(deal.data.net_commission),
    gross_commission: numberFormat(deal.data.gross_commission),
});

const onFileChange = (name,title) => {
    let file = document.getElementById('document_'+name).files[0];
    formData.value[name] = URL.createObjectURL(file);
    selectedDocument.value['other_documents'].push({
        name: name,
        title: title,
        file: file
    });


}


const onPriceChange = (e) => {
    let deal_price = formData.value.deal_price.replace(/,/g, '')
    let commission_percentage = formData.value.commission_percentage;
    let gross_commission = (deal_price * commission_percentage) / 100;
    formData.value.gross_commission = numberFormat(gross_commission);
    formData.value.net_commission = numberFormat(gross_commission);
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
    emit('removeLeadModal');
    modalLoader.value = false;
}
const onSubmit = () => {

  const formRes = updateDeal(formData.value).then(res => {
      deal.data = res.data.data;
       if(res.data.status === '1') {
           $('#dealModal').modal('hide');
           modalLoader.value = false;
           //emit('removeLeadModal');
       }
    }).catch(err => {
        console.log(err);
  });

}
onMounted(() => {

    if (props.deals) {
        $('#dealModal').modal('show');
        setTimeout(() => {
            modalLoader.value = false;
        }, 1000);
    }

    if (!leadResources.leadTypes) {

        getLeadTypes();
        leadResources.leadTypes = leadTypes;
        console.log(leadResources);
    }

});

onUnmounted(() => {
    $('#dealModal').modal('hide');
    modalLoader.value = true;
});

</script>
<style scoped>
.modal {
    position: fixed !important;

}
</style>
