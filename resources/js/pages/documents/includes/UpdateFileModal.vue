<template>
    <div class="modal fade" id="updateFileModal" tabindex="-1" aria-labelledby="taskModallLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateFileModallLabel">Update File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="modalClose()"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <form @submit.prevent="onSubmit" enctype="multipart/form-data">

                            <div class="row g-3">


                                <div class="col-12">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" v-model="formData.title" name="title">
                                    <small class="text-warning">(<i>Please keep the extension ex. .jpg,.png,.pdf</i>)</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" v-model="formData.description" name="description" rows="5"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label"> Slug
                                    <a href="javascript:void(0);" class="badge bg-warning" @click="toggleFileSlugInput">
                                        <i class="ri ri-edit-2-fill"></i>
                                    </a>
                                    </label>

                                    <input type="text" id="file_slug_input" class="form-control" v-model="formData.slug"
                                           name="slug" readonly>
                                    <small class="text-danger">(<i>[CRITICAL] Please ignore if you don't understand</i>)</small>
                                </div>


                            </div>

                            <div class="mt-4">

                                <div class="hstack gap-2 justify-content-right float-end">
                                    <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                       data-bs-dismiss="modal" @click="modalClose()"><i
                                        class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="submit" class="btn btn-success action-add">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref, watch } from 'vue';
const emit = defineEmits(["fileUpdated"]);
const modal = ref(null);
const props = defineProps({
    file: {
        type: Object,
        required: true
    }
});
const formData = ref({
    id: props.file.id,
    title: props.file.title,
    slug: props.file.slug,
    description: props.file.description,
})
const modalClose = () => {
    modal.value.hide();
    formData.value = {
        id: '',
        title: '',
        slug: '',
        description: '',
    }
}
const toggleFileSlugInput = () => {
    document.getElementById('file_slug_input').readOnly = document.getElementById('file_slug_input').readOnly === true ? false : true;

}
const onSubmit = async () => {
   await axios.post(route('admin.api.documents.update.file'), formData.value)
        .then(response => {
            if (response.data.status === '1') {
                showToastify('File Updated Successfully', 'success');
                modalClose();
                emit('fileUpdated');
            }else{
                showToastify('File Update Failed', 'error');
            }
        })
        .catch(error => {
            showToastify('File Update Failed', 'error');
            console.log(error);
        });
}
onMounted(() => {
    const modalEl = document.getElementById("updateFileModal");
    document.body.appendChild(modalEl);

    modal.value = new bootstrap.Modal(modalEl, {
        keyboard: false,
    });

    modal.value.show();
   // $('#updateFileModal').show();
    // $('#updateFileModal').on('hidden.bs.modal', function (e) {
    //     formData.value = {
    //         name: '',
    //         folder_order: '',
    //         tags: [],
    //         property_id: '',
    //         lead_id: '',
    //         file_id: '',
    //         file: '',
    //
    //     }
    // })
})
</script>
