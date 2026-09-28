<template>
    <div>
        <div class="d-flex align-items-center mb-3">
            <h5 class="flex-grow-1 fs-16 mb-0" id="filetype-title">{{ title }} Files</h5>
            <div class="flex-shrink-0">
                <button class="btn btn-success action-save" v-if="folder.id" @click="toggleUploadFilesModal"><i
                    class="ri-add-line align-bottom me-1"></i> Upload Files
                </button>
            </div>
        </div>

        <div>
            <div class="table-sticky-header-container" id="table-sticky-header">
                <div class="row">
                    <div class="col-9">
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1 no-border d-inline-flex"
                            role="tablist">
                            <li class="nav-item" role="presentation" v-for="(filterTab, index) in typeFilterTabs">
                                <a @click="applyFilter(filterTab.column, filterTab.value)"
                                   class="nav-link margin-minus-4"
                                   :class="{ 'active': index === 0 }" data-bs-toggle="tab"
                                   :id="`type-filter-${filterTab.value}`"
                                   href="javascript:void(0)" role="tab" :aria-selected="index === 0" tabindex="-1">
                                    {{ filterTab.title }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation" v-for="(filterTab, index) in modelTypeFilterTabs">
                                <a @click="applyFilter(filterTab.column, filterTab.value)"
                                   class="nav-link margin-minus-4"
                                   data-bs-toggle="tab" :id="`model-type-filter-${filterTab.value}`"
                                   href="javascript:void(0)"
                                   role="tab" aria-selected="false" tabindex="-1">
                                    {{ filterTab.title }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-3">
                        <div class="text-right">
                            <div id="dataTable_filter" class="dataTables_filter"><label
                                class="d-flex justify-between-between align-items-center gap-2">Search:<input
                                type="search"
                                v-model="fileSearchKeyword"
                                @keyup="getMyFileList()"
                                class="form-control form-control-sm"
                                placeholder="Search files"
                                aria-controls="dataTable"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table id="dataTable" class="table align-middle" style="width:100%">
                    <thead>
                    <tr class="text-uppercase table-light2">
                        <th scope="col">Title</th>
                        <th scope="col">Type</th>
                        <th scope="col">Category</th>
                        <th scope="col">Size</th>
                        <th scope="col">Created</th>
                        <th scope="col">Updated</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="!fileLists">
                        <td colspan="7" class="text-center">No data available in table</td>
                    </tr>
                    <tr :id="'fileItem'+item.id" :class=" index % 2 === 0 ? 'even' : 'odd'" v-if="fileLists"
                        v-for="(item, index) in fileLists"
                        :key="index">
                        <td>
                            <a href="javascript:void(0)" class="d-flex align-items-center"
                               @click="fetchNViewFile(item.id)"
                               :data-id="item.id">
                                <div class="flex-shrink-0 fs-17 me-2 filelist-icon"><i
                                    :class="selectFileIcon(item.extension)+ ' align-bottom'"></i></div>
                                <div class="flex-grow-1 filelist-name">{{ item.title }}</div>
                            </a>
                        </td>
                        <td>
                            {{ item.type }}
                        </td>
                        <td>
                            -
                        </td>
                        <td>
                            {{ item.formatted_size }}
                        </td>
                        <td>


                            <div v-if="item?.created_by == null" class="flex-grow-1"><p class="mb-0">
                                <small>{{ formatTimeAgo(item.created_at) }}</small></p></div>

                            <div v-else class="d-flex align-items-center" :title="item?.created_by?.first_name">
                                <div class="flex-shrink-0 me-2">
                                    <img :src="item.created_by.thumb_url" alt="" class="avatar-xs rounded-circle">
                                </div>
                                <div class="flex-grow-1">{{ item?.created_by?.first_name }}<p title="Created at"
                                                                                              class="mb-0">
                                    <small>{{ formatTimeAgo(item.created_at) }}</small></p></div>
                            </div>

                        </td>
                        <td>

                            <div v-if="item?.updated_by == null" class="flex-grow-1"><p class="mb-0">
                                <small>{{ formatTimeAgo(item.updated_at) }}</small></p></div>

                            <div v-else class="d-flex align-items-center" :title="item?.updated_by?.name">
                                <div class="flex-shrink-0 me-2">
                                    <img :src="item?.updated_by?.thumb_url" alt="" class="avatar-xs rounded-circle">
                                </div>
                                <div class="flex-grow-1">{{ item?.updated_by?.first_name }}<p title="Created at"
                                                                                              class="mb-0">
                                    <small>{{ formatTimeAgo(item.created_at) }}</small></p></div>
                            </div>

                        </td>
                        <td>
                            <div class="d-flex gap-3 justify-content-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-icon btn-sm dropdown" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                        class="ri-more-fill align-bottom"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end">

                                        <li><a v-if="item.data_editable" class="dropdown-item"
                                               @click="editDataBtnFunc(item)"
                                               title="Edit existing data in this document"
                                               href="javascript:void(0)">Edit Data</a></li>
                                        <li v-if="isDocumentTemplate(item.slug)"><a class="dropdown-item"
                                               href="javascript:void(0)"
                                            title="You can use this template to create a new document"
                                               @click="openCreateDocumentModal(item.slug)"
                                        >Use This Template</a></li>
                                        <li><a class="dropdown-item" target="_blank" :href="item.path"
                                               title="Download this file"
                                               download>Download</a>
                                        </li>

                                        <li>
                                            <div class="btn-group file-action-group mt-2" role="group"
                                                 aria-label="Basic example">
                                                <button type="button" @click="fetchNViewFile(item.id)"
                                                        title="View File"
                                                        class="btn btn-light"><i class="ri-eye-line ax-text"></i>
                                                </button>
                                                <button type="button" @click="fetchNEditFile(item)"
                                                        title="Edit File"
                                                        class="btn btn-light"><i
                                                    class=" ri-edit-box-line ax-text"></i></button>
                                                <button type="button" @click="showRemoveFile(item.id)"
                                                        title="Delete File"
                                                        class="btn btn-light"><i
                                                    class="ri-delete-bin-line ax-text"></i></button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>

                    </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-3">
                        Showing {{ (filePerPage * (currentPage - 1)) + 1 }} to
                        {{ Math.min(filePerPage * currentPage, totalData) }} of {{ totalData }} entries

                    </div>


                    <div class="col-9 d-flex">
                        <div class="flex-grow-1 ">
                            </div>
                        <div class=" align-items-center me-2">
                            <div class="dataTables_length d-flex align-items-center" id="dataTable_length">
                                <label class="me-2 mb-0">Show</label>
                                <select name="dataTable_length" v-model="filePerPage" @change="getMyFileList()"
                                        aria-controls="dataTable" class="form-select form-select-sm mb-0">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <label class="ms-2 mb-0">entries</label>
                            </div>


                        </div>
                        <ul class="pagination flex-shrink-0">
                            <li class="page-item" @click="getMyFileList(first_page_url)">
                                <a href="javascript:void(0)" class="page-link">←</a>
                            </li>


                            <li :class="{ 'page-item': true, 'active': page.active }"
                                v-for="(page, pageIndex) in pageLinks" :key="pageIndex">
                                <a href="javascript:void(0)" class="page-link"
                                   @click="getMyFileList(page.url)">
                                    <span v-if="page.label.includes('Previous')"><i
                                        class="tf-icon mdi mdi-chevron-left"></i></span>
                                    <span v-else-if="page.label.includes('Next')"><i
                                        class="tf-icon mdi mdi-chevron-right"></i></span>
                                    <span v-else>{{ page.label }}</span>

                                </a>
                            </li>

                            <li class="page-item">
                                <a href="javascript:void(0)" @click="getMyFileList(last_page_url)"
                                   class="page-link">→</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <component @toggleViewFileModal="toggleViewFileModal" :is="showingViewFileModal ? ViewFileModal : null"
               :file="file">
    </component>
    <component @toggleGeneratePDFFileModal="toggleGeneratePDFFileModal"
               :is="showingGeneratePDFFileModal ? GeneratePDFFileModal : null" :file="file"
               :propsSourceFile="sourceFile" :fileType="pdfEditFileType"
               :creation_type="queryCreationType"
    >
    </component>

    <component @toggleUploadFilesModal="toggleUploadFilesModal" @refreshFilesList="applyFilter"
               :is="showingUploadFilesModal ? UploadFilesModal : null" :folder="folder">
    </component>
    <component :is="showingUpdateFileModal ? UpdateFileModal : null" :file="file"
               @fileUpdated="fileUpdated"></component>
</template>
<script setup>
import {onMounted, watch, ref, defineExpose, provide, computed} from 'vue';
import useFolders from '../composables/folders';
import ViewFileModal from './ViewFileModal';
import GeneratePDFFileModal from './GeneratePDFFileModal';
import {useRoute} from 'vue-router';
import UploadFilesModal from './UploadFilesModal';
import UpdateFileModal from "./UpdateFileModal.vue";

const sourceFile = ref(null);
const {
    file,
    getFile,
    deleteFile
} = useFolders();

const {property, getProperty} = useFolders();

provide("property", property);
provide("getProperty", getProperty);

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    folder: {
        type: Object,
        required: false
    },
})

