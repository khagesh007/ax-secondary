<template>
    <div class="row">
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12 text-center">
                    <h4>KYC FORM <br>
                        PROPERTY PURCHASE (INDIVIDUAL)</h4>
                </div>
                <div class="col-12">
                    <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                        NAME OF PURCHASER
                    </div>
                </div>
                <div class="col-12">

                    <div class="row mt-2" id="">
                        <div class="col-4">
                            <label for="full_name" class="form-label">Full Name</label>
                        </div>
                        <div class="col-8">
                            <input type="text" class="form-control" id="full_name" v-model="formData.full_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-4">
                            <label for="aliases" class="form-label">Aliases (if any)</label>
                        </div>
                        <div class="col-8">
                            <input type="text" class="form-control" id="aliases" v-model="formData.aliases">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-4">
                            <label for="current_residential_address" class="form-label">Current Residential
                                Address</label>
                        </div>
                        <div class="col-8">
                            <input type="text" class="form-control" id="current_residential_address"
                                   v-model="formData.current_residential_address">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="text" class="form-control" v-model="formData.email" id="email">
                        </div>
                        <div class="col-4">
                            <label for="telephone_number" class="form-label">Telephone No.</label>
                            <input type="text" class="form-control" v-model="formData.telephone_number"
                                   id="telephone_number">
                        </div>
                        <div class="col-4">
                            <label for="mobile_number" class="form-label">Mobile No.</label>
                            <input type="text" class="form-control" v-model="formData.mobile_number" id="mobile_number">
                        </div>

                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-4">
                            <label for="email" class="form-label">Date of Birth (dd/mm/yyyy)</label>
                            <input type="date" class="form-control" v-model="formData.dob" id="email">
                        </div>
                        <div class="col-4">
                            <label for="pob" class="form-label">Place of Birth</label>
                            <input type="text" class="form-control" v-model="formData.pob" id="pob">
                        </div>
                        <div class="col-4">
                            <label for="gender" class="form-label">Gender</label>
                            <br>
                            <span class="mt-1">
                            <input type="radio" name="gender" v-model="formData.gender" value="Female"> Female
                            <input type="radio" name="gender" v-model="formData.gender" value="Male"> Male
                                </span>
                        </div>

                    </div>


                    <div class="col-12 mt-2">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                            CITIZENSHIP
                        </div>
                    </div>

                    <div class="row mt-2" id="">
                        <div class="col-4">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" class="form-control" v-model="formData.nationality" id="nationality">
                        </div>
                        <div class="col-4">
                            <label for="passport_number" class="form-label">Passport No</label>
                            <input type="text" class="form-control" v-model="formData.passport_number"
                                   id="passport_number">
                        </div>
                        <div class="col-4">
                            <label for="passport_issue_country" class="form-label">Country Of Issue</label>
                            <input type="text" class="form-control" v-model="formData.passport_issue_country"
                                   id="passport_issue_country">

                        </div>

                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-4">
                            <label for="passport_issue_date" class="form-label">Issue Date (dd/mm/yyyy)</label>
                            <input type="date" class="form-control" v-model="formData.passport_issue_date"
                                   id="passport_issue_date">
                        </div>
                        <div class="col-4">
                            <label for="passport_expiry_date" class="form-label">Expiry Date (dd/mm/yyyy)</label>
                            <input type="date" class="form-control" v-model="formData.passport_expiry_date"
                                   id="passport_expiry_date">
                        </div>
                        <div class="col-4">


                        </div>

                    </div>
                    <div class="col-12 mt-2">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                            SOURCE OF FUNDS (Please check all that apply)
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <input type="checkbox" v-model="formData.fund_employment_income" id="employment_income" class="ml-2">
                            <label for="employment_income" class="form-label">Employment Income</label>

                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" v-model="formData.fund_sale_of_investment" id="sale_of_investment" class="ml-2">
                            <label for="employment_income" class="form-label">Sale of Investment or Properties </label>
                        </div>

                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <input type="checkbox" v-model="formData.fund_business_income" id="business_income" class="ml-2">
                            <label for="business_income" class="form-label">Business Income</label>

                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" v-model="formData.fund_savings" id="savings" class="ml-2">
                            <label for="savings" class="form-label">Savings</label>
                        </div>

                        <div class="col-md-2 mt-2">
                            <input type="checkbox" v-model="formData.fund_other" id="fund_other" class="ml-2">
                            <label for="fund_other" class="form-label">Other</label>

                        </div>
                        <div class="col-md-4 mt-2">
                            <input type="text" class="form-control" v-model="formData.fund_other_label" name="fund_other_text">
                        </div>

                    </div>

                    <div class="col-12 mt-2">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                            UAE RESIDENCY (if applicable)
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <label for="telephone_number" class="form-label">Residency Visa No.:</label>
                            <input type="text" class="form-control" v-model="formData.residency_visa_number">
                        </div>

                        <div class="col-4">
                            <label for="telephone_number" class="form-label" >Emirates ID No.:</label>
                            <input type="text" class="form-control" name="emirates_id_no" v-model="formData.emirates_id_no">
                        </div>
                        <div class="col-4">
                            <label for="visa_expiry_date" class="form-label">Expiry Date (dd/mm/yyyy):</label>
                            <input type="date" class="form-control" name="visa_expiry_date"  v-model="formData.visa_expiry_date">
                        </div>

                    </div>

                    <div class="col-12 mt-2">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                            EMPLOYMENT
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-6">
                            <label class="form-label">Occupation</label>
                            <input type="text" class="form-control" placeholder="" v-model="formData.occupation">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Years in Present Role</label>
                            <input type="text" class="form-control" v-model="formData.years_in_role">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label class="form-label">Employer / Establishment Name</label>
                            <input type="text" class="form-control" placeholder="" v-model="formData.employer_name">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Employer / Establishment Address</label>
                            <input type="text" class="form-control" v-model="formData.employer_address">
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                            SOURCE OF WEALTH (Please check all that apply)
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <input type="checkbox" v-model="formData.wealth_employment_income" id="employment_income"
                                   class="ml-2">
                            <label for="employment_income" class="form-label">Employment Income</label>

                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" v-model="formData.wealth_sale_of_inv_property" id="sale_of_investment"
                                   class="ml-2">
                            <label for="employment_income" class="form-label">Sale of Investment or Properties </label>
                        </div>

                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <input type="checkbox" v-model="formData.wealth_business_income" id="business_income"
                                   class="ml-2">
                            <label for="business_income" class="form-label">Business Income</label>

                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" v-model="formData.wealth_savings" id="savings" class="ml-2">
                            <label for="savings" class="form-label">Savings</label>
                        </div>


                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <input type="checkbox" v-model="formData.wealth_inheritance" id="savings" class="ml-2">
                            <label for="savings" class="form-label">Inheritance</label>
                        </div>
                        <div class="col-md-2 mt-2">
                            <input type="checkbox" v-model="formData.wealth_other" id="wealth_other" class="ml-2">
                            <label for="fund_other" class="form-label">Other</label>

                        </div>
                        <div class="col-md-4 mt-2">
                            <input type="text" class="form-control" v-model="formData.wealth_label" name="fund_other_label">
                        </div>
                    </div>
                    <div class="row mt-4" id="">
                        <div class="col-md-4">

                            <label for="savings" class="form-label">Annual Income:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="" v-model="formData.annual_income">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-3">
                            Purpose of Purchase:
                        </div>
                        <div class="col-2">
                            <input type="checkbox" v-model="formData.purchase_end_use" id="end_use" class="ml-2">
                            End Use
                        </div>
                        <div class="col-2">
                            <input type="checkbox" v-model="formData.purchase_savings" id="end_use" class="ml-2">
                            Savings
                        </div>
                        <div class="col-2">
                            <input type="checkbox" v-model="formData.purchase_other" id="end_use" class="ml-2">
                            Other
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" v-model="formData.purchase_other_label">
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-3">
                           Payment Method:
                        </div>
                        <div class="col-2">
                            <input type="checkbox" v-model="formData.payment_cash" id="end_use" class="ml-2">
                           Cash
                        </div>
                        <div class="col-2">
                            <input type="checkbox" v-model="formData.payment_cheque" id="end_use" class="ml-2">
                          Cheque
                        </div>
                        <div class="col-3">
                            <input type="checkbox" v-model="formData.payment_bank_transfer" id="end_use" class="ml-2">
                          Bank Transfer
                        </div>


                    </div>
                    <div class="row mt-2">
                        <div class="col-3">

                        </div>
                        <div class="col-2">
                            <input type="checkbox" v-model="formData.payment_mortgage" id="end_use" class="ml-2">
                            Mortgage
                        </div>
                        <div class="col-2">
                            <input type="checkbox" v-model="formData.payment_crypto" id="end_use" class="ml-2">
                            Crypto
                        </div>
                        <div class="col-2">
                            <input type="checkbox" v-model="formData.payment_other" id="end_use" class="ml-2">
                            Other
                        </div>
                        <div class="col-3">
                            <input type="text" v-model="formData.payment_other_label" class="form-control">
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            Are you current / former Politically Exposed Person?
                        </div>
                        <div class="col-2">
                            <input type="radio" name="pep" v-model="formData.pep" value="Yes" class="ml-2">
                            Yes
                            </div>
                        <div class="col-2">
                            <input type="radio" name="pep" v-model="formData.pep" value="No"  class="ml-2">
                            No
                        </div>
                        <div class="col-2">
                            <input type="radio" name="pep" v-model="formData.pep" value="Other"  class="ml-2">
                            Other
                        </div>
                        <div class="col-3">
                            <input type="text" v-model="formData.pep_other_label" class="form-control">
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <div class="col-md-6" id="pdfIframeSection">
            <iframe id="pdfIFrame" :src="docURl" frameborder="0" class="responsive-iframe" width="100%"
                    height="700px"></iframe>
        </div>
    </div>
