<template>
    <div class="row">
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12">
                    <h4>Part 1 [Parties]</h4>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>The Broker Office/Agent</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_agent_office_name" class="form-label">Office Name</label>
                            <input type="text" class="form-control" id="party_agent_office_name"
                                v-model="formData.party_agent_office_name">
                        </div>
                    </div>

                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_agent_license_authority" class="form-label">License Authority</label>
                            <input type="text" class="form-control" id="party_agent_license_authority"
                                v-model="formData.party_agent_license_authority">
                        </div>
                    </div>

                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_agent_orn" class="form-label">ORN</label>
                            <input type="text" class="form-control" id="party_agent_orn"
                                v-model="formData.party_agent_orn">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_agent_license_number" class="form-label">License Number</label>
                                <input type="text" id="party_agent_license_number" class="form-control"
                                    v-model="formData.party_agent_license_number">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_agent_fax" class="form-label">FAX</label>
                            <input type="text" id="party_agent_fax" class="form-control"
                                v-model="formData.party_agent_fax">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_agent_phone" class="form-label">Phone</label>
                                <input type="text" id="party_agent_phone" class="form-control"
                                    v-model="formData.party_agent_phone">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_agent_address" class="form-label">Address</label>
                            <textarea id="party_agent_address" class="form-control" cols="30" rows="3"
                                v-model="formData.party_agent_address"></textarea>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_agent_email" class="form-label">Email</label>
                            <input type="text" id="party_agent_email" class="form-control"
                                v-model="formData.party_agent_email">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_agent_name" class="form-label">Agent Name</label>
                            <input type="text" id="party_agent_name" class="form-control"
                                v-model="formData.party_agent_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_agent_brn" class="form-label">BRN</label>
                            <input type="text" id="party_agent_brn" class="form-control"
                                v-model="formData.party_agent_brn">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_agent_mobile" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="party_agent_mobile"
                                    v-model="formData.party_agent_mobile">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>The Buyer's Details</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_buyer_name" class="form-label">Name of Buyer</label>
                            <input type="text" class="form-control" id="party_buyer_name"
                                v-model="formData.party_buyer_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_buyer_id_card_number" class="form-label">ID Card Number</label>
                            <input type="text" class="form-control" id="party_buyer_id_card_number"
                                v-model="formData.party_buyer_id_card_number">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_buyer_nationality" class="form-label">Nationality</label>
                                <input type="text" id="party_buyer_nationality" class="form-control" v-model="formData.party_buyer_nationality">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_buyer_passport_no" class="form-label">Passport No</label>
                            <input type="text" class="form-control" id="party_buyer_passport_no"
                                v-model="formData.party_buyer_passport_no">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_buyer_passport_expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" id="party_buyer_passport_expiry_date" class="form-control" v-model="formData.party_buyer_passport_expiry_date">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_buyer_mobile" class="form-label">Mobile</label>
                            <input type="text" class="form-control" id="party_buyer_mobile"
                                v-model="formData.party_buyer_mobile">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_buyer_po_box" class="form-label">PO Box</label>
                                <input type="text" id="party_buyer_po_box" class="form-control" v-model="formData.party_buyer_po_box">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="party_buyer_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="party_buyer_phone"
                                v-model="formData.party_buyer_phone">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="party_buyer_fax" class="form-label">FAX</label>
                                <input type="text" id="party_buyer_fax" class="form-control" v-model="formData.party_buyer_fax">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_buyer_address" class="form-label">Address</label>
                            <textarea id="party_buyer_address" class="form-control" cols="30" rows="3"
                                v-model="formData.party_buyer_address"></textarea>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="party_buyer_email" class="form-label">Email</label>
                            <input type="text" id="party_buyer_email" class="form-control" v-model="formData.party_buyer_email">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <h4>Part 2 [The Property]</h4>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>The Property Details</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="property_type_of_area" class="form-label">Type of Area</label>
                            <input type="text" id="property_type_of_area" class="form-control" v-model="formData.property_type_of_area">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="property_location" class="form-label">Location</label>
                                <input type="text" id="property_location" class="form-control" v-model="formData.property_location">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="property_type_of_property" class="form-label">Type of Property</label>
                            <input type="text" id="property_type_of_property" class="form-control" v-model="formData.property_type_of_property">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="property_buyer_budget" class="form-label">Buyer's Budget</label>
                            <input type="number" id="property_buyer_budget" class="form-control" v-model="formData.property_buyer_budget">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="property_area" class="form-label">Area</label>
                            <input type="text" id="property_area" class="form-control" v-model="formData.property_area">
                        </div>
                    </div>

                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="property_tenanted_property" class="form-label">Tenanted Property</label>
                            <input type="text" id="property_tenanted_property" class="form-control" v-model="formData.property_tenanted_property">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="property_present_use" class="form-label">Present Use</label>
                                <input type="text" id="property_present_use" class="form-control" v-model="formData.property_present_use">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="property_special_requirements" class="form-label">Special Requirements</label>
                            <textarea name="" id="property_special_requirements" class="form-control" cols="30" rows="3"
                                v-model="formData.property_special_requirements"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>Property Financials</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="property_financial_ready_cash" class="form-label">Ready Cash</label>
                            <input type="text" id="property_financial_ready_cash" class="form-control" v-model="formData.property_financial_ready_cash">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="property_financial_finance_amout" class="form-label">Finance</label>
                                <input type="number" id="property_financial_finance_amout" class="form-control" v-model="formData.property_financial_finance_amout">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="property_financial_approval_date" class="form-label">Date of Approval</label>
                            <input type="date" id="property_financial_approval_date" class="form-control" v-model="formData.property_financial_approval_date">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="property_financial_bank" class="form-label">Bank</label>
                            <input type="text" id="property_financial_bank" class="form-control" v-model="formData.property_financial_bank">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <h4>Part 3. [Commission & The Duration of The Agreement]</h4>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="commission_start_date" class="form-label">Start Date</label>
                            <input type="date" id="commission_start_date" class="form-control" v-model="formData.commission_start_date">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="commission_end_date" class="form-label">End Date</label>
                                <input type="date" id="commission_end_date" class="form-control" v-model="formData.commission_end_date">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="commission_broker_commission" class="form-label">Broker's Commission</label>
                            <input type="number" id="commission_broker_commission" class="form-control" v-model="formData.commission_broker_commission">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="commission_activity_reporting" class="form-label">Activity Reporting</label>
                                <input type="text" id="commission_activity_reporting" class="form-control" v-model="formData.commission_activity_reporting">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <h4>Part 4. [The Signature of The Parties]</h4>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>First Party [The Broker Office]</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="signature_party_name" class="form-label">Name</label>
                            <input type="text" id="signature_party_name" class="form-control" v-model="formData.signature_party_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="signature_title" class="form-label">Title</label>
                            <input type="text" id="signature_title" class="form-control" v-model="formData.signature_title">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="signature_date" class="form-label">Date</label>
                            <input type="date" id="signature_date" class="form-control" v-model="formData.signature_date">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>Second Party [The Buyer(s)]</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="signature_buyer_name_english" class="form-label">Buyer Name(In English)</label>
                            <input type="text" id="signature_buyer_name_english" class="form-control" v-model="formData.signature_buyer_name_english">
                        </div>
                        <div class="col-md-6">
                            <label for="signature_buyer_name_arabic" class="form-label">Buyer Name(In Arabic)</label>
                            <input type="text" id="signature_buyer_name_arabic" class="form-control" v-model="formData.signature_buyer_name_arabic">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="signature_legal_representative_english" class="form-label">Legal Representative(In English)</label>
                            <input type="text" id="signature_legal_representative_english" class="form-control" v-model="formData.signature_legal_representative_english">
                        </div>
                        <div class="col-md-6">
                            <label for="signature_legal_representative_arabic" class="form-label">Legal Representative(In Arabic)</label>
                            <input type="text" id="signature_legal_representative_arabic" class="form-control" v-model="formData.signature_legal_representative_arabic">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="signature_legal_attorney_number" class="form-label">Attorney Number</label>
                            <input type="text" id="signature_legal_attorney_number" class="form-control" v-model="formData.signature_legal_attorney_number">
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

