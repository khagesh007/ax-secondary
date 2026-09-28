<template>

    <div class="modal fade" id="leadFormModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">View/Update Lead Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="leadModalClosed()"></button>
                </div>
                <div class="modal-body p-5">
                    <div v-if="!modalLoader">
                        <form @submit.prevent="onSubmit" enctype="multipart/form-data">
                            <div class="row gy-2">
                                <div class="col-lg-9">




                                    <div class="row mt-2">
                                        <div class="col-6" v-for="(item, index) in documentsType" :key="index">

                                            <div class="input-group">
                                                <label>{{ item }}</label>
                                                <input type="file" class="form-control inputs"
                                                       :name="'document_'+index"
                                                       :id="'document_'+index"
                                                       placeholder="Enter first name"
                                                       @change="onFileChange(index,item)"
                                                >
                                                <div class="invalid-feedback"> Please select
                                                    {{ item }} file.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->
                                <div class="col-lg-3">
                                    <div class="row g-2">
                                        <h5 class="mt-3">Actions</h5>


                                        <div class="col-md-12">
                                            <label for="zip" class="form-label">Lead Type*</label>
                                            <select class="form-control select2 inputs" name="type_id"
                                                    v-model="lead.lead_type_id"
                                                    id="type_id">
                                                <option selected disabled value="">Select lead
                                                    type
                                                </option>
                                                <option v-if="leadTypeOptions" v-for="type in leadTypeOptions"
                                                        :value="type.id"
                                                        :key="type.id"
                                                        :selected="type.id === lead.lead_type_id"
                                                >{{ type.name }}
                                                </option>


                                            </select>

                                            <div class="invalid-feedback"> Please choose lead type.</div>
                                        </div>









                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive table-card">
                                <table class="table table-borderless table-nowrap table-centered align-middle mb-0">
                                    <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Dedline</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Assignee</th>
                                    </tr>
                                    </thead><!-- end thead -->
                                    <tbody>
                                    <tr   v-for="(item, index) in documentsType" :key="index">
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input fs-15" type="checkbox" value="" id="checkTask1">
                                                <label class="form-check-label ms-1" for="checkTask1">
                                                    {{index}}
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-muted"><input type="file" class="form-control"></td>
                                        <td><span class="badge badge-soft-success">PENDING</span></td>
                                        <td>
                                            <a href="javascript: void(0);" class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" :title="task?.user?.full_name" :data-bs-original-title="task?.user?.full_name">
                                                <img  alt="" class="rounded-circle avatar-xxs">
                                            </a>
                                        </td>
                                    </tr><!-- end -->

                                    </tbody><!-- end tbody -->
                                </table><!-- end table -->
                            </div>
                        </div>
                    </div>

                            <div class="mt-4">

                                <div class="hstack gap-2 justify-content-right float-end">
                                    <a href="javascript:void(0);" class="btn btn-link link-success fw-medium"
                                       data-bs-dismiss="modal" @click="leadModalClosed()"><i
                                        class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="submit" class="btn btn-success action-add">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div v-else>
                        <!-- Border spinner -->
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import {ref, onMounted, watchEffect, onUnmounted} from 'vue'
import {useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
import axios from "axios";

const emit = defineEmits(['removeLeadModal']);
const modalLoader = ref(true);
import useGlobalHelper from "../../../composables/helpers/global";

const {getLeadTypes, leadTypes, updateLead} = useDeals();
const {humanReadableString} = useGlobalHelper();
const {leadResources} = useDealStore();
const leadTypeOptions = ref([]);

const props = defineProps({
    deals: {
        type: Object,
        required: false
    },
    lead: {
        type: Object,
        required: true
    }
});
const documents = ref([]);
const documentsType = ref({
    passport: 'Passport copy',
    visa: 'Visa copy',
});
const selectedDocument = ref({
    other_documents: []
});
const formData = ref({
    first_name: props.lead?.first_name,
    last_name: props.lead?.last_name,
    email: props.lead?.email,
    phone: props.lead?.phone,
    mobile: props.lead?.mobile,
    whatsapp: props.lead?.whatsapp,
    gender: props.lead?.gender,
    address: props.lead?.address,
    city: '',
    state: '',
    country: '',
    zip: '',
    date_of_birth: props.lead?.dob,
    place_of_birth: '',
    passport_file: '',
    type_id: '',
    source_id: props.lead?.source_id,
    message: '',
    file: '',
    lead_id: props.lead?.id,
    lead_type_id: '',
    lead_source_id: '',
    lead_medium_id: '',
    lead_status_id: '',
    lead_status: '',
    lead_response_time: '',
    unsuccessfull_reason: '',


});

const onFileChange = (name, title) => {
    let file = document.getElementById('document_' + name).files[0];
    formData.value[name] = URL.createObjectURL(file);

    // formData.value['other_documents'].push({
    //     name: name,
    //     title: title,
    //     file: file
    // });

    selectedDocument.value['other_documents'].push({
        name: name,
        title: title,
        file: file
    });


}


watchEffect(() => {
    leadTypeOptions.value = leadResources.leadTypes;
    if (props.lead) {
        setTimeout(() => {
            modalLoader.value = false;
            $('#leadFormModal').modal('show');
        }, 2000);
    } else {
        modalLoader.value = true;
    }

})
const leadModalClosed = () => {
    emit('removeLeadModal');
    modalLoader.value = false;
}
const onSubmit = () => {
    let formData1 = new FormData();
    // for (let [key, value] of Object.entries(formData.value)) {
    //     formData1.append(key, value);
    // }

    for (let [key, value] of Object.entries(props.lead)) {
        formData1.append(key, value ? value : '');
    }
    selectedDocument.value['other_documents'].forEach((item, index) => {
        formData1.append('other_documents[' + index + '][file]', item.file);
        formData1.append('other_documents[' + index + '][title]', item.title);
    });

    updateLead(formData1);
}
onMounted(() => {
    if (props.lead) {
        $('#leadFormModal').modal('show');
        setTimeout(() => {
            modalLoader.value = false;
        }, 1000);
    }
    if (!leadResources.leadTypes) {
        getLeadTypes();
        leadResources.leadTypes = leadTypes;
    }

});

onUnmounted(() => {
    $('#leadFormModal').modal('hide');
    modalLoader.value = true;
});

</script>
<style scoped>
.modal {
    position: fixed !important;

}
</style>