const route = useRoute();

const pdfEditFileType = ref("");

const showingViewFileModal = ref(false);

const showingGeneratePDFFileModal = ref(false);
const showingUpdateFileModal = ref(false);

const showingUploadFilesModal = ref(false);

const queryFileSlug = computed(() => route.query.file_slug);
const queryPropertyRefNo = computed(() => route.query.property_ref_no);
const queryCreationType = computed(() => route.query.creation_type) ?? 'new';
const fileLists = ref([]);
const currentPage = ref(1);
const lastPage = ref('');
const totalData = ref('');
const pageLinks = ref([]);
const first_page_url = ref('');
const last_page_url = ref('');
const fileSearchKeyword = ref('');
const filePerPage = ref('10');
const selectedFilterType = ref('');
const selectedFilterValue = ref('');
watch(queryFileSlug, async (queryFileSlug) => {
    console.log('queryFileSlug', queryFileSlug)
    let exclude = [
        "lead-kyc-form-individual",
        "kyc-handover-form",
        "form-a",
        "leasing-letter-of-intent-payables",
        "sales-letter-of-intent-purchase",
        "form-b-sales-contract-buyer-broker",
        "leasing-reservation-form",
        "sales-reservation-form",
        "kyc-compliance-declaration-form",
        "form-f1-property-sales-contract-seller-buyer",
    ];
    //exclude these files from fetching to prevent file_id conflict which was replacing the template file itself, need to improve this in future
    if (exclude.includes(queryFileSlug)) {

// if(queryFileSlug !== 'lead-kyc-form-individual') {
        const res = await getFile(null, queryFileSlug)

        if (res && res.data && res.data.status === '1') {
            //  console.log('file fetched', res.data.data)
            sourceFile.value = res.data.data;
            toggleGeneratePDFFileModal(queryFileSlug, res.data.data);
        }
    } else {
        toggleGeneratePDFFileModal(queryFileSlug, null);
    }
})