const docURl = ref(`${documentsPath}/contract-between-buyer-and-broker-form-b-sample.pdf`)

const formData = ref({
    filename: '',

    type: "contract-between-buyer-and-broker-form-b",

    party_agent_office_name: '',
    party_agent_license_authority: '',
    party_agent_orn: '',
    party_agent_license_number: '',
    party_agent_fax: '',
    party_agent_phone: '',
    party_agent_address: '',
    party_agent_email: '',
    party_agent_name: '',
    party_agent_brn: '',
    party_agent_mobile: '',

    party_buyer_name: '',
    party_buyer_id_card_number: '',
    party_buyer_nationality: '',
    party_buyer_passport_no: '',
    party_buyer_passport_expiry_date: '',
    party_buyer_mobile: '',
    party_buyer_po_box: '',
    party_buyer_phone: '',
    party_buyer_fax: '',
    party_buyer_address: '',
    party_buyer_email: '',


    property_type_of_area: '',
    property_location: '',
    property_type_of_property: '',
    property_buyer_budget: '',
    property_area: '',
    property_tenanted_property: '',
    property_present_use: '',
    property_special_requirements: '',

    property_financial_ready_cash: '',
    property_financial_finance_amout: '',
    property_financial_approval_date: '',
    property_financial_bank: '',


    commission_start_date: '',
    commission_end_date: '',
    commission_broker_commission: '',
    commission_activity_reporting: '',


    signature_party_name: '',
    signature_title: '',
    signature_date: '',

    signature_buyer_name_english: '',
    signature_buyer_name_arabic: '',
    signature_legal_representative_english: '',
    signature_legal_representative_arabic: '',
    signature_legal_attorney_number: '',
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
        docURl.value = "http://localhost:8000/contact-between-buyer-and-broker-form-b-sample.pdf";
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
