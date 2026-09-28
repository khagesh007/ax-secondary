<template>
    <section class="h-100 h-custom bg-light min-h-content mt-2">


            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col d-flex justify-content-center align-items-center">

                    <div class="card border-0 " style="width:100%">
                        <div class="card-body">

                            <div ref="dropRef" class="dropzone custom-dropzone"></div>
                            <div class="dropzone preview-container d-none"></div>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="onSubmit" enctype="multipart/form-data">
                                <div class="row mt-2" v-for="(item, index) in uploadedMedia">
                                    <div class="col-2">

                                        <img v-if="item.extension == null" src="/assets/icons/documents.png" class="avatar-sm rounded"
                                             alt=".">
                                      <img v-else-if="item.extension =='pdf'" src="/assets/icons/pdf-file.png" class="avatar-sm rounded"
                                             alt=".">
                                        <img  v-else-if="item.path !=null && (item.extension =='png' || item.extension =='jpg' || item.extension =='jpeg')" :src="item.path" class="avatar-sm rounded" alt=".">
                                    </div>
                                    <div class="col-4">
                                      {{item.description}}
<!--                                        <VueMultiselect-->

<!--                                            v-model="item.model"-->
<!--                                            :allowEmpty="true"-->
<!--                                            :searchable="true"-->
<!--                                            :close-on-select="true"-->
<!--                                            :show-label="true"-->
<!--                                            label="name"-->
<!--                                            :options="documentModel"-->
<!--                                            :multiple="false"-->
<!--                                            :selectLabel="''"-->
<!--                                            track-by="name"-->
<!--                                        ></VueMultiselect>-->
                                    </div>
                                    <div class="col-5">
                                        <VueMultiselect
                                            v-model="item.title_obj"
                                            :id="index"
                                            :allowEmpty="true"
                                            :searchable="true"
                                            :close-on-select="true"
                                            :show-label="true"
                                            label="name"
                                            :options="documentList"
                                            :multiple="false"
                                            :taggable="true"
                                            :selectLabel="''"
                                            track-by="name"
                                            @Select="dynamicDocTypeSelected($event,item)"
                                            @tag="addDynamicDocType($event,item)"
                                        >
                                        </VueMultiselect>
                                    </div>
                                    <div class="col-1">
                                        <a href="" @click.prevent="removeMedia(index,item)">
                                            <i class="ri ri-delete-bin-2-fill"
                                               style="font-size: 1.5rem; color: #c48667;"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary float-end ml-2">Save</button>
                                        <button type="button" class="btn btn-light float-end wave-effect"
                                                @click="closeUploader">Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

    </section>
</template>
<script setup>
import {ref, onMounted, defineComponent} from 'vue'
import {useDealStore} from "../store/dealState";

const deal_state = useDealStore();
import {Dropzone} from 'dropzone'
import VueMultiselect from "vue-multiselect";
import 'vue-multiselect/dist/vue-multiselect.css';
import useDeals from "../composables/deals";
import CardPlaceholder from "../../../utilities/placeholder/CardPlaceholder.vue";

const {updateDealDocuments, deleteDealMedia} = useDeals();
const emit = defineEmits(['fileDataUpdated', 'closeUploader']);
const props = defineProps({
    documentsFor: {
        type: String,
        required: true
    },
    deal: {
        type: Object,
        required: true
    },
    lead: {
        type: Object,
        required: false
    },
    contact: {
        type: Object,
        required: false
    }

})
const documentList = ref([]);
const uploadedMedia = ref([]);
const placeholder = ref(true);
//getting the div container
const dropRef = ref(null)
//creating html custom preview for uploading files
const customPreview = `
        <div class="d-flex flex-wrap dz-preview dz-processing dz-image-preview dz-complete">
          <div class="dz-image">
            <img data-dz-thumbnail>
          </div>
          <div class="dz-details">
            <div class="dz-size"><span data-dz-size></span></div>
              <div class="dz-filename"><span data-dz-name></span></div>
            </div>
            <div class="dz-progress">
              <span class="dz-upload" data-dz-uploadprogress></span>
            </div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
            <div class="dz-success-mark">
                <i class="bi bi-check-circle-fill" style="font-size: 2rem; color: green;"></i>
            </div>
            <div class="dz-error-mark">
                <i class="bi bi-exclamation-circle-fill" style="font-size: 2rem; color: red;"></i>
          </div>
        </div>
      `;

