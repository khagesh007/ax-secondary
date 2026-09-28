<template>
    <div class="row">
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12">
                    <h4>Tenancy Contract</h4>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Property Usage</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_usage"
                                        id="property_usage-Industrial" value="Industrial" v-model="formData.property_usage">
                                    <label class="form-check-label" for="property_usage-Industrial">Industrial</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_usage"
                                        id="property_usage-Commercial" value="Commercial" v-model="formData.property_usage">
                                    <label class="form-check-label" for="property_usage-Commercial">Commercial</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_usage"
                                        id="property_usage-Residential" value="Residential"
                                        v-model="formData.property_usage">
                                    <label class="form-check-label" for="property_usage-Residential">Residential</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="owner_name" class="form-label">Owner Name</label>
                            <input type="text" class="form-control" id="owner_name" v-model="formData.owner_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="landlord_name" class="form-label">Landlord Name</label>
                            <input type="text" class="form-control" id="landlord_name" v-model="formData.landlord_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="tenant_name" class="form-label">Tenant Name</label>
                            <input type="text" class="form-control" id="tenant_name" v-model="formData.tenant_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="tenant_email" class="form-label">Tenant Email</label>
                            <input type="text" class="form-control" id="tenant_email" v-model="formData.tenant_email">
                        </div>
                        <div class="col-md-6">
                            <label for="landlord_email" class="form-label">Landlord Email</label>
                            <input type="text" class="form-control" id="landlord_email" v-model="formData.landlord_email">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="tenant_phone" class="form-label">Tenant Phone</label>
                            <input type="text" class="form-control" id="tenant_phone" v-model="formData.tenant_phone">
                        </div>
                        <div class="col-md-6">
                            <label for="landlord_phone" class="form-label">Landlord Phone</label>
                            <input type="text" class="form-control" id="landlord_phone" v-model="formData.landlord_phone">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="building_name" class="form-label">Building Name</label>
                            <input type="text" class="form-control" id="building_name" v-model="formData.building_name">
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" v-model="formData.location">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-4">
                            <label for="property_size" class="form-label">Property Size(S.M)</label>
                            <input type="text" id="property_size" class="form-control" v-model="formData.property_size">
                        </div>
                        <div class="col-md-4">
                            <div>
                                <label for="property_type" class="form-label">Property Type</label>
                                <input type="text" id="property_type" class="form-control" v-model="formData.property_type">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div>
                                <label for="property_no" class="form-label">Property No</label>
                                <input type="text" id="property_no" class="form-control" v-model="formData.property_no">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="premises_no" class="form-label">Premises No(DEWA)</label>
                            <input type="text" id="premises_no" class="form-control" v-model="formData.premises_no">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="plot_no" class="form-label">Plot No</label>
                                <input type="text" class="form-control" id="plot_no" v-model="formData.plot_no">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="contract_period_to" class="form-label">Contract Period(To)</label>
                            <input type="date" id="contract_period_to" class="form-control"
                                v-model="formData.contract_period_to">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="contract_period_from" class="form-label">Contract Period(From)</label>
                                <input type="date" class="form-control" id="contract_period_from"
                                    v-model="formData.contract_period_from">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="annual_rent" class="form-label">Annual Rent</label>
                            <input type="number" class="form-control" id="annual_rent" v-model="formData.annual_rent">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="contract_value" class="form-label">Contract Value</label>
                            <input type="number" class="form-control" id="contract_value" v-model="formData.contract_value">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="security_deposit_amount" class="form-label">Security Deposit Amount</label>
                            <input type="number" id="security_deposit_amount" class="form-control"
                                v-model="formData.security_deposit_amount">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="mode_of_payment" class="form-label">Mode of Payment</label>
                                <input type="text" class="form-control" id="mode_of_payment"
                                    v-model="formData.mode_of_payment">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <iframe id="pdfIFrame" :src="docURl" frameborder="0" class="responsive-iframe" width="100%"
                height="700px"></iframe>
        </div>
    </div>
</template>
<script setup>
import { ref, defineExpose, onMounted } from 'vue';

import useFolders from '../../composables/folders';

const { generatePDF } = useFolders();

const props = defineProps({
    file: {
        type: Object || null,
        required: false
    }
});

const emit = defineEmits(["setDownloadFileUrl"]);

const docURl = ref(`${documentsPath}/tenancy-form-dubai-sample.pdf`)

const formData = ref({
    file_id: '',
    filename: '',
    type: "tenancy-form-dubai",
    deal_id: '',
    property_usage: '',
    owner_name: '',
    landlord_name: '',
    tenant_name: '',
    tenant_email: '',
    landlord_email: '',
    tenant_phone: '',
    landlord_phone: '',
    building_name: '',
    location: '',
    property_size: '',
    property_type: '',
    property_no: '',
    premises_no: '',
    plot_no: '',
    contract_period_to: '',
    contract_period_from: '',
    annual_rent: '',
    contract_value: '',
    security_deposit_amount: '',
    mode_of_payment: '',
})

const refreshIFrame = () => {
    const iframe = document.getElementById('pdfIFrame');
    if (iframe) {
        const contentDoc = iframe.contentDocument;

        if (contentDoc) {
            contentDoc.location.reload(true);
        }
    }
}

const setFormData = (deal, file = null, reset = false) => {
    // if(deal === null || reset === true){
    //     Object.keys(formData.value).map(key => {
    //         if(key == "filename" || key == "type"){
    //             return;
    //         }
    //         formData.value[key] = '';
    //     })
    //     return
    // }
    if(file !== null && file !== undefined){
        const {data} = file;
        if(file.path){
            docURl.value = file.path;
            refreshIFrame();
        }
        Object.keys(data).map(key => {
            formData.value[key] = data[key] || '';
        })
        formData.value.file_id = file.id;
        return;
    }

    formData.value.deal_id = deal.id|| '';
    formData.value.property_usage = '';
    formData.value.owner_name = '';
    formData.value.landlord_name = '';
    formData.value.tenant_name = '';
    formData.value.tenant_email = '';
    formData.value.landlord_email = '';
    formData.value.tenant_phone = '';
    formData.value.landlord_phone = '';
    formData.value.building_name = '';
    formData.value.location = '';
    formData.value.property_size = '';
    formData.value.property_no = '';
    formData.value.premises_no = '';
    formData.value.plot_no = '';
    formData.value.contract_period_to = '';
    formData.value.contract_period_from = '';
    formData.value.contract_period_from = '';
    formData.value.contract_value = '';
    formData.value.security_deposit_amount = '';
    formData.value.mode_of_payment = '';
}

const submitForm = async () => {
    emit("setDownloadFileUrl", "");
    const res = await generatePDF(formData.value);
    if (res && res.data && res.data.status === '1') {
        if (res.data.url) {
            docURl.value = res.data.url;
            refreshIFrame();
            emit("setDownloadFileUrl", res.data.url);
        }
    }
}

defineExpose({
    submitForm
})

onMounted(() => {
    formData.value.filename = moment(new Date()).format("YYYY-MM-DD H-m-ss");
})

onMounted(() => {
    if(Object.keys(props.file || {}).length !== 0){
        setFormData(null, props.file)
    }
})

</script>
<style scoped></style>