</template>
<script setup>
import {ref, defineExpose, onMounted, watch, computed, onBeforeMount} from 'vue';
import {useRoute} from 'vue-router';
import useGlobalHelper from "../../../../composables/helpers/global";
const {getCountries, countries} = useGlobalHelper();
const route = useRoute();
import useFolders from '../../composables/folders';
import useLeads from "../../../leads/composables/leads";

const {
    lead,
    getLead,
} = useLeads();
const {
    file,
    sourceFile,
    getFile,
} = useFolders();
const {generatePDF} = useFolders();
const props = defineProps({
    file: {
        type: Object || null,
        required: false
    },
    propsSourceFile: {
        type: Object || null,
        required: false
    }
});
const lead_id = computed(() => route.query.lead_id);
const meta = ref([]);
const emit = defineEmits(["setDownloadFileUrl"]);

//const docURl = ref(null)
const docURl = ref(`${documentsPath}/KYC_Form_Individual.pdf`)

const formData = ref({
    file_id: '',
    filename: '',
    type: "lead-kyc-form-individual",
    lead_id: '',
    full_name: '',
    aliases: '',
    current_residential_address: '',
    email: '',
    telephone_number: '',
    mobile_number: '',
    dob: '',
    pob: '',
    gender: '',
    nationality: '',
    passport_number: '',
    passport_issue_country: '',
    passport_issue_date: '',
    passport_expiry_date: '',
    fund_employment_income: '',
    fund_sale_of_investment: '',
    fund_business_income: '',
    fund_savings: '',
    fund_other: '',
    fund_other_label: '',
    residency_visa_number: '',
    emirates_id_no: '',
    visa_expiry_date: '',
    occupation: '',
    years_in_role: '',
    employer_name: '',
    employer_address: '',
    wealth_employment_income: '',
    wealth_sale_of_inv_property: '',
    wealth_business_income: '',
    wealth_savings: '',
    wealth_inheritance: '',
    wealth_other: '',
    wealth_label: '',
    annual_income: '',
    purchase_end_use: '',
    purchase_savings: '',
    purchase_other: '',
    purchase_other_label: '',
    payment_cash: '',
    payment_cheque: '',
    payment_bank_transfer: '',
    payment_mortgage: '',
    payment_crypto: '',
    payment_other: '',
    payment_other_label: '',
    pep: '',
    pep_other_label: '',



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
    if (file !== null && file !== undefined) {
        const {data} = file;
        if (file.path) {
            docURl.value = file.path;
            refreshIFrame(docURl.value);
        }
        if(data != null || data != undefined){
            console.log(data)
            Object.keys(data).map(key => {
                formData.value[key] = data[key] || '';
            })
        }

        formData.value.file_id = file.id;
        return;
    }

    formData.value.deal_id = deal.id || '';
    formData.value.property_usage = '';

}
const setIframe = (url) => {
    // Get the reference to the parent element
    const pdfIframeSection = document.getElementById('pdfIframeSection');
    pdfIframeSection.innerHTML = '';
// Remove any existing iframe element
    const existingIframe = pdfIframeSection.querySelector('iframe');
    if (existingIframe) {
        pdfIframeSection.removeChild(existingIframe);
    }

// Create a new iframe element
    const newIframe = document.createElement('iframe');
    newIframe.setAttribute('src', url);
    newIframe.setAttribute('width', '100%');
    newIframe.setAttribute('height', '700px');
    newIframe.setAttribute('frameborder', '0');
    newIframe.setAttribute('scrolling', 'no');
    newIframe.setAttribute('class', 'responsive-iframe');

// Append the new iframe to the parent element
    pdfIframeSection.appendChild(newIframe);

}
const submitForm = async () => {
    emit("setDownloadFileUrl", "");
  //  let url = 'https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/file-manager/pdf/lead-kyc-form-individual.pdf';

    const res = await generatePDF(formData.value);
    if (res && res.data && res.data.status === '1') {
        if (res.data.url) {
            docURl.value = res.data.url;
            // let iframeSection = document.getElementById('pdfIframeSection');
            // iframeSection.innerHTML = '';
            // iframeSection.innerHTML = `<iframe id="pdfIFrame" src="`+res.data.url+`" frameborder="0" class="responsive-iframe" width="100%"
            //         height="700px"></iframe>`;
            // iframeSection.scrollIntoView({behavior: "smooth"});
            setIframe(docURl.value);
        //    refreshIFrame();
            emit("setDownloadFileUrl", res.data.url);
        }
    }
}

defineExpose({
    submitForm
})
watch(() => file.value, (val) => {
    docURl.value = val.path;
    formData.value.file_id = val.id;
    refreshIFrame();
})
watch(() => sourceFile.value, (val) => {
    if(file.value !== null){
        docURl.value = file.value.path;
    }else if(val !== null) {
        docURl.value = val.path;
    }

})
watch(() => lead.value, (val) => {
    //console.log(val.lead_meta)
    if (val.lead_meta != null) {
        for (let [key, value] of Object.entries(val.lead_meta)) {
            //  console.log(value.meta_key, value.meta_value)
            meta.value[value.meta_key] = value.meta_value;
            if (value.meta_key == "full_legal_name") {
                formData.value.full_legal_name = value.meta_value;
            }if (value.meta_key == "aliases") {
                formData.value.aliases = value.meta_value;
            }
            if (value.meta_key == "company_trading_name") {
                formData.value.company_trading_name = value.meta_value;
            }
            if (value.meta_key == "company_coi") {
                formData.value.coi = value.meta_value;
            }
            if (value.meta_key == "company_coi_date") {
                formData.value.doi = value.meta_value;
            }
            if (value.meta_key == "trade_license_issue_date") {
                formData.value.trade_license_issue_date = value.meta_value;
            }
            if (value.meta_key == "trade_license_expiry_date") {
                formData.value.trade_license_expiry_date = value.meta_value;
            }
            if (value.meta_key == "company_trad_license_no") {
                formData.value.company_trad_license_no = value.meta_value;
            }
            if (value.meta_key == "place_of_birth") {
                formData.value.pob = value.meta_value;
            }
            if (value.meta_key == "passport_number") {
                formData.value.passport_number = value.meta_value;
            }
            if (value.meta_key == "passport_issue_date") {
                formData.value.passport_issue_date = value.meta_value;
            }
            if (value.meta_key == "passport_expiry") {
                formData.value.passport_expiry_date = value.meta_value;
            }
            if (value.meta_key == "emirates_id_expiry") {
                formData.value.visa_expiry_date = value.meta_value;
            }
            if (value.meta_key == "emirates_id") {
                formData.value.emirates_id_no = value.meta_value;
            }

            if (value.meta_key == "employer_name") {
                formData.value.employer_name = value.meta_value;
            }
            if (value.meta_key == "annual_income") {
                formData.value.annual_income = value.meta_value;
            }

        }
    }

    //formData.address = val.address;
    formData.value.lead_id = val.id;
    formData.value.phone = val.phone;
    formData.value.email = val.email;
    formData.value.full_name = val.first_name + " " + val.last_name;
    formData.value.current_residential_address = val.address;
    formData.value.telephone_number = val.phone;
    formData.value.mobile_number = val.mobile;
    formData.value.dob = val.dob;
    formData.value.gender = val.gender;
    //get country name from countries array val.nationality_id

    setTimeout(() => {
        formData.value.nationality = lead.value.nationality_id != null ? findNationality(lead.value.nationality_id) : "";
    }, 1000);



})

const findNationality =(id) =>  {
    const foundCountry = countries.value.find(country => country.id === id);
    return foundCountry ? foundCountry.nationality : "";
}
onMounted(() => {

    if(lead_id.value !== undefined) {
        if (lead_id.value !== undefined || lead_id.value !== null || lead_id.value !== '') {
            getLead(lead_id.value);
        }
    }
    formData.value.filename = moment(new Date()).format("YYYY-MM-DD H-m-ss");
   // getFile('', 'lead-kyc-form-individual','source')

})
onBeforeMount(() => {
    getCountries();



})
onMounted(() => {

    if (Object.keys(props.file || {}).length !== 0) {

        setFormData(null, props.file)
    }
})

</script>
<style scoped>

</style>
