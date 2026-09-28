<template>
    <router-link :to="{ name: 'documents-view', params: { slug: folder.slug } }" class="col-xxl-2 col-3 folder-card" :title="folder?.name">
        <div class="card bg-light shadow-none" :id="`folder_${folder.id}`">
            <div class="card-body">
                <div class="d-flex mb-1">
                    <div class="form-check form-check-danger mb-3 fs-15 flex-grow-1">

                    </div>
                    <div class="dropdown">
                        <button class="btn btn-ghost-primary btn-icon btn-sm dropdown" type="button"
                            data-bs-toggle="dropdown" :aria-expanded="false">
                            <i class="ri-more-2-fill fs-16 align-bottom"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="javascript:void(0);" @click.stop="openUpdateModal">Edit</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);" @click.stop="downloadFolder">Download</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);" @click.stop="removeFolder">Delete</a></li>
                        </ul>
                    </div>
                </div>

                <div class="text-center">
                    <div class="mb-2">
                        <i class="ri-folder-2-fill align-bottom text-warning display-5"></i>
                    </div>
                    <h6 class="fs-15 folder-name">{{ _truncate(folder?.name, { length: 20 }) }}</h6>
                </div>
                <div class="hstack mt-4 text-muted">
                    <span class="me-auto"><b>{{ folder?.total_no_files || 0 }}</b> Files</span>
                    <!-- <span><b>4.10</b>GB</span> -->
                </div>
            </div>
        </div>
    </router-link>
</template>
<script setup>

import useFolders from "../composables/folders";
import _truncate from 'lodash/truncate';

const { downloadFolderZip } = useFolders();

const emit = defineEmits(['showUpdateModal']);
const props = defineProps({
    folder: {
        type: Object,
        required: true
    },
    removeFolder: {
        type: Function,
        required: true
    }
})

const openUpdateModal = () => {
    emit('showUpdateModal', props.folder);
}

const downloadFolder = async () => {
    const res = await downloadFolderZip(props.folder.id);
    if (res && res.data && res.data.status === '1') {
        if (res.data.downloadUrl) {
            window.open(res.data.downloadUrl, "_BLANK")
        }
    }
}

const removeFolder = () => {
    Swal.fire({
        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to remove this folder, removing this folder will also remove all sub folders and files.?</p></div></div>',

        showCancelButton: true,
        cancelButtonText: 'Cancel',
        cancelButtonClass: 'btn btn-warning action-close w-xs',

        showCloseButton: true,
        confirmButtonText: 'Delete',
        confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

        buttonsStyling: false,

    }).then(function (result) {
        if (result.isConfirmed) {
            deleteFolder()
        }
    })
}

const deleteFolder = async () => {
    const res = await props.removeFolder(props.folder.id);
}


</script>
<style scoped>
.link-icon {
    margin-left: 5px;
}
</style>
