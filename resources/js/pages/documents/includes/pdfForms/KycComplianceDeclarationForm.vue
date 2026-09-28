<template>
    <div class="row">
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12 text-center">
                    <h4>COMPLIANCE DECLARATION FORM</h4>

                </div>

                <div class="col-12">

                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="we" class="form-label">We</label>
                            <input type="text" class="form-control" id="we"
                                   v-model="formData.we">
                        </div>
                        <div class="col-6">
                            <label for="trade_license" class="form-label">Trade License</label>
                            <input type="text" class="form-control" id="trade_license"
                                   v-model="formData.trade_license">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="reg_no" class="form-label">Registration No.</label>
                            <input type="text" class="form-control" id="reg_no"
                                   v-model="formData.reg_no">
                        </div>

                    </div>
                    <div class="col-12 mt-3">
                        <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                            COMPANY INFORMATION
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="unit" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="unit" v-model="formData.company_name">
                        </div>
                        <div class="col-6">
                            <label for="unit_types" class="form-label">Trade License No.</label>
                            <input type="text" class="form-control" id="unit_types"
                                   v-model="formData.company_trade_license">
                        </div>


                    </div>
                    <hr>
                    <div class="row mt-2">
                        <div class="col-6"><label class="form-label">Share Holder Details</label></div>
                        <div class="col-6"><label class="form-label">Passport No.</label></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-6">

                            <input type="text" class="form-control"  v-model="formData.share_holder1">
                        </div>
                        <div class="col-6">

                            <input type="text" class="form-control"  v-model="formData.share_holder_passport_no1">
                        </div>

                    </div>
                    <div class="row mt-1">
                        <div class="col-6">

                            <input type="text" class="form-control"  v-model="formData.share_holder2">
                        </div>
                        <div class="col-6">

                            <input type="text" class="form-control"  v-model="formData.share_holder_passport_no2">
                        </div>

                    </div>
                    <div class="row mt-1">
                        <div class="col-6">

                            <input type="text" class="form-control"  v-model="formData.share_holder3">
                        </div>
                        <div class="col-6">

                            <input type="text" class="form-control"  v-model="formData.share_holder_passport_no3">
                        </div>

                    </div>


                </div>
                <div class="col-12 mt-3">
                    <div class="bg-dark text-white fs-16 p-2 text-center" style="color: #ffb58d !important;">
                       <div class="row">
                           <div class="col-5 fs-8">Description</div>
                           <div class="col-2">Yes/No</div>
                           <div class="col-5" style="font-size: 12px;">IF ANSWERING "NO" OR "N/A",
                               PLEASE PROVIDE AN EXPLANATION</div>
                       </div>
                    </div>
                </div>
                <div class="row" v-for="(item, index) in descriptions" :key="index">
                    <div class="col-5 fs-8 div-border-1">{{item.description}}</div>
                    <div class="col-2 div-border-1">
                        <input type="radio" :name="'yes_no' + index" value="Yes" v-model="item.yes_no" class="form-check-input" :checked="formData.yes_no === 'Yes'"> Yes
                        <input type="radio" :name="'yes_no' + index" value="No" v-model="item.yes_no" class="form-check-input"> No
                    </div>
                    <div class="col-5 div-border-1">

                        <textarea class="form-control" style="width:100%;height:100%;" v-model="item.explanation"></textarea>
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
const docURl = ref(`${documentsPath}/KYC_Compliance_Declaration_Form.pdf`)


const descriptions = ref([
    {
        "description": "Does the firm have documented AML/CFT and Sanction \n" +
            "screening policies and procedures approved by Board or \n" +
            "Senior committee in place?",
        "yes_no": "No",
        "explanation": ""
    },
    {
        "description": "Are the procedures comprehensive enough to prevent, \n" +
            "detect and report suspicious transactions?\n",
        "yes_no": "No",
        "explanation": ""
    },{
        "description": "Do you have procedures in place to identify the Ultimate Beneficial Owner for the transaction?",
        "yes_no": "No",
        "explanation": ""
    }
    ,{
        "description": "Do you have procedures in place to identify Politically Exposed Persons and related Individuals?",
        "yes_no": "No",
        "explanation": ""
    },{
        "description": "Do you have record retention procedures in place to meet the local regulatory requirement of keeping 5 years transactional record?",
        "yes_no": "No",
        "explanation": ""
    },{
        "description": "Has your company or any of the shareholders been involved in any AML/CFT related investigation or court litigation?",
        "yes_no": "No",
        "explanation": ""
    },{
        "description": "Dose your company conduct Client Risk Assessment?",
        "yes_no": "No",
        "explanation": ""
    },{
        "description": "Does the firm have procedures to establish a record for each new customer noting their respective identification documents and 'Know Your Client' information?",
        "yes_no": "No",
        "explanation": ""
    },{
        "description": "Does the firm have a process to review and, where appropriate, update client 'Know Your Client' information?",
        "yes_no": "No",
        "explanation": ""
    },{
        "description": "Do you have systems & procedures in place to screen new clients as per the regulatory requirement and check portfolio against updates to the sanction lists .",
        "yes_no": "No",
        "explanation": ""
    },{
        "description": "Do you have a designated and Independent Money Laundering Reporting Officer?",
        "yes_no": "No",
        "explanation": ""
    },{
        "description": "Do you have a regular AML/CFT/Sanction training program in place for your sales staff and senior leadership team?",
        "yes_no": "No",
        "explanation": ""
    },{
        "description": "Does the firm have an internal audit function or other independent third party that assesses AML policies and practices on a regular basis?",
        "yes_no": "No",
        "explanation": ""
    }
]);
const formData = ref({
    file_id: '',
    filename: '',
    type: "KYC-Compliance-Declaration-Form",
    creation_type: props.creation_type,
    we: '',
    trade_license: '',
    reg_no: '',
    company_name: '',
    company_trade_license: '',
    share_holder1: '',
    share_holder_passport_no1: '',
    share_holder2: '',
    share_holder_passport_no2: '',
    share_holder3: '',
    share_holder_passport_no3: '',
    descriptions : '',
    yes_no: '',
    explanation: ''


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

    //loop through the descriptions and add to the formData

    let yes_no = [];
    let explanation = [];
    for (let i = 0; i < descriptions.value.length; i++) {
        yes_no.push(descriptions.value[i].yes_no)
        explanation.push(descriptions.value[i].explanation);
    }
    formData.value.yes_no = yes_no;
    formData.value.explanation = explanation;

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
.div-border-1{
    border: 1px solid #d4d4d4;
}
</style>
