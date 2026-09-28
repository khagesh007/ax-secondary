<template>
    <div class="row">
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12">
                    <h4>1. The Parties</h4>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>1.1. The Agent/Broker Seller's Agent</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_seller_established_name" class="form-label">Name of the
                                establishment</label>
                            <input type="text" class="form-control" id="party_seller_established_name"
                                v-model="formData.party_seller_established_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_seller_address" class="form-label">Address</label>
                            <textarea id="party_seller_address" class="form-control" cols="30" rows="3"
                                v-model="formData.party_seller_address"></textarea>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_seller_telephone" class="form-label">Telephone</label>
                            <input type="text" class="form-control" id="party_seller_telephone"
                                v-model="formData.party_seller_telephone">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_seller_fax" class="form-label">Fax</label>
                                <input type="text" id="party_seller_fax" class="form-control"
                                    v-model="formData.party_seller_fax">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_seller_po_box" class="form-label">P.O. Box</label>
                            <input type="text" id="party_seller_po_box" class="form-control"
                                v-model="formData.party_seller_po_box">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_seller_email" class="form-label">Email</label>
                                <input type="text" id="party_seller_email" class="form-control"
                                    v-model="formData.party_seller_email">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_seller_orn" class="form-label">ORN</label>
                            <input type="text" id="party_seller_orn" class="form-control"
                                v-model="formData.party_seller_orn">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_seller_ded_license" class="form-label">DED License</label>
                                <input type="text" class="form-control" id="party_seller_ded_license"
                                    v-model="formData.party_seller_ded_license">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4" id="">
                        <div>
                            <h6>Registered Agent Details</h6>
                        </div>
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Name</label>
                            <input type="text" class="form-control" v-model="formData.party_seller_agent_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="basiInput" class="form-label">BRN#</label>
                            <input type="text" class="form-control" v-model="formData.party_seller_agent_brn_no">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="title" class="form-label">Date issued</label>
                                <input type="text" class="form-control" v-model="formData.party_seller_agent_date_issued">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="basiInput" class="form-label">Mobile</label>
                            <input type="text" class="form-control" v-model="formData.party_seller_agent_mobile">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="title" class="form-label">Email</label>
                                <input type="text" class="form-control" v-model="formData.party_seller_agent_email">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Agent form A Str No.</label>
                            <input type="text" class="form-control" v-model="formData.party_seller_agent_str_no">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>1.2. The Agent/Broker Buyer's Agent</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_established_name" class="form-label">Name of the
                                establishment</label>
                            <input type="text" class="form-control" id="party_established_name"
                                v-model="formData.party_buyer_established_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_address" class="form-label">Address</label>
                            <textarea id="party_address" class="form-control" cols="30" rows="3"
                                v-model="formData.party_buyer_address"></textarea>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_telephone" class="form-label">Telephone</label>
                            <input type="text" class="form-control" id="party_telephone"
                                v-model="formData.party_buyer_telephone">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_fax" class="form-label">Fax</label>
                                <input type="text" id="party_fax" class="form-control" v-model="formData.party_buyer_fax">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_po_box" class="form-label">P.O. Box</label>
                            <input type="text" id="party_po_box" class="form-control" v-model="formData.party_buyer_po_box">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_email" class="form-label">Email</label>
                                <input type="text" id="party_email" class="form-control"
                                    v-model="formData.party_buyer_email">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_orn" class="form-label">ORN</label>
                            <input type="text" id="party_orn" class="form-control" v-model="formData.party_buyer_orn">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="title" class="form-label">DED License</label>
                                <input type="text" class="form-control" v-model="formData.party_buyer_ded_license">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4" id="">
                        <div>
                            <h6>Registered Agent Details</h6>
                        </div>
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Name</label>
                            <input type="text" class="form-control" v-model="formData.party_buyer_agent_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="basiInput" class="form-label">BRN#</label>
                            <input type="text" class="form-control" v-model="formData.party_buyer_agent_brn_no">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_buyer_agent_date_issued" class="form-label">Date
                                    issued</label>
                                <input type="date" class="form-control" v-model="formData.party_buyer_agent_date_issued">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="basiInput" class="form-label">Mobile</label>
                            <input type="text" class="form-control" v-model="formData.party_buyer_agent_mobile">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="title" class="form-label">Email</label>
                                <input type="text" class="form-control" v-model="formData.party_buyer_agent_email">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Agent form A Str No.</label>
                            <input type="text" class="form-control" v-model="formData.party_buyer_agent_str_no">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <h4>2. The Property</h4>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Address</label>
                            <textarea name="" id="" class="form-control" cols="30" rows="3"
                                v-model="formData.property_address"></textarea>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="basiInput" class="form-label">Master developer</label>
                            <input type="text" class="form-control" v-model="formData.property_master_developer">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="title" class="form-label">Master project name</label>
                                <input type="text" class="form-control" v-model="formData.property_master_project_name">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="basiInput" class="form-label">Listing agent to complete</label>
                            <input type="text" class="form-control" v-model="formData.property_listing_agent_to_complete">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="title" class="form-label">Building name</label>
                                <input type="text" class="form-control" v-model="formData.property_building_name">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Listed price</label>
                            <input type="number" class="form-control" v-model="formData.property_listet_price">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Description</label>
                            <textarea name="" id="" class="form-control" cols="30" rows="3"
                                v-model="formData.property_description"></textarea>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Does MOU exists on this
                                property?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_mou_axists"
                                        id="property_mou_axists-yes" value="1" v-model="formData.property_mou_axists">
                                    <label class="form-check-label" for="property_mou_axists-yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_mou_axists"
                                        id="property_mou_axists-no" value="0" v-model="formData.property_mou_axists">
                                    <label class="form-check-label" for="property_mou_axists-no">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Is this property tananted?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_tenanted"
                                        id="property_tenanted-yes" value="1" v-model="formData.property_tenanted">
                                    <label class="form-check-label" for="property_tenanted-yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_tenanted"
                                        id="property_tenanted-no" value="0" v-model="formData.property_tenanted">
                                    <label class="form-check-label" for="property_tenanted-no">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Maintenance fee</label>
                            <input type="number" class="form-control" v-model="formData.property_maintenace_fee">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Additional notes</label>
                            <textarea name="" id="" class="form-control" cols="30" rows="3"
                                v-model="formData.property_additional_notes"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12">
                    <h4>3. The Commission Split</h4>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="commission_seller_agent" class="form-label">Seller agent's
                                comission</label>
                            <input type="number" class="form-control" v-model="formData.commission_seller_agent">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="commission_buyer_agent" class="form-label">Seller agent's
                                    comission</label>
                                <input type="number" class="form-control" v-model="formData.commission_buyer_agent">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Buyer's Name</label>
                            <input type="text" class="form-control" v-model="formData.commission_buyer_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Transfer fee paid by</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commission_transfer_fee_paid_by"
                                        id="commission_transfer_fee_paid_by-seller" value="Seller"
                                        v-model="formData.commission_transfer_fee_paid_by">
                                    <label class="form-check-label"
                                        for="commission_transfer_fee_paid_by-seller">Seller</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commission_transfer_fee_paid_by"
                                        id="commission_transfer_fee_paid_by-buyer" value="Buyer"
                                        v-model="formData.commission_transfer_fee_paid_by">
                                    <label class="form-check-label" for="commission_transfer_fee_paid_by-buyer">Buyer</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commission_transfer_fee_paid_by"
                                        id="commission_transfer_fee_paid_by-neg" value="Neg"
                                        v-model="formData.commission_transfer_fee_paid_by">
                                    <label class="form-check-label" for="commission_transfer_fee_paid_by-neg">Neg</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Has the buyer had the pre-finance
                                approval?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commission_pre_finance_approved"
                                        id="commission_pre_finance_approved-yes" value="1"
                                        v-model="formData.commission_pre_finance_approved">
                                    <label class="form-check-label" for="commission_pre_finance_approved-yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commission_pre_finance_approved"
                                        id="commission_pre_finance_approved-no" value="0"
                                        v-model="formData.commission_pre_finance_approved">
                                    <label class="form-check-label" for="commission_pre_finance_approved-no">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Has this buyer contacted to list
                                agent?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commission_buyer_contacted_to_agent"
                                        id="commission_buyer_contacted_to_agent-yes" value="1"
                                        v-model="formData.commission_buyer_contacted_to_agent">
                                    <label class="form-check-label" for="commission_buyer_contacted_to_agent-yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commission_buyer_contacted_to_agent"
                                        id="commission_buyer_contacted_to_agent-no" value="0"
                                        v-model="formData.commission_buyer_contacted_to_agent">
                                    <label class="form-check-label" for="commission_buyer_contacted_to_agent-no">No</label>
                                </div>
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
import { ref, defineExpose, onMounted, onUnmounted } from 'vue';

