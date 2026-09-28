<template>
    <div class="row">
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12 text-center">
                    <h4>LEASE RESERVATION FORM</h4>

                </div>

                <div class="col-12">

                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="name_of_property" class="form-label">Name of property</label>
                            <input type="text" class="form-control" id="name_of_property"
                                   v-model="formData.property_name">
                        </div>
                        <div class="col-6 mt-4">
                            <input type="checkbox" name="residential" class="mt-1" v-model="formData.residential">
                            Residential
                            <input type="checkbox" name="commercial" class="mt-1" v-model="formData.commercial">
                            Commercial
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" v-model="formData.unit">
                        </div>
                        <div class="col-6">
                            <label for="unit_types" class="form-label">Unit Types</label>
                            <input type="text" class="form-control" id="unit_types" v-model="formData.unit_types">
                        </div>
                        <div class="col-6">
                            <label for="unit_ref" class="form-label">Unit Ref#</label>
                            <input type="text" class="form-control" id="unit_ref" v-model="formData.unit_ref">
                        </div>
                        <div class="col-6">
                            <label for="unit_area" class="form-label">Unit Area</label>
                            <input type="text" class="form-control" id="unit_area" v-model="formData.unit_area">
                        </div>

                    </div>
                    <div class="row mt-2">

                        <div class="col-12">
                            <label for="tenant" class="form-label">Tenant</label>
                            <input type="text" class="form-control" id="tenant" v-model="formData.tenant">
                        </div>
                    </div>
                    <div class="row mt-2">

                        <div class="col-4">
                            <label for="tenant" class="form-label">Contact Type</label>
                            <input type="text" class="form-control" id="tenant" v-model="formData.tenant">
                        </div>
                        <div class="col-2">
                            <label for="tenant" class="form-label">Individual</label>
                            <br>
                            <input type="checkbox" v-model="formData.individual">
                        </div>
                        <div class="col-2">
                            <label for="tenant" class="form-label">Corporate</label>
                            <br>
                            <input type="checkbox" v-model="formData.corporate">
                        </div>
                        <div class="col-2">
                            <label for="tenant" class="form-label">Government</label>
                            <br>
                            <input type="checkbox" v-model="formData.government">
                        </div>
                    </div>
                    <div class="row mt-2">

                        <div class="col-6">
                            <label for="occupants_name" class="form-label">Name of Occupants</label>
                            <input type="text" class="form-control" id="occupants_name"
                                   v-model="formData.occupants_name">
                        </div>
                        <div class="col-6">
                            <label for="po_box" class="form-label">P.O. Box</label>
                            <input type="text" class="form-control" id="po_box" v-model="formData.po_box">
                        </div>
                    </div>
                    <div class="row mt-2">

                        <div class="col-12">
                            <label for="occupants_name" class="form-label">No. of Occupants</label>
                            <div class="row row-cols-auto">
                                <div class="col-4">

                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-adults">Adults</span>
                                        <input type="text" class="form-control" v-model="formData.adults"
                                               aria-label="Sizing example input" aria-describedby="inputGroup-adults">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-children">Children</span>
                                        <input type="text" class="form-control" v-model="formData.children"
                                               aria-label="Sizing example input" aria-describedby="inputGroup-children">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-children_age">Children Age</span>
                                        <input type="text" class="form-control" v-model="formData.children_age"
                                               aria-label="Sizing example input"
                                               aria-describedby="inputGroup-children_age">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-2">

                            <label for="adults" class="form-label">Marital Status</label>

                            <input type="text" class="form-control" name="marital_status"
                                   v-model="formData.marital_status">

                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="tenant" class="form-label"> Tenant Passport No.</label>
                            <input type="text" class="form-control" id="tenant_passport"
                                   v-model="formData.tenant_passport">
                        </div>
                        <div class="col-6">
                            <label for="visa_type" class="form-label"> Type of Visa</label>
                            <input type="text" class="form-control" id="visa_type" v-model="formData.visa_type">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="dob" class="form-label"> Date of birth</label>
                            <input type="text" class="form-control" id="dob" v-model="formData.dob">
                        </div>
                        <div class="col-6">
                            <label for="visa_no" class="form-label"> Visa No.</label>
                            <input type="text" class="form-control" id="visa_no" v-model="formData.visa_no">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="dob" class="form-label"> Profession</label>
                            <input type="text" class="form-control" id="dob" v-model="formData.profession">
                        </div>
                        <div class="col-6">
                            <label for="nationality" class="form-label"> Nationality</label>
                            <input type="text" class="form-control" id="nationality" v-model="formData.nationality">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <label for="dob" class="form-label"> Office Number</label>
                            <input type="text" class="form-control" id="dob" v-model="formData.office_number">
                        </div>
                        <div class="col-4">
                            <label for="mobile" class="form-label"> Mobile</label>
                            <input type="text" class="form-control" id="mobile" v-model="formData.mobile">
                        </div>
                        <div class="col-4">
                            <label for="fax" class="form-label"> Fax</label>
                            <input type="text" class="form-control" id="fax" v-model="formData.fax">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <label for="email" class="form-label"> Email Address</label>
                            <input type="text" class="form-control" id="email" v-model="formData.email">
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <label for="company_name" class="form-label"> Name of Company</label>
                            <input type="text" class="form-control" id="company_name" v-model="formData.company_name">
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <label for="landlord_name" class="form-label"> Name of Landlord</label>
                            <input type="text" class="form-control" id="landlord_name" v-model="formData.landlord_name">
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="contract_start_date" class="form-label"> Contract Start Date</label>
                            <input type="date" class="form-control" id="contract_start_date" v-model="formData.contract_start_date">
                        </div>
                        <div class="col-6">
                            <label for="lease_term" class="form-label"> Lease Term</label>
                            <input type="text" class="form-control" id="lease_term" v-model="formData.lease_term">
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="annual_rent" class="form-label"> Annual Rent</label>
                            <input type="text" class="form-control" id="annual_rent" v-model="formData.annual_rent">
                        </div>
                        <div class="col-6">
                            <label for="cheques" class="form-label"> No. Of Cheques</label>
                            <input type="text" class="form-control" id="cheques" v-model="formData.cheques">
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="annual_rent" class="form-label"> Security Deposit</label>
                            <input type="text" class="form-control" id="annual_rent" v-model="formData.annual_rent">
                        </div>
                        <div class="col-6">
                            <label for="agency_fee" class="form-label"> Agency Fee + 5% VAT</label>
                            <input type="text" class="form-control" id="cheques" v-model="formData.agency_fee">
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
    },
    creation_type: {
        type: String,
        default: 'new'
    }
});
const lead_id = computed(() => route.query.lead_id);
const meta = ref([]);
const emit = defineEmits(["setDownloadFileUrl"]);

