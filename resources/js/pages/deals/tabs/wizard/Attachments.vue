<template>
  <div class="tab-pane" id="nav-attachments" role="tabpanel">


    <div class="card" v-if="contact">
      <div class="card-header">
          <button data-v-873ea108="" type="button" class="btn btn-sm btn-white btn-label right rounded-pill border-danger text-danger"><i data-v-873ea108="" class="ri-question-line label-icon align-middle rounded-pill fs-16 ms-2 text-danger" data-bs-toggle="popover" data-bs-html="true" data-bs-content="
        <ul>
          <li>Allowed file type: pdf</li>
          <li>Allowed file size: 3MB</li>
          <li>Required: Yes</li>
        </ul>
      " data-bs-placement="top" data-bs-trigger="hover"></i> Passport, Visa or EID of Tenant</button>
        <button class="btn btn-sm btn-soft-success waves-effect waves-light float-end" @click="fileUploaderFor = 'contact'">
          <i class="bx bx-cloud-upload"></i>
          Upload
        </button>
        <h5>
          <i class=" ri-shield-user-line" style="color: #c48667; vertical-align: bottom;"></i>
          {{ deal_state.data.type == 'Rental' ? 'Owner' : 'Seller' }} ({{ contact.first_name }}
          {{ contact.last_name }})</h5>

      </div>


      <div class="card-body">
        <div class="">
            <button type="button" class="btn btn-sm btn-white btn-label right rounded-pill" :class="{'border-danger text-danger': requiredDocument.required, 'border-gray-new text-dark': !requiredDocument.required}" v-for="requiredDocument in requiredDocuments">
                <i
                    class="ri-question-line label-icon align-middle rounded-pill fs-16 ms-2"
                    :class="{'text-danger': requiredDocument.required, 'text-dark': !requiredDocument.required}"
                    data-bs-toggle="popover"
                    data-bs-html="true"
                    :data-bs-content="getPopoverContent(requiredDocument)"
                    data-bs-placement="top"
                    data-bs-trigger="hover"
                ></i>
                {{ requiredDocument.name }}
            </button>
            <button type="button" class="btn btn-sm btn-white border-success text-success btn-label right rounded-pill">
                <i class="ri-check-double-line label-icon align-middle rounded-pill bg-succes text-success fs-16 ms-2"></i>
                Uploaded
            </button>
        </div>

        <component @closeUploader="fileUploaderFor = null"
                   :is="fileUploaderFor == 'contact' ? DealDocUploader : null"
                   :deal="deal"
                   :lead="{}" :contact="contact" :documentsFor="'contact'"
                   @fileDataUpdated="actionOnFileUpdate"
        ></component>

        <div class="row">
          <div class="col-12" v-if="contact.documents !=null" v-for="(doc, index2) in contact.documents"
               :key="index2">
            <div class="row mb-1 border-bottom">
              <div class="col-8">
                <div class="d-flex align-items-center">
                  <div class="avatar-sm bg-light rounded p-1 me-2">

                    <img v-if="doc.extension == 'jpg' || doc.extension == 'png' || doc.extension == 'jpeg'"  @click="viewFileModal(doc)"
                         :src="doc.path" alt=""
                         class="img-fluid d-block">
                      <img v-if="doc.extension == 'pdf'"  @click="viewFileModal(doc)"
                           src="/assets/icons/pdf-file.png" alt=""
                           class="img-fluid d-block">


                  </div>
                  <div>
                    <h5 class="fs-14 my-1"><a @click="viewFileModal(doc)" href="javascript:void(0)" class="text-reset">
                      {{ doc.title }}</a></h5>

                  </div>
                </div>
              </div>
              <div class="col-3">
                <div class="d-flex align-items-center">

                  <div>
                    <span class="fs-14 my-1"><a :href="doc.path" target="_blank"
                                                class="text-reset">{{ doc?.created_by?.full_name }}</a></span>
                    <br>
                    <span class="text-muted fs-10">{{ formateDateTime(doc.created_at) }}</span>
                  </div>
                </div>
              </div>
              <div class="col-1">

                <button type="button" class="btn btn-soft-info waves-effect waves-light btn-sm float-end">
                  <i class="bx bx-pencil"></i>
                </button>
                <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm float-end"
                        @click="deleteContactMedia(doc.id)">
                  <i class="bx bx-trash"></i>
                </button>

              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <div class="mb-2" v-if="leads && deal_state.data.type != 'Rental'" v-for="(lead, index) in leads" :key="index">
      <div class="card">
        <div class="card-header">
          <button class="btn btn-primary btn-sm action-add float-end" @click="fileUploaderFor = 'lead'+lead.id">
            <i class="bx bx-cloud-upload"></i>
            Upload
          </button>
          <h5>
            <i class="  ri-user-3-line" style="color: #c48667; vertical-align: bottom;"></i>
            {{ deal_state.data.type == 'Rental' ? 'Tenant' : 'Buyer' }} ({{ lead.first_name }}{{ lead.last_name }}
            )</h5>
        </div>
        <div class="card-body">
            <div class="">
                <button type="button" class="btn btn-sm btn-white btn-label right rounded-pill" :class="{'border-danger text-danger': requiredDocument.required, 'border-dark text-dark': !requiredDocument.required}" v-for="requiredDocument in requiredDocuments">
                    <i
                        class="ri-question-line label-icon align-middle rounded-pill fs-16 ms-2"
                        :class="{'text-danger': requiredDocument.required, 'text-dark': !requiredDocument.required}"
                        data-bs-toggle="popover"
                        data-bs-html="true"
                        :data-bs-content="getPopoverContent(requiredDocument)"
                        data-bs-placement="top"
                        data-bs-trigger="hover"
                    ></i>
                    {{ requiredDocument.name }}
                </button>
                <button type="button" class="btn btn-sm btn-white border-success text-success btn-label right rounded-pill">
                    <i class="ri-check-double-line label-icon align-middle rounded-pill bg-succes text-success fs-16 ms-2"></i>
                    Uploaded
                </button>
            </div>
          <div class="row">
            <div class="col-12">
              <component @closeUploader="fileUploaderFor = null"
                         :is="fileUploaderFor == 'lead'+lead.id ? DealDocUploader : null" :deal="deal"
                         :lead="lead" :contact="{}" :documentsFor="'lead'"
                         @fileDataUpdated="actionOnFileUpdate"
              ></component>
            </div>
          </div>
          <div class="row">
            <div class="col-12" v-if="lead.documents !=null" v-for="(doc, index2) in lead.documents" :key="index2">

              <div class="row mb-1 border-bottom">
                <div class="col-8">
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-light rounded p-1 me-2">

                      <img @click="viewFileModal(doc)"
                           src="https://themesbrand.com/velzon/html/default/assets/images/small/img-4.jpg" alt=""
                           class="img-fluid d-block">

                    </div>
                    <div>
                      <h5 class="fs-14 my-1"><a @click="viewFileModal(doc)" href="javascript:void(0)"
                                                class="text-reset">
                        {{ doc.title }}</a></h5>

                    </div>
                  </div>
                </div>
                <div class="col-3">
                  <div class="d-flex align-items-center">

                    <div>
                    <span class="fs-14 my-1"><a :href="doc.path" target="_blank"
                                                class="text-reset">{{ doc?.created_by?.full_name }}</a></span>
                      <br>
                      <span class="text-muted fs-10">{{ formateDateTime(doc.created_at) }}</span>
                    </div>
                  </div>
                </div>
                <div class="col-1">

                  <button type="button" class="btn btn-soft-info waves-effect waves-light btn-sm float-end">
                    <i class="bx bx-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm float-end"
                          @click="deleteLeadMedia(doc.id)">
                    <i class="bx bx-trash"></i>
                  </button>

                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">

        <button class="btn btn-primary btn-sm action-add float-end" @click="showUploadContainer = true">
          <i class="bx bx-cloud-upload"></i>
          Upload
        </button>
        <h5>
          <i class=" ri-folder-reduce-line" style="color: #c48667; vertical-align: bottom;"></i>
          Other Documents</h5>

      </div>
      <div class="card-body">
        <div class="">
            <button type="button" class="btn btn-sm btn-white btn-label right rounded-pill" :class="{'border-danger text-danger': requiredDocument.required, 'border-dark text-dark': !requiredDocument.required}" v-for="requiredDocument in requiredDocuments">
                <i
                    class="ri-question-line label-icon align-middle rounded-pill fs-16 ms-2"
                    :class="{'text-danger': requiredDocument.required, 'text-dark': !requiredDocument.required}"
                    data-bs-toggle="popover"
                    data-bs-html="true"
                    :data-bs-content="getPopoverContent(requiredDocument)"
                    data-bs-placement="top"
                    data-bs-trigger="hover"
                ></i>
                {{ requiredDocument.name }}
            </button>
            <button type="button" class="btn btn-sm btn-white border-success text-success btn-label right rounded-pill">
                <i class="ri-check-double-line label-icon align-middle rounded-pill bg-succes text-success fs-16 ms-2"></i>
                Uploaded
            </button>
        </div>
        <div class="row">
          <div class="col-12">
            <Transition name="fade">

              <DealDocUploader v-if="showUploadContainer"
                               :deal="deal"
                               :leads="leads"
                               @fileDataUpdated="actionOnFileUpdate"
                               @closeUploader="showUploadContainer = false"
                  ></DealDocUploader>
            </Transition>
          </div>
          <div class="col-12" v-if="dealMedia!=null" v-for="(doc, index2) in dealMedia" :key="index2">
            <div class="row mb-1 border-bottom">
              <div class="col-8">
                <div class="d-flex align-items-center">
                  <div class="avatar-sm bg-light rounded p-1 me-2">

                    <img @click="viewFileModal(doc)"
                         src="https://themesbrand.com/velzon/html/default/assets/images/small/img-4.jpg" alt=""
                         class="img-fluid d-block">

                  </div>
                  <div>
                    <h5 class="fs-14 my-1"><a @click="viewFileModal(doc)" href="javascript:void(0)" class="text-reset">
                      {{ doc.title }}</a></h5>

                  </div>
                </div>
              </div>
              <div class="col-3">
                <div class="d-flex align-items-center">

                  <div>
                    <span class="fs-14 my-1"><a :href="doc.path" target="_blank"
                                                class="text-reset">{{ doc.created_by.full_name }}</a></span>
                    <br>
                    <span class="text-muted fs-10">{{ formateDateTime(doc.created_at) }}</span>
                  </div>
                </div>
              </div>
              <div class="col-1">

                <button type="button" class="btn btn-soft-info waves-effect waves-light btn-sm float-end">
                  <i class="bx bx-pencil"></i>
                </button>
                <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm float-end"
                        @click="deleteDealMedias(doc.id)">
                  <i class="bx bx-trash"></i>
                </button>

              </div>
            </div>

          </div>
        </div>
      </div>

    </div>

    <component @toggleViewFileModal="toggleViewFileModal" :is="showingViewFileModal ? FileViewModel : null"
               :file="viewFile">
    </component>
      <div class="actions clearfix mt-5">
          <div class="row">
              <div class="col-8"></div>
              <div class="col-4">
                  <button type="button" @click="nextStepBtn"
                          class="btn btn-success action-save float-end">
                         <span v-if="showBtnLoader" class="d-flex align-items-center">
                                                            <span class="spinner-border flex-shrink-0" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </span>
                                                            <span class="flex-grow-1 ms-2">
                                                                Loading...
                                                            </span>
                                                        </span>
                      <span v-else>Next</span>

                  </button>
              </div>
          </div>

      </div>
  </div>
