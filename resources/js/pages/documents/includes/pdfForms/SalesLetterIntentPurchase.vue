<template>
    <div class="row">
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12 text-center">
                    <h4>OFFER TO PURCHASE</h4>

                </div>

                <div class="col-12">

                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="first_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="first_date" v-model="formData.date">
                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                    <div class="row mt-2" >
                        <div class="col-12">
                            <label for="client_name" class="form-label">Property</label>
                            <input type="text" class="form-control" id="client_name" v-model="formData.property">
                        </div>

                    </div>
                    <div class="row mt-2" >

                        <div class="col-12">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text"  class="form-control" id="subject" v-model="formData.subject">
                        </div>
                    </div>
                    <div class="row mt-2" >
                        <div class="col-12">
                            <label for="purchaser" class="form-label">Purchaser</label>
                            <input type="text" class="form-control" id="purchaser" v-model="formData.purchaser">
                        </div>
                    </div>
                    <div class="row mt-2" >
                        <div class="col-12">
                            <label for="offer" class="form-label">Offer</label>
                            <input type="text" class="form-control" id="offer" v-model="formData.offer">
                        </div>

                        <div class="col-12 mt-1">
                            <label for="agency_commission" class="form-label">Agency Commission</label>
                            <input type="text" class="form-control" id="agency_commission" v-model="formData.agency_commission">
                        </div>
                    </div>
                    <div class="row mt-2" >
                        <div class="col-6">
                            <label for="security_deposit" class="form-label">Security Deposit</label>
                            <input type="text" class="form-control" id="security_deposit" v-model="formData.security_deposit">
                        </div>

                        <div class="col-6">
                            <label for="held_by" class="form-label">To be held by</label>
                            <input type="text" class="form-control" id="held_by" v-model="formData.held_by">
                        </div>
                    </div>
                    <div class="row mt-2" >
                        <div class="col-12">
                            <label for="property_consultant" class="form-label">Property Consultant</label>
                            <input type="text" class="form-control" id="property_consultant" v-model="formData.property_consultant">
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
const docURl = ref(`${documentsPath}/Sales_Letter_Of_Intent_Purchase.pdf`)

const formData = ref({
    file_id: '',
    filename: '',
    type: "Sales-Letter-Of-Intent-Purchase",
    creation_type : props.creation_type,
    date: '',
    property: '',
    subject: '',
    purchaser: '',
    offer: '',
    agency_commission: '',
    security_deposit: '',
    held_by: '',
    property_consultant: '',





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

// const findNationality =(id) =>  {
//     const foundCountry = countries.value.find(country => country.id === id);
//     return foundCountry ? foundCountry.nationality : "";
// }
onMounted(() => {


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
