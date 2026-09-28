<template>
    <div class="row">
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12 text-center">
                    <h4>KYC FORM PROPERTY PURCHASE - ENTITY</h4>
                </div>
                <div class="col-12">
                    <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                        COMPANY PARTICULARS
                    </div>
                </div>
                <div class="col-12">

                    <div class="row mt-2" id="">
                        <div class="col-4">
                            <label for="full_legal_name" class="form-label">Full Legal Name</label>
                        </div>
                        <div  class="col-8">
                            <input type="text" class="form-control" id="full_legal_name" v-model="formData.full_legal_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div  class="col-4">
                            <label for="company_trading_name" class="form-label">Trading names (if any)</label>
                        </div>
                        <div  class="col-8">
                            <input type="text" class="form-control" id="company_trading_name" v-model="formData.company_trading_name">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div  class="col-4">
                            <label for="company_trad_license_no" class="form-label">Trade license number</label>
                        </div>
                        <div  class="col-8">
                            <input type="text" class="form-control" id="company_trad_license_no" v-model="formData.company_trad_license_no">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div  class="col-4">
                            <label for="coi" class="form-label">Country of incorporation / registration</label>
                        </div>
                        <div  class="col-8">
                            <input type="text" class="form-control" v-model="formData.coi" id="coi">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div  class="col-4">
                            <label for="date_of_incorp" class="form-label">Date of incorporation / registration</label>
                        </div>
                        <div  class="col-8">
                            <input type="date" class="form-control" id="date_of_incorp" v-model="formData.doi">
                        </div>
                    </div>

                    <div class="row mt-2" id="">
                        <div  class="col-4">
                            <label for="registered_address" class="form-label">Registered address</label>
                        </div>
                        <div  class="col-8">
                            <input type="text" class="form-control" id="registered_address" v-model="formData.address">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div  class="col-4">
                            <label for="trade_license_authority" class="form-label">Trade license issuing authority</label>
                        </div>
                        <div  class="col-8">
                            <input type="text" class="form-control" id="trade_license_authority" v-model="formData.trade_license_authority">
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                            CONTACT DETAILS
                        </div>
                    </div>

                    <div class="row mt-2" id="">
                        <div class="col-md-3">
                            <label for="telephone_number" class="form-label">Telephone number</label>
                            <input type="text" class="form-control" id="telephone_number" v-model="formData.phone">
                        </div>
                        <div class="col-md-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="text" class="form-control" id="email" v-model="formData.email">
                        </div>

                        <div class="col-md-3">
                            <label for="trade_license_issue_date" class="form-label fs-12">Trade license issue date</label>
                            <input type="date" class="form-control" id="trade_license_issue_date" v-model="formData.trade_license_issue_date">
                        </div>
                        <div class="col-md-3">
                            <label for="trade_license_expiry_date" class="form-label fs-12">Trade license expiry date</label>
                            <input type="date" class="form-control" id="trade_license_expiry_date" v-model="formData.trade_license_expiry_date">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                          ULTIMATE BENEFICIAL OWNER'S (UBO) DETAILS
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-2">
                            <label for="telephone_number" class="form-label">1. Full Name</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" v-model="formData.ubo_name_1" class="form-control" >
                        </div>
                        <div class="col-md-2">
                            <label  class="form-label">Percentage owned</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" v-model="formData.ubo_percentage_1" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-2">
                            <label for="telephone_number" class="form-label">2. Full Name</label>
                        </div>
                        <div class="col-4">
                            <input type="text" v-model="formData.ubo_name_2" class="form-control" >
                        </div>
                        <div class="col-2">
                            <label  class="form-label">Percentage owned</label>
                        </div>
                        <div class="col-4">
                            <input type="text" v-model="formData.ubo_percentage_2" class="form-control">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                           PERSONS ACTING ON BEHALF OF COMPANY
                        </div>
                    </div>
                    <div class="row mt-2" >
                        <div class="col-6 text-center">
                            <label for="telephone_number" class="form-label">Full Name</label>
                        </div>

                        <div class="col-6 text-center">
                            <label  class="form-label">Relationship</label>
                        </div>

                    </div>
                    <div class="row mt-2">


                        <div class="col-6">
                            <input type="text" class="form-control" v-model="formData.company_person1_name" placeholder="1. Full Name">
                        </div>

                        <div class="col-6">
                            <input type="text" v-model="formData.company_person1_relationship" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <input type="text" class="form-control" v-model="formData.company_person2_name" placeholder="2. Full Name">
                        </div>
                        <div class="col-6">
                            <input type="text" v-model="formData.company_person2_relationship" class="form-control">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                            BUSINESS ACTIVITY DETAILS
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="form-label">Business details. (products/ services)</label>
                            <input type="text" class="form-control" placeholder="" v-model="formData.company_product">
                        </div>
                        <div class="col-12 mt-2">
                            <label class="form-label">Which are the primary countries in which the company has dealings with</label>
                            <input type="text" class="form-control" v-model="formData.company_primary_countries">
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                            OCCUPATION / BUSINESS DETAILS BENEFICIAL
                            OWNER (UBO)
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label class="form-label">What is the individual’s occupation</label>
                            <input type="text" class="form-control" v-model="formData.company_individual_occupation" placeholder="">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Name of Establishment or Employer*?</label>
                            <input type="text" v-model="formData.name_of_employer" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>
                                        Does the company deal with any individual or
                                        entity from the following countries.<br>
                                        -Democratic People’s Republic of Korea<br>
                                        -Democratic Republic of the Congo<br>
                                        -Iran<br>
                                        -Libya<br>
                                        -Somalia<br>
                                        -South Sudan<br>
                                        -Sudan<br>
                                        -Yemen<br>

                                    </td>
                                    <td>
                                        <input type="radio" value="1" name="risky_country" v-model="formData.deal_with_risky_countries"> Yes
                                        <br>
                                        <input type="radio" value="0" name="risky_country" v-model="formData.deal_with_risky_countries"> No
                                    </td>
                                </tr>
                                <tr>
                                    <td>Is the individual a current or former Politically
                                        Exposed Person (PEP)?</td>
                                    <td>
                                        <input type="radio" value="1" name="is_political"  v-model="formData.politically_exposed"> Yes
                                        <br>
                                        <input type="radio" value="0"  name="is_political" v-model="formData.politically_exposed"> No
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 text-center">
                    <h5>Declaration by Person Acting on Behalf of Client/(UBO)</h5>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>
                                        Signature
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Name of person acting
                                        on behalf of company:
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="name_of_person" v-model="formData.company_person_name">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Position in or relationship with
                                        the company
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="name_of_person" v-model="formData.company_person_relationship">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Date:
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" v-model="formData.declaration_date">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6" id="iframeDiv">
            <iframe id="pdfIFrame" :src="docURl" frameborder="0" class="responsive-iframe" width="100%"
                    height="700px"></iframe>
        </div>
    </div>
