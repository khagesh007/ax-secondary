<template>
    <div class="modal zoomIn" id="generatePDFFileModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-soft-danger">
                    <h5 class="modal-title" id="generatePDFFileModalLabel">Generate PDF</h5>
                    <!-- <div class="mx-2">
                        <v-select :reduce="lead => lead" :options="[]"
                            :create-option="lead => ({ id: lead, first_name: lead })" label="title" name="lead_id"
                            placeholder="Type to search leads" :style="{ width: '220px' }">
                        </v-select>
                    </div> -->
                    <!-- <div class="mx-2">
                        <v-select :reduce="lead => lead" :options="[]"
                            :create-option="lead => ({ id: lead, first_name: lead })" label="title" name="lead_id"
                            placeholder="Type to search listings" :style="{ width: '220px' }">
                        </v-select>
                    </div> -->
                    <!-- <div class="mx-2">
                        <v-select :reduce="lead => lead" :options="[]"
                            :create-option="lead => ({ id: lead, first_name: lead })" label="title" name="lead_id"
                            placeholder="Type to search deals" :style="{ width: '220px' }">
                        </v-select>
                    </div> -->

                    <button type="button" class="btn-close" @click="modalClose()" :disabled="formLoading"></button>
                </div>
                <div class="modal-body">
                    <FormF1Seller1Buyer ref="formRef" v-if="fileType === 'form-f1-property-sales-contract-seller-buyer'" :file="file" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <KycComplianceDeclarationForm ref="formRef" v-if="fileType === 'kyc-compliance-declaration-form'" :file="file" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <SalesReservationForm ref="formRef" v-if="fileType === 'sales-reservation-form'" :file="file" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <LeasingReservationForm ref="formRef" v-if="fileType === 'leasing-reservation-form'" :file="file" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <SalesLetterIntentPurchase ref="formRef" v-if="fileType === 'sales-letter-of-intent-purchase'" :file="file" @setDownloadFileUrl="setDownloadFileUrl" :creation_type="creation_type || 'new'"/>
                    <LeasingLetterIntentPayable ref="formRef" v-if="fileType === 'leasing-letter-of-intent-payables'" :file="file" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <KeyHandoverForm ref="formRef" v-if="fileType === 'key-handover-form'" :file="file" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <FormA ref="formRef" v-if="fileType === 'form-a'" :file="file" :propsSourceFile="propsSourceFile" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <KycFormIndividual ref="formRef" v-if="fileType === 'lead-kyc-form-individual'" :file="file" :propsSourceFile="propsSourceFile" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <KycFormEntity ref="formRef" v-if="fileType === 'lead-kyc-form-entity'" :file="file" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <EditA2AForm ref="formRef" v-if="fileType === 'a2a-agreement-form'" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <EditFormBForm ref="formRef" v-if="fileType === 'contract-between-buyer-and-broker-form-b'" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <EditTenancyFormDubaiForm  :file="file" ref="formRef" v-if="fileType === 'tenancy-form-dubai'" @setDownloadFileUrl="setDownloadFileUrl"/>
                    <EditMarketingFormAForm :file="file" ref="formRef" v-if="fileType === 'marketing-form-a-leasing-fillable'" :property="property" @setDownloadFileUrl="setDownloadFileUrl"/>
                </div>
                <div class="modal-footer">
                    <a :href="downloadFileUrl" target="_blank" class="btn btn-success" v-if="downloadFileUrl">
                        Download
                    </a>
                    <button type="button" class="btn btn-success action-save" @click="submitForm" :disabled="formLoading">
                        Generate
                    </button>
                    <button type="button" class="btn btn-light action-close" @click="modalClose()" :disabled="formLoading">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, defineProps, defineEmits, inject, watch, onMounted, onUnmounted } from 'vue';

import EditA2AForm from '../includes/pdfForms/EditA2AForm.vue';
import EditFormBForm from './pdfForms/EditFormBForm.vue';
import EditTenancyFormDubaiForm from './pdfForms/EditTenancyFormDubaiForm.vue';
import EditMarketingFormAForm from './pdfForms/EditMarketingFormAForm.vue';
import KycFormEntity from "./pdfForms/KycFormEntity.vue";
import KycFormIndividual from "./pdfForms/KycFormIndividual.vue";
import FormA from "./pdfForms/FormASellerBroker.vue";
import KeyHandoverForm from "./pdfForms/KeyHandoverForm.vue";
import LeasingLetterIntentPayable from "./pdfForms/LeasingLetterIntentPayable.vue";
import SalesLetterIntentPurchase from "./pdfForms/SalesLetterIntentPurchase.vue";
import LeasingReservationForm from "./pdfForms/LeasingReservationForm.vue";
import SalesReservationForm from "./pdfForms/SalesReservationForm.vue";
import KycComplianceDeclarationForm from "./pdfForms/KycComplianceDeclarationForm.vue";
import FormF1Seller1Buyer from "./pdfForms/FormF1Seller1Buyer.vue";
const emit = defineEmits(["toggleGeneratePDFFileModal"]);

const property = inject("property");

const props = defineProps({
    creation_type: {
        type: String,
        default: 'new'
    },
    fileType: {
        type: String,
        required: true
    },
    file: {
        type: Object||null,
        required: false
    },
    propsSourceFile: {
        type: Object || null,
        required: false
    }
});

const formRef = ref(null);

const downloadFileUrl = ref("");

const formLoading = ref(false);

const modalClose = async() => {
    formLoading.value = false;
    emit("toggleGeneratePDFFileModal", "", null, false);
}

const submitForm = async () => {
    formLoading.value = true;
    if (formRef.value && formRef.value.submitForm) {
        await formRef.value.submitForm();
    } else {
        console.error('submitForm method not found');
    }
    formLoading.value = false;
}

const setDownloadFileUrl = (fileUrl = "") => {
    downloadFileUrl.value = fileUrl;
}

watch(() => props.file, (file) => {
    console.log(props.file)
    downloadFileUrl.value = file?.path || '';
})

const modal = ref(null);

onMounted(() => {

console.log('props file type'+props.fileType)
    const modalEl = document.getElementById("generatePDFFileModal");
    document.body.appendChild(modalEl);
    modal.value = new bootstrap.Modal(modalEl, {
        keyboard: false,
    });
    modal.value.show();
})

onUnmounted(() => {
    modal.value.hide()
})

</script>
<style scoped></style>
