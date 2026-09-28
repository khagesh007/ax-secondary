<template>
    <div class="modal fade" id="addUpdateFolderModal" tabindex="-1" aria-labelledby="taskModallLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModallLabel">{{ folder.id ? "Update" : "Create" }} Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="modalClose()"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <form @submit.prevent="onSubmit" enctype="multipart/form-data">

                            <div class="row g-3">
                                <input type="hidden" class="form-control" v-model="formData.property_id" name="property_id">
                                <input type="hidden" class="form-control" v-model="formData.lead_id" name="lead_id">
                                <div class="col-3">
                                    Name
                                </div>
                                <div class="col-9">
                                    <input type="text" class="form-control" v-model="formData.name" name="name">
                                </div>
                                <div class="col-3">
                                    Order
                                </div>
                                <div class="col-9">
                                    <input type="number" class="form-control" v-model="formData.folder_order"
                                        name="folder_order">
                                </div>
                                <div class="col-3">
                                    Tags
                                </div>
                                <div class="col-9">
                                    <input v-model="formData.tags" placeholder="Type and press Enter" ref="tagifyInput" />
                                </div>
                                <div class="col-3">
                                    Thumbnail
                                </div>
                                <div class="col-9">
                                    <input type="file" accept="image/*" class="form-control"
                                        @change="uploadThumbnail($event)" id="thumbanil">
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
import Tagify from '@yaireo/tagify';
import "@yaireo/tagify/dist/tagify.css";


const modalLoader = ref(true);
const props = defineProps({
    folder: {
        type: Object,
        required: false
    },
    parentFolder: {
        type: Object,
        required: false
    },
    createOrUpdateFolder: {
        type: Function,
        required: true
    },
});

const tagify = ref(null)
const tagifyInput = ref(null);

const formData = ref({
    id: '',
    property_id: '',
    lead_id: '',
    name: '',
    folder_order: '',
    tags: [],
    thumbnail: '',
    parent_id: props.parentFolder.id ? props.parentFolder.id : (props.folder.parent_id || '')
});

watch(() => props.folder, (folder) => {
    formData.value.id = folder.id || '';
    formData.value.property_id = folder.property_id || '';
    formData.value.lead_id = folder.lead_id || '';
    formData.value.name = folder.name || '';
    formData.value.folder_order = folder.folder_order || '';
    formData.value.tags = folder.tags?.map(tag => tag.name) || [];
    formData.value.thumbnail = '';
    formData.value.parent_id = props.parentFolder.id ? props.parentFolder.id : (folder.parent_id || '');
})

const resetFormData = () => {
    formData.value.id = '';
    formData.value.property_id = '';
    formData.value.lead_id = '';
    formData.value.name = '';
    formData.value.folder_order = '';
    formData.value.thumbnail = '';
    formData.value.parent_id = '';
    formData.value.tags = [];
}

const modalClose = () => {
    modalLoader.value = false;
}

const onSubmit = async () => {
    const res = await props.createOrUpdateFolder(formData.value);

    if (res && res.data && res.data.status === '1') {
        $('#addUpdateFolderModal').modal('hide');
        modalLoader.value = true;
        resetFormData();
    }
}

const uploadThumbnail = (event) => {
    formData.value.thumbnail = event.target.files[0];
}

onMounted(() => {
    tagify.value = new Tagify(tagifyInput.value, {
        callbacks: {
            add: (e) => {
                formData.value.tags.push(e.detail.data.value)
            },
            remove: (e) => {
                const index = formData.value.tags.indexOf(e.detail.data.value);
                if(index !== -1){
                    formData.value.tags.splice(index, 1);
                }
            },
        },
    });
});

</script>
<style scoped>
.link-icon {
    margin-left: 5px;
}
</style>