const formData = ref({
    deal_id: props.deal.id,
    id : null,
    model: null,
})
const rental_documents = [
    {name: 'Signed Tenancy Contract', id: 'Signed Tenancy Contract'},
    {name: 'Passport, Visa or EID of Landlord', id: 'Passport, Visa or EID of Landlord'},
    {name: 'Passport, Visa or EID of Tenant', id: 'Passport, Visa or EID of Tenant'},
    {name: 'Title Deed / Oqood / SPA', id: 'Title Deed / Oqood / SPA'},
    {name: 'Trade License if Tenant is a Company', id: 'Trade License if Tenant is a Company\n'},
    {
        name: 'Passport, Visa or EID of the authorized signatory if Tenant is a Company',
        id: 'Passport, Visa or EID of the authorized signatory if Tenant is a Company'
    },
    {name: 'Filled KYC Form', id: 'Filled KYC Form'},
];
const primary_sale_documents = [
    {name: 'Buyers PP Copy', id: 'Buyers PP Copy'},
    {name: 'KYC Form', id: 'KYC Form'},
    {name: 'Booking / Reservation Form', id: 'Booking / Reservation Form'},

];
const secondary_sale_documents = [
    {name: 'Signed Manual MOU or Form F with password', id: 'Signed Manual MOU or Form F with password'},
    {name: 'Form A and B with password', id: 'Form A and B with password'},
    {name: 'Passport, Visa or EID Seller', id: 'Passport, Visa or EID Seller'},
    {name: 'Passport, Visa or EID Buyer', id: 'Passport, Visa or EID Buyer'},
    {name: 'Title Deed / Oqood / SPA', id: 'Title Deed / Oqood / SPA'},
    {name: 'Trade License if Buyer is a Company', id: 'Trade License if Buyer is a Company'},
    {
        name: 'Passport, Visa or EID of the authorized signatory if Buyer is a Company',
        id: 'Passport, Visa or EID of the authorized signatory if Buyer is a Company'
    },
    {name: 'Filled KYC Form', id: 'Filled KYC Form'},
];
const documentModel = [
    {name: 'This deal', model: 'deal', id: props.deal.id},
    {name: 'Owner/Seller', model: 'owner', id: deal_state.data.contact_id}
];
const addDynamicDocType = (newTag, target) => {
    const tag = {
        name: newTag,
        id: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
    }
    documentList.value.push(tag)
    target.title = newTag;
    target.title_obj = tag;
}
const dynamicDocTypeSelected = (option, target) => {
    target.title_obj = option;
    target.title = option.name;
}
const removeMedia = (index, media) => {
    uploadedMedia.value.splice(index, 1);
    let formData3 = new FormData();
    formData3.append('id', media.id);
    formData3.append('model', formData.value.model);
    formData3.append('deal_id', props.deal.id);
    formData3.append('model_id', formData.value.id);
    deleteDealMedia(formData3);
}
const onSubmit = async () => {
    let formData1 = new FormData();
    formData1.append('deal_id', props.deal.id);
    formData1.append('model', formData.value.model);
    formData1.append('id', formData.value.id);
   formData1.append('documents', JSON.stringify(uploadedMedia.value));

   // await updateDealDocuments(uploadedMedia.value, props.deal.id);
    await updateDealDocuments(formData1);
    emit('fileDataUpdated');
}
const closeUploader = () => {
    emit('closeUploader');
}
const dropzoneIntitializer = () => {
    if (dropRef.value !== null) {
        new Dropzone(dropRef.value, {
            url: '/api/vue/v1/add-deal-doc',//backend url
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
            },
            paramName: "file", // input name,
            init: function () {
                this.on("sending", function (file, xhr, formData2) {
                    formData2.append("deal_id", props.deal.id);
                    formData2.append("model", formData.value.model);
                    formData2.append("id", formData.value.id);
                });
            },
            maxFilesize: 10, // MB
            // maxFiles: 10,
            addRemoveLinks: true,
            autoProcessQueue: true,

            acceptedFiles: ".jpg,.png,.jpeg,.webp,.pdf,.doc,.docx,.zip",// accepted files
            previewTemplate: customPreview,
            previewsContainer: dropRef.value.parentElement.querySelector('.preview-container'),
            success: function (file, response) {

                if (response.status == '1') {
                    console.log(response.data)
                    uploadedMedia.value.push(response.data)
                }
            },

        })
        // customizing the input field of dropzone
        if (dropRef.value.querySelector('.dz-default')) {
            dropRef.value.querySelector('.dz-default').innerHTML = `
              <div style="display: flex; justify-content: center;">
              <i class="ri ri-upload-cloud-line" style="font-size: 5rem;color: #adadad;"></i>

              </div>
              <p style="text-align: center; margin: 0;color: #6f6f6f;"><strong>Drag and drop files to upload</strong></p>
            `
        }
    }
}
onMounted(() => {

    if (props.deal.type == 'Rental') {
        documentList.value.push(...rental_documents)
    } else if (props.deal.type == 'Primary Sale') {
        documentList.push(...primary_sale_documents)
    } else if (props.deal.type == 'Secondary Sale') {
        documentList.value.push(...secondary_sale_documents)
    }
    if(props.documentsFor == 'contact'){
        formData.value.model = 'contact';
        formData.value.id = props.contact.id;
    }else  if(props.documentsFor == 'lead'){
        formData.value.model = 'lead';
        formData.value.id = props.lead.id;
    }else {
        formData.value.model = 'deal';
        formData.value.id = props.deal.id;
    }

    dropzoneIntitializer();
    // if (props.leads) {
    //     for (let i = 0; i < props.leads.length; i++) {
    //         documentModel.push({name: props.leads[i].name, model: 'lead', id: props.leads[i].id})
    //     }
    //
    // }


})
</script>

<style scoped>
.custom-dropzone {
    border-style: dashed;
    border-width: 1px;
    border-color: rgb(221, 221, 221);
    padding: 20px;
    color: rgb(114, 114, 114)
}

</style>