import useFolders from '../../composables/folders';

const { generatePDF } = useFolders();

const props = defineProps({});

const emit = defineEmits(["setDownloadFileUrl"]);

const docURl = ref(`${documentsPath}/a2a-agreement-form-sample.pdf`)

const formData = ref({
    filename: '',
    type: "a2a-agreement-form",
    party_seller_established_name: '',
    party_seller_address: '',
    party_seller_telephone: '',
    party_seller_fax: '',
    party_seller_po_box: '',
    party_seller_email: '',
    party_seller_orn: '',
    party_seller_ded_license: '',
    party_seller_agent_name: '',
    party_seller_agent_brn_no: '',
    party_seller_agent_date_issued: '',
    party_seller_agent_mobile: '',
    party_seller_agent_email: '',
    party_seller_agent_str_no: '',

    party_buyer_established_name: '',
    party_buyer_address: '',
    party_buyer_telephone: '',
    party_buyer_fax: '',
    party_buyer_po_box: '',
    party_buyer_email: '',
    party_buyer_orn: '',
    party_buyer_ded_license: '',
    party_buyer_agent_name: '',
    party_buyer_agent_brn_no: '',
    party_buyer_agent_date_issued: '',
    party_buyer_agent_mobile: '',
    party_buyer_agent_email: '',
    party_buyer_agent_str_no: '',

    property_address: '',
    property_master_developer: '',
    property_master_project_name: '',
    property_listing_agent_to_complete: '',
    property_building_name: '',
    property_listet_price: '',
    property_description: '',
    property_mou_axists: '',
    property_tenanted: '',
    property_maintenace_fee: '',
    property_additional_notes: '',

    commission_seller_agent: '',
    commission_buyer_agent: '',
    commission_buyer_name: '',
    commission_transfer_fee_paid_by: '',
    commission_pre_finance_approved: '',
    commission_buyer_contacted_to_agent: '',
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

const submitForm = async() => {
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

</script>
<style scoped></style>