watch(queryPropertyRefNo, async (queryPropertyRefNo) => {
    await getProperty(null, queryPropertyRefNo);
})

const toggleViewFileModal = (selectedFile = null, open = true) => {
    showingViewFileModal.value = open;
    file.value = selectedFile;
}

const toggleGeneratePDFFileModal = (fileSlug, selectedFile = null, open = true) => {
    file.value = selectedFile;
    const fileData = selectedFile?.data;
    if (fileData != null) {
        getProperty(fileData.property_id);
    }
    console.log('fileSlug - ', fileSlug)
    if (open) {
        pdfEditFileType.value = fileSlug;
    } else {
        pdfEditFileType.value = "";
    }

    showingGeneratePDFFileModal.value = open
}

const typeFilterTabs = [
    {
        title: "All",
        value: "all",
        column: "type",
    },
    {
        title: "Photos",
        value: "Photos",
        column: "type",
    },
    {
        title: "Documents",
        value: "Document",
        column: "type",
    },
    {
        title: "Other",
        value: "Other",
        column: "type",
    },
];

const modelTypeFilterTabs = [
    {
        title: "Listings",
        value: "Property",
        column: "model_type",
    },
    {
        title: "Deals",
        value: "Deal",
        column: "model_type",
    },
    {
        title: "Leads",
        value: "Lead",
        column: "model_type",
    },
];


const toggleUploadFilesModal = (open = true) => {
    showingUploadFilesModal.value = open;
}


const showEditFileContent = (selectedFile) => {
    let slug = "";
    const {data} = selectedFile;
    if (data !== null) {
        slug = data.type || '';
        // console.log(slug)
        toggleGeneratePDFFileModal(slug, selectedFile)
    }
}

const removeFile = async (id) => {
    try {
        const res = await deleteFile(id);
        if (res && res.data && res.data.status === '1') {
            let selectedFileRow = document.getElementById('fileItem' + id);
            selectedFileRow.style.backgroundColor = "";

            selectedFileRow.style.transition = "background-color 2s ease";
            getMyFileList();
        }
    } catch (err) {
        console.error("Error in deleteFile:", err);
        throw err;
    }
};

const showRemoveFile = (fileId) => {
    let selectedFileRow = document.getElementById('fileItem' + fileId);

    selectedFileRow.style.backgroundColor = "#ff8080";
    Swal.fire({
        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to delete this file it can not be restored ?</p></div></div>',


        showCancelButton: true,
        cancelButtonText: 'Cancel',
        cancelButtonClass: 'btn btn-warning action-close w-xs',

        showCloseButton: true,
        confirmButtonText: 'Delete',
        confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

        buttonsStyling: false,

    }).then(function (result) {
        if (result.isConfirmed) {
            removeFile(fileId)

        } else {
            selectedFileRow.style.backgroundColor = "";

        }
    })
}

