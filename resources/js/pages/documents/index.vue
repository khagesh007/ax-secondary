<template>
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
        <div class="file-manager-sidebar">
            <!-- <Sidebar /> -->
        </div>
        <div class="file-manager-content w-100 p-3 py-0">
            <div class="mx-n3 pt-4 px-4 file-manager-content-scroll simplebar-scrollable-y" data-simplebar="init">
                <div class="simplebar-wrapper" style="margin: -24px -24px 0px;">
                    <div class="simplebar-height-auto-observer-wrapper">
                        <div class="simplebar-height-auto-observer"></div>
                    </div>
                    <div class="simplebar-mask">
                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                            <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                                <div class="simplebar-content" style="padding: 24px 24px 0px;">
                                    <div>
                                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                            <h4 class="mb-sm-0">{{ folder.name || "Root" }}</h4>
                                            <div class="page-title-right">
                                                <ol class="breadcrumb m-0">
                                                    <li class="breadcrumb-item">
                                                        <router-link :to="{ name: 'documents' }"><i
                                                                class="ri-home-3-line"></i></router-link>
                                                    </li>
                                                    <li class="breadcrumb-item" v-for="breadcrumb in breadcrumbs">
                                                        <router-link
                                                            :to="{ name: 'documents-view', params: { slug: breadcrumb.slug } }">{{
                                                                breadcrumb.name }}</router-link>
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="folder-list" class="mb-2" style="display: block;">

                                        <div class="row justify-content-beetwen g-2 mb-3">
                                            <div class="col">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-2 d-none d-lg-none">
                                                        <button type="button"
                                                            class="btn btn-soft-success btn-icon btn-sm fs-16 file-menu-btn">
                                                            <i class="ri-menu-2-fill align-bottom"></i>
                                                        </button>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="fs-16 mb-0">Folders</h5>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--end col-->
                                            <div class="col-auto">
                                                <div class="d-flex gap-2 align-items-start">
                                                    <input type="text" class="form-control" placeholder="Search folders" v-model="foldersSearchTerm">
                                                    <button class="btn btn-success w-sm action-save flex-shrink-0"
                                                        @click="showAddUpdateFolderModal()">
                                                        <i class="ri-add-line align-bottom me-1"></i> Create Folder</button>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                        <div class="row" id="folderlist-data">
                                            <FolderCard v-for="fold in foldersList" :folder="fold" :key="fold.id"
                                                @showUpdateModal="showAddUpdateFolderModal" :remove-folder="removeFolder" />
                                            <div class="flex-shrink-0" v-if="foldersListLastPage > foldersListCurrentPage">
                                                <button class="btn btn-success action-save" @click="loadMoreFolders">Load
                                                    more</button>
                                            </div>
                                        </div>
                                        <!--end row-->
                                    </div>
                                    <hr>
<!--                                    <FilesList :folder="folder" :title="folder.name || 'My'" ref="fileListRef"/>-->
                                    <FileList2 :folder="folder" :title="folder.name || 'My'"  ref="fileListRef2"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: 1058px; height: 752px;"></div>
                </div>
                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                </div>
                <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                    <div class="simplebar-scrollbar"
                        style="height: 121px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
                </div>
            </div>
        </div>
        <AddUpdateFolderModal :folder="selectedFolder" :parentFolder="folder"
            :create-or-update-folder="createOrUpdateFolder" />
    </div>
</template>
<script setup>
import useFolders from './composables/folders';
import Sidebar from './includes/Sidebar.vue';
import FolderCard from './includes/FolderCard.vue';
import FilesList from './includes/FilesList.vue';
import AddUpdateFolderModal from './includes/AddUpdateFolderModal.vue';
import { onMounted, provide, computed, watch, ref } from "vue";
import { useRoute } from 'vue-router';
import FileList2 from "./includes/FileList2.vue";

const {
    getFoldersList,
    storeFolder,
    updateFolder,
    deleteFolder,
    folder,
    selectedFolder,
    foldersPaginationData,
    foldersList,
    foldersListCurrentPage,
    foldersListLastPage,
    getFolder,

    breadcrumbs
} = useFolders();

provide('selectedFolder', selectedFolder)
provide('foldersPaginationData', foldersPaginationData)
provide('foldersList', foldersList)
provide('foldersListCurrentPage', foldersListCurrentPage)
provide('foldersListLastPage', foldersListLastPage)
provide('folder', folder)

provide('breadcrumbs', breadcrumbs)

const fileListRef = ref(null);

const route = useRoute()

const slug = computed(() => route.params.slug);

const foldersSearchTerm = ref("");

watch(slug, (slug) => {
    fetchFolderData();
})


watch(foldersSearchTerm, (foldersSearchTerm) => {
    let rootFoldersOnly = false;

    if(foldersSearchTerm === ""){
        rootFoldersOnly = (folder.value.id === undefined);
    }
    getFoldersList(1, folder.value.id, true, rootFoldersOnly, foldersSearchTerm);
})


const fetchFolderData = async () => {
    await getFolder(slug.value);
    await getFoldersList(1, folder.value.id, true, folder.value.id === undefined, foldersSearchTerm.value);
    await refreshFilesList();
}


const showAddUpdateFolderModal = (selected = {}) => {
    selectedFolder.value = selected;
    $('#addUpdateFolderModal').modal('show');
}

const refreshFilesList = async () => {
    if (fileListRef.value && fileListRef.value.applyFilter) {
        await fileListRef.value.applyFilter();
    } else {
        console.error('applyFilter method not found');
    }
}


const loadMoreFolders = () => {
    let rootFoldersOnly = false;

    if(foldersSearchTerm.value === ""){
        rootFoldersOnly = (folder.value.id === undefined);
    }

    getFoldersList(foldersListCurrentPage.value + 1, folder.value.id, false, rootFoldersOnly, foldersSearchTerm.value);
}

const createOrUpdateFolder = async (data) => {
    try {
        const res = await data.id ? updateFolder(data) : storeFolder(data);
        return res; // Return the entire response object
    } catch (err) {
        console.error("Error in createFolder:", err);
        throw err;
    }
};

const removeFolder = async (id) => {
    try {
        const res = await deleteFolder(id);
        return res; // Return the entire response object
    } catch (err) {
        console.error("Error in createFolder:", err);
        throw err;
    }
};

onMounted(() => {

    getFoldersList();
})

</script>