</template>
<script setup>
import {onMounted, ref, watch, watchEffect} from "vue";
import {useDealStore} from "../../store/dealState";
import useDeals from "../../composables/deals";
import DealDocUploader from "../../includes/DealDocUploader.vue";
import LeadModal from "../../includes/LeadModal.vue";
import FileViewModel from "../../includes/FileViewModel.vue";

const deal_state = useDealStore();
const {leads, getDealLeads, getContact, contact, getDealMedia, dealMedia, deleteDealMedia, deleteMedia, updateDealState} = useDeals();
const showUploadContainer = ref(false);
const deal = ref(null);
const fileUploaderFor = ref(null);
const showingViewFileModal = ref(false);
const viewFile = ref(null);
const showBtnLoader = ref(false);
const emit = defineEmits(['next_step']);
const actionOnFileUpdate = (data) => {
  getDealMedia(deal_state.data.id);
  getDealLeads(deal_state.data.id);
  getContact(deal_state.data.contact_id);
  showUploadContainer.value = false;
}
const viewFileModal = (file) => {
  viewFile.value = file;
  showingViewFileModal.value = true;
}
const toggleViewFileModal = () => {
  showingViewFileModal.value = false;
  viewFile.value = null;
}
// const removeMedia = (index, media) => {
//   uploadedMedia.value.splice(index, 1);
//   let formData3 = new FormData();
//   formData3.append('id', media.id);
//   formData3.append('model', formData.value.model);
//   formData3.append('deal_id', props.deal.id);
//   formData3.append('model_id', formData.value.id);
//   deleteDealMedia(formData3);
// }
const formateDateTime = (date) => {
  return globalDateTime(date);
  //return moment(date).format('YYYY-MM-DD HH:mm:ss');
}
const deleteContactMedia = (id) => {
  Swal.fire({
    html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to delete this documents ?</p></div></div>',

    icon: "warning",
    showCancelButton: true,
    cancelButtonText: 'Cancel',
    cancelButtonClass: 'btn btn-warning action-close w-xs',

    showCloseButton: true,
    confirmButtonText: 'Delete',
    confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

    buttonsStyling: false,

  }).then(function (result) {
    if (result.isConfirmed) {
      deleteMedia(id, 'contact');
      getContact(deal_state.data.contact_id);
    }
  });
}
const deleteDealMedias = (id) => {
  Swal.fire({
    html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to delete this documents ?</p></div></div>',

    icon: "warning",
    showCancelButton: true,
    cancelButtonText: 'Cancel',
    cancelButtonClass: 'btn btn-warning action-close w-xs',

    showCloseButton: true,
    confirmButtonText: 'Delete',
    confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

    buttonsStyling: false,

  }).then(function (result) {
    if (result.isConfirmed) {
      deleteMedia(id, 'deal');
      getDealMedia(deal_state.data.id);
    }
  });
}
const deleteLeadMedia = (id) => {
  Swal.fire({
    html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to delete this documents ?</p></div></div>',

    icon: "warning",
    showCancelButton: true,
    cancelButtonText: 'Cancel',
    cancelButtonClass: 'btn btn-warning action-close w-xs',

    showCloseButton: true,
    confirmButtonText: 'Delete',
    confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

    buttonsStyling: false,

  }).then(function (result) {
    if (result.isConfirmed) {
      deleteMedia(id, 'lead');
      getDealLeads(deal_state.data.id);
    }
  });
}
const nextStepBtn = () => {
    showBtnLoader.value = true;
    updateDealState(deal_state.data.id, 'financial')
    setTimeout(() => {
        showBtnLoader.value = false;
        emit('next_step', 'financial');
    }, 2000);


}
const requiredDocuments = [
    {
        id: "Passport Copy",
        name: "Passport Copy",
        extensions: "pdf",
        size: "3MB",
        required: false
    },
    {
        id: "Visa Copy",
        name: "Visa Copy",
        extensions: "pdf",
        size: "3MB",
        required: false
    },
    {
        id: "Emirate Copy",
        name: "Emirate Copy",
        extensions: "pdf",
        size: "3MB",
        required: false
    },
    {
        id: "Signed Tenancy Contract",
        name: "Signed Tenancy Contract",
        extensions: "pdf",
        size: "3MB",
        required: true
    },
    {
        id: "Passport, Visa or EID of Landlord",
        name: "Passport, Visa or EID of Landlord",
        extensions: "pdf",
        size: "3MB",
        required: true
    },
    {
        id: "Passport, Visa or EID of Tenant",
        name: "Passport, Visa or EID of Tenant",
        extensions: "pdf",
        size: "3MB",
        required: true
    },
    {
        id: "Title Deed / Oqood / SPA",
        name: "Title Deed / Oqood / SPA",
        extensions: "jpg, jpeg, png, pdf",
        size: "3MB",
        required: false
    },
    {
        id: "Passport, Visa or EID of the authorized signatory",
        name: "Passport, Visa or EID of the authorized signatory",
        extensions: "jpg, jpeg, png, pdf",
        size: "3MB",
        required: false
    },
    {
        id: "Filled KYC Form",
        name: "Filled KYC Form",
        extensions: "jpg, jpeg, png, pdf",
        size: "3MB",
        required: false
    },
];

const getPopoverContent = (requiredDocument) => {
      return `
        <ul>
          <li>Allowed file type: ${requiredDocument.extensions}</li>
          <li>Allowed file size: ${requiredDocument.size}</li>
          <li>Required: ${requiredDocument.required ? 'Yes' : 'No'}</li>
        </ul>
      `;
    }
onMounted(() => {
  if (deal_state.data) {
    deal.value = deal_state.data;
    getDealMedia(deal_state.data.id);
    getDealLeads(deal_state.data.id);
    getContact(deal_state.data.contact_id);
  }

  $(document).ready(function(){
    $('[data-bs-toggle="popover"]').popover();
  })
});
watch(() => leads.value, (newLeads) => {
    $(document).ready(function(){
        $('[data-bs-toggle="popover"]').popover();
    })
})
</script>
<style scoped>
.obligation {
  padding: 24px;
  text-align: center;
  border: 1px dashed;
  background: #F9FAFB;
}

.card-price-span {
  display: flex;
  width: 696px;
  padding: 12px 24px;
  justify-content: space-between;
  align-items: flex-start;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.border-gray-new{
    border-color: #ddd7d7!important;
}
.btn-label.right {
    padding-left: .6rem;
    padding-right: 28px;
}

</style>