const isDocumentTemplate = (slug) => {
    return [
        "key-handover-form",
        "form-a",
        "lead-kyc-form-individual",
        "lead-kyc-form-entity",
        "tenancy-form-dubai",
        "marketing-form-a-leasing-fillable",
        "contract-between-buyer-and-broker-form-b",
        "a2a-agreement-form",
        "sales-letter-of-intent-purchase",
        "leasing-letter-of-intent-payables",
        "sales-reservation-form",
        "leasing-reservation-form",
        "kyc-compliance-declaration-form",
        "form-f1-property-sales-contract-seller-buyer"

    ].includes(slug);
}


const applyFilter = (column, value) => {
    if (value == "all") {
        selectedFilterType.value = '';
        selectedFilterValue.value = '';
        getMyFileList();
        return;
    } else {
        selectedFilterType.value = column;
        selectedFilterValue.value = value;
        getMyFileList();
    }
    //   initDatatable(column, value);
}

defineExpose({
    applyFilter
})

watch(() => props.folder, (folder) => {
    getMyFileList();
})
const fileUpdated = () => {
    getMyFileList();
    showingUpdateFileModal.value = false;
}
const formatTimeAgo = (time) => {
    return moment(time).fromNow()
}


//file list table related functions
const getMyFileList = (url = '/api/documents/get-my-file-list') => {

    let params = new URLSearchParams();
    params.append('keyword', fileSearchKeyword.value);
    params.append('length', filePerPage.value);
    params.append(selectedFilterType.value, selectedFilterValue.value);
    params.append('folder_id', props.folder.id ?? '');
    if (url.includes('?')) {
        url = url + `&${params.toString()}`;
    } else {
        url = url + `?${params.toString()}`;
    }

    axios.get(url)
        .then(response => {

            fileLists.value = response.data.data.data;
            currentPage.value = response.data.data.current_page;
            console.log(currentPage.value)
            lastPage.value = response.data.data.last_page;
            totalData.value = response.data.data.total;
            pageLinks.value = response.data.data.links;
            // next_page_url.value = response.data.data.next_page_url;
            // prev_page_url.value = response.data.data.prev_page_url;
            first_page_url.value = response.data.data.first_page_url;
            last_page_url.value = response.data.data.last_page_url;
        })
        .catch(error => {
            console.log(error);
        });

}
const selectFileIcon = (type) => {
    let iconClass = "";

    if (type == "pdf") {
        iconClass = "ri-file-pdf-fill text-danger";
    } else if (type == "doc" || type == "docx") {
        iconClass = "ri-file-word-fill text-secondary";
    } else if (type == "zip") {
        iconClass = "ri-file-zip-fill text-dark";
    } else {
        iconClass = "ri-file-fill text-secondary";
    }

    return iconClass;
}
const toggleUpdateFileModal = (selectedFile = null, open = true) => {
    showingUpdateFileModal.value = open;
    file.value = selectedFile;
}
const fetchNViewFile = async (id) => {
    try {
        const res = await getFile(id);
        if (res && res.data && res.data.status === '1') {
            toggleViewFileModal(file.value)
        }
    } catch (err) {
        console.error("Error in getFile:", err);
        throw err;
    }
};
const fetchNEditFile = (file) => {
    try {
        // const res = await getFile(id);
        // if (res && res.data && res.data.status === '1') {
        toggleUpdateFileModal(file, true)
        // }
    } catch (err) {
        console.error("Error in getFile:", err);
        throw err;
    }
};
const openCreateDocumentModal = (slug) => {
    toggleGeneratePDFFileModal(slug, null, true)
}
const editDataBtnFunc = (selectedFile) => {
       // let file = fileLists.value.find(file => file.id === id);
        showEditFileContent(selectedFile)
}

onMounted(() => {
    // initDatatable();
    getMyFileList();


    const updateFile = async (id) => {
        console.log("id", id)
        try {
            const res = await getFile(id);
            if (res && res.data && res.data.status === '1') {
                toggleUpdateFileModal(file.value)
            }
        } catch (err) {
            console.error("Error in getFile:", err);
            throw err;
        }
    };
})

</script>
<style scoped>
.link-icon {
    margin-left: 5px;
}

.file-action-group {
    width: 100%;
}
</style>