//const docURl = ref(null)
const docURl = ref(`${documentsPath}/Leasing_Reservation_Form.pdf`)

const formData = ref({
    file_id: '',
    filename: '',
    type: "Leasing-Reservation-Form",
    creation_type: props.creation_type,
    property_name: '',
    residential: false,
    commercial: false,
    unit: '',
    unit_types: '',
    unit_ref: '',
    unit_area: '',
    tenant: '',
    individual: false,
    corporate: false,
    government: false,
    occupants_name: '',
    po_box: '',
    adults: '',
    children: '',
    children_age: '',
    marital_status: '',
    tenant_passport: '',
    visa_type: '',
    dob: '',
    visa_no: '',
    profession: '',
    nationality : '',
    office_number: '',
    mobile: '',
    fax: '',
    email: '',
    company_name: '',
    landlord_name: '',
    contract_start_date: '',
    lease_term: '',
    annual_rent: '',
    cheques: '',
    security_deposit: '',
    agency_fee: '',


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
        if (data != null || data != undefined) {
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
    if (file.value !== null) {
        docURl.value = file.value.path;
    } else if (val !== null) {
        docURl.value = val.path;
    }

})

// const findNationality =(id) =>  {
//     const foundCountry = countries.value.find(country => country.id === id);
//     return foundCountry ? foundCountry.nationality : "";
// }
onMounted(() => {
    console.log('Form mounted')

    formData.value.filename = moment(new Date()).format("YYYY-MM-DD H-m-ss");
    // getFile('', 'lead-kyc-form-individual','source')

})
onBeforeMount(() => {
    getCountries();

})
onMounted(() => {
    console.log('Form mounted')
    if (Object.keys(props.file || {}).length !== 0) {
        setFormData(null, props.file)
    }
})

</script>
<style scoped>

</style>
