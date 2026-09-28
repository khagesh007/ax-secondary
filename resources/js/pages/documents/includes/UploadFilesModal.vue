<template>
    <div class="modal fade" id="uploadFilesModal" tabindex="-1" aria-labelledby="taskModallLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-soft-danger">
                    <h5 class="modal-title" id="uploadPhotosModalLabel">Upload Files</h5>
                    <button type="button" class="btn-close" @click="modalClose()"></button>
                </div>
                <div class="modal-body p-2 text-center">
                    <div class="mt-4 text-center">
                        <div>
                            <form ref="myDropzone" id="my-dropzone" class="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file" multiple />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="mt-4">
                        <div class="hstack gap-2 justify-content-right float-end">
                            <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                data-bs-dismiss="modal" @click="modalClose()"><i
                                    class="ri-close-line me-1 align-middle"></i> Close</a>
                            <button type="submit" class="btn btn-success action-add" @click="uploadAllFiles">Upload
                                Files</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

import 'dropzone/dist/dropzone.css';
import { Dropzone } from 'dropzone';

import useFolders from './../composables/folders';

const { uploadFiles } = useFolders();

const emit = defineEmits(["refreshFilesList", "toggleUploadFilesModal"]);

const myDropzone = ref(null);

const acceptedFiles = ref([]);

const props = defineProps({
    folder: {
        type: Object,
        required: true
    },
});

const modalClose = () => {
    emit("toggleUploadFilesModal", false);
}

const uploadAllFiles = async (event) => {
    const res = await uploadFiles(acceptedFiles.value, props.folder.id);
    if (res && res.data && res.data.status === '1') {
        emit("toggleUploadFilesModal", false);
        emit("refreshFilesList")
    }
}

const modal = ref(null);

onMounted(() => {
    const modalEl = document.getElementById("uploadFilesModal");
    document.body.appendChild(modalEl);

    modal.value = new bootstrap.Modal(modalEl, {
        keyboard: false,
    });

    modal.value.show();

    const dropzoneElement = document.getElementById('my-dropzone');
    myDropzone.value = new Dropzone(dropzoneElement, {
        url: '/',
        autoProcessQueue: false,
        addRemoveLinks: true,
    });

    myDropzone.value.on('addedfile', async (file) => {
        acceptedFiles.value.push(file);
    });

});

onUnmounted(() => {
    if (myDropzone.value) {
        myDropzone.value.destroy();
    }

    modal.value.hide()
});

</script>
<style scoped>
.link-icon {
    margin-left: 5px;
}
</style>
