<template>
    <div class="row">
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12 text-center">
                    <h4>Key Handover Form</h4>

                </div>

                <div class="col-12">

                    <div class="row mt-2">
                        <div class="col-6">
                            <label for="first_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="first_date" v-model="formData.date">
                        </div>
                        <div class="col-6">
                            <label for="first_time" class="form-label float-end">Time</label>
                            <input type="time"  class="form-control" id="first_time" v-model="formData.time">
                        </div>
                    </div>
                    <div class="row mt-2" >
                        <div class="col-12">
                            <label for="property_details" class="form-label">Property Details</label>
                            <input type="text" class="form-control" id="property_details" v-model="formData.property_details">
                        </div>

                    </div>
                    <div class="row mt-2" >
                        <div class="col-6">
                            <label for="main_door" class="form-label">Main Door</label>
                            <input type="text" class="form-control" id="main_door" v-model="formData.main_door">
                        </div>
                        <div class="col-6">
                            <label for="parking" class="form-label float-end">Parking</label>
                            <input type="text"  class="form-control" id="parking" v-model="formData.parking">
                        </div>
                    </div>
                    <div class="row mt-2" >
                        <div class="col-6">
                            <label for="main_door" class="form-label">Main Door Keys</label>
                            <input type="text" class="form-control" id="main_door" v-model="formData.main_door_keys">
                        </div>
                        <div class="col-6">
                            <label for="access_card" class="form-label float-end">Access Card</label>
                            <input type="text"  class="form-control" id="access_card" v-model="formData.access_card">
                        </div>
                    </div>

                    <div class="row mt-2" >
                        <div class="col-6">
                            <label for="main_door" class="form-label">Main Door Access Card</label>
                            <input type="text" class="form-control" id="main_door" v-model="formData.main_door_access_card">
                        </div>
                        <div class="col-6">
                            <label for="transporter" class="form-label float-end">Transporter</label>
                            <input type="text"  class="form-control" id="transporter" v-model="formData.transporter">
                        </div>
                    </div>

                    <div class="row mt-2" >
                        <div class="col-6">
                            <label for="main_door_remote_control" class="form-label">Main Door Remote Control</label>
                            <input type="text" class="form-control" id="main_door_remote_control" v-model="formData.main_door_remote_control">
                        </div>
                        <div class="col-6">
                            <label for="remote_control" class="form-label float-end">Remote Control</label>
                            <input type="text" class="form-control" id="remote_control" v-model="formData.remote_control">
                        </div>
                    </div>
                    <div class="row mt-2" >
                        <div class="col-6">
                            <label for="others" class="form-label">Others</label>
                            <input type="text" class="form-control" id="others" v-model="formData.others">
                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                    <div class="row mt-2" >
                        <div class="col-12">
                            <label for="long_text" class="form-label">Others</label>
                           <textarea class="form-control" id="long_text" rows="5" v-model="formData.long_text"></textarea>
                        </div>

                    </div>
                    <div class="row mt-2" >
                        <div class="col-12">
                            I,  <input type="text" class="form-control" id="others" v-model="formData.user_name">, take full responsibility for the key/s and/
                            or access card/s and/or remote control/s given to me by the Landlord, Mr./Ms.
                            <input type="text" class="form-control" id="others" v-model="formData.user_relative_name"> as stated on this form. If I am unable to return
                            any of the items in working order or otherwise, then I agree to pay for the item/s as
                            per the charges mentioned above.
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
const docURl = ref(`${documentsPath}/Key_Handover_Form.pdf`)

const formData = ref({
    file_id: '',
    filename: '',
    type: "key-handover-form",
    date: '',
    time: '',
    property_details: '',
    main_door: '',
    parking: '',
    main_door_keys: '',
    access_card: '',
    main_door_access_card: '',
    transporter: '',
    main_door_remote_control: '',
    remote_control: '',
    others: '',
    long_text: '',
    user_name: '',
    user_relative_name: '',



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

const findNationality =(id) =>  {
    const foundCountry = countries.value.find(country => country.id === id);
    return foundCountry ? foundCountry.nationality : "";
}
onMounted(() => {


    formData.value.filename = moment(new Date()).format("YYYY-MM-DD H-m-ss");
    // getFile('', 'lead-kyc-form-individual','source')

})
onBeforeMount(() => {
    getCountries();

})
onMounted(() => {
    console.log('form a mounted')

    if (Object.keys(props.file || {}).length !== 0) {

        setFormData(null, props.file)
    }
})

</script>
<style scoped>

</style>