</template>
<script setup>
import {ref, defineExpose, onMounted, watch, computed, onBeforeMount} from 'vue';
import { useRoute } from 'vue-router';
const route = useRoute();
import useGlobalHelper from "../../../../composables/helpers/global";
const {getCountries, countries} = useGlobalHelper();
import useFolders from '../../composables/folders';
import useLeads from "../../../leads/composables/leads";
const {
    lead,
    getLead,
} = useLeads();
const {
    file,
    getFile,
} = useFolders();
const { generatePDF } = useFolders();
const sourceFile = ref(null);
const props = defineProps({
    file: {
        type: Object || null,
        required: false
    }
});
const lead_id = computed(() => route.query.lead_id);
const meta = ref([]);
const emit = defineEmits(["setDownloadFileUrl"]);

const docURl = ref(`${documentsPath}/tenancy-form-dubai-sample.pdf`)

const formData = ref({
    file_id: '',
    filename: '',
    type: "lead-kyc-form-entity",
    lead_id: '',
    full_legal_name: '',
    trading_name: '',
    company_trad_license_no: '',
    coi: '',
    doi: '',
    address: '',
    trade_license_authority: '',
    phone: '',
    email: '',
    trade_license_issue_date: '',
    trade_license_expiry_date: '',

    ubo_name_1: '',
    ubo_percentage_1: '',
    ubo_name_2: '',
    ubo_percentage_2: '',
    company_person1_name: '',
    company_person1_relationship: '',
    company_person2_name: '',
    company_person2_relationship: '',

    company_product: '',
    company_primary_countries: '',
    company_individual_occupation: '',
    name_of_employer: '',
    deal_with_risky_countries: '',
    politically_exposed: '',
    company_person_name: '',
    company_person_relationship: '',
    declaration_date: '',

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
            console.log(docURl.value)
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

}

const submitForm = async () => {
    emit("setDownloadFileUrl", "");
    const res = await generatePDF(formData.value);
    if (res && res.data && res.data.status === '1') {
        if (res.data.url) {
            docURl.value = res.data.url;

            refreshIFrame();
            $("#iframeDiv").html('');
            $("#iframeDiv").html(`<iframe id="pdfIFrame" src="`+res.data.url+`" frameborder="0" class="responsive-iframe" width="100%"
                    height="700px"></iframe>`);
            emit("setDownloadFileUrl", res.data.url);
        }
    }
}

defineExpose({
    submitForm
})
watch(()=> file.value, (val) => {
   console.log(val.id)
    docURl.value = val.path;
   formData.value.file_id = val.id;
})
watch(() => lead.value, (val) => {
    let coi = "";
    //console.log(val.lead_meta)
    if (val.lead_meta != null) {
        for (let [key, value] of Object.entries(val.lead_meta)) {
          //  console.log(value.meta_key, value.meta_value)
            meta.value[value.meta_key] = value.meta_value;
            if(value.meta_key == "full_legal_name"){
                formData.value.full_legal_name = value.meta_value;
            }
            if(value.meta_key == "company_trading_name"){
                formData.value.company_trading_name = value.meta_value;
            }
            if(value.meta_key == "company_coi"){
                formData.value.coi = value.meta_value;
            }
            if(value.meta_key == "company_coi_date"){
                formData.value.doi = value.meta_value;
            }
            if(value.meta_key == "trade_license_issue_date"){
                formData.value.trade_license_issue_date = value.meta_value;
            }
            if(value.meta_key == "trade_license_expiry_date"){
                formData.value.trade_license_expiry_date = value.meta_value;
            }
            if(value.meta_key == "company_trad_license_no"){
                formData.value.company_trad_license_no = value.meta_value;
            }
            if(value.meta_key == "company_trading_name"){
                formData.value.trading_name = value.meta_value;
            }
            if(value.meta_key == "company_trading_name"){
                formData.value.trading_name = value.meta_value;
            }
            if(value.meta_key == "company_trade_license_no"){
                formData.value.company_trad_license_no = value.meta_value;
            }
            if(value.meta_key == "company_trade_license_no"){
                formData.value.company_trad_license_no = value.meta_value;
            }
            if(value.meta_key == "company_coi"){
                coi = value.meta_value;
            }
        }
    }

    //formData.address = val.address;
    formData.value.lead_id = val.id;
    formData.value.phone = val.phone;
    formData.value.email = val.email;
    formData.value.address = val.address;
   // formData.value.trade_license_authority = val.address;

    setTimeout(() => {
        formData.value.coi = coi != null ? findNationality(coi) : "";
    }, 1000);

})
const findNationality =(id) =>  {
    // Find the country object with matching code using find
    const foundCountry = countries.value.find(country => country.id === id);

    // Return nationality if found, otherwise "Not found"
    return foundCountry ? foundCountry.nationality : "";
}

onMounted(() => {
    getLead(lead_id.value);
    formData.value.filename = moment(new Date()).format("YYYY-MM-DD H-m-ss");
    getFile('','lead-kyc-form-entity')

})
onBeforeMount(() => {
    getCountries();
})
onMounted(() => {

    if(Object.keys(props.file || {}).length !== 0){

        setFormData(null, props.file)
    }
})

</script>
<style scoped></style>
