<template>
    <div>
        <div class="d-flex align-items-center mb-3">
            <h5 class="flex-grow-1 fs-16 mb-0" id="filetype-title">{{ title }} Files</h5>
            <div class="flex-shrink-0">
                <button class="btn btn-success action-save" v-if="folder.id" @click="toggleUploadFilesModal"><i
                        class="ri-add-line align-bottom me-1"></i> Upload Files</button>
            </div>
        </div>

        <div>
            <div class="table-sticky-header-container" id="table-sticky-header">
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1 no-border d-inline-flex" role="tablist">
                    <li class="nav-item" role="presentation" v-for="(filterTab, index) in typeFilterTabs">
                        <a @click="applyFilter(filterTab.column, filterTab.value)" class="nav-link margin-minus-4"
                            :class="{ 'active': index === 0 }" data-bs-toggle="tab" :id="`type-filter-${filterTab.value}`"
                            href="javascript:void(0)" role="tab" :aria-selected="index === 0" tabindex="-1">
                            {{ filterTab.title }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation" v-for="(filterTab, index) in modelTypeFilterTabs">
                        <a @click="applyFilter(filterTab.column, filterTab.value)" class="nav-link margin-minus-4"
                            data-bs-toggle="tab" :id="`model-type-filter-${filterTab.value}`" href="javascript:void(0)"
                            role="tab" aria-selected="false" tabindex="-1">
                            {{ filterTab.title }}
                        </a>
                    </li>
                </ul>
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
                </table>
            </div>
        </div>
    </div>
    <component @toggleViewFileModal="toggleViewFileModal" :is="showingViewFileModal ? ViewFileModal : null" :file="file">
    </component>
    <component @toggleGeneratePDFFileModal="toggleGeneratePDFFileModal"
        :is="showingGeneratePDFFileModal ? GeneratePDFFileModal : null" :file="file" :propsSourceFile="sourceFile" :fileType="pdfEditFileType"
    :creation_type="queryCreationType"
    >
    </component>

    <component @toggleUploadFilesModal="toggleUploadFilesModal" @refreshFilesList="applyFilter"
        :is="showingUploadFilesModal ? UploadFilesModal : null" :folder="folder">
    </component>
    <component :is="showingUpdateFileModal ? UpdateFileModal : null" :file="file" @fileUpdated="fileUpdated"></component>
</template>
<script setup>
import { onMounted, watch, ref, defineExpose, provide, computed } from 'vue';
import useFolders from '../composables/folders';
import ViewFileModal from './ViewFileModal';
import GeneratePDFFileModal from './GeneratePDFFileModal';
import { useRoute } from 'vue-router';
import UploadFilesModal from './UploadFilesModal';
import UpdateFileModal from "./UpdateFileModal.vue";
const sourceFile = ref(null);
const {
    file,
    getFile,
    deleteFile
} = useFolders();

const { property, getProperty } = useFolders();

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
}else{
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

const datatable = ref(null);

const toggleUploadFilesModal = (open = true) => {
    showingUploadFilesModal.value = open;
}


const showEditFileContent = (selectedFile) => {
    let slug = "";
    const { data } = selectedFile;
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
            initDatatable();
        }
    } catch (err) {
        console.error("Error in deleteFile:", err);
        throw err;
    }
};

const showRemoveFile = (fileId) => {
    Swal.fire({
        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to remove this lead from this deal ?</p></div></div>',


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

const initDatatable = (filterColumn = null, filterValue = null) => {

    if (datatable.value != null) {
        $('#dataTable').DataTable().destroy();
    }

    datatable.value = $('#dataTable').DataTable({ //datatable initialization
        "order": [
            [5, "DESC"]
        ],
        'lengthMenu': [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],
        dom: `<'row'<'col-sm-6 text-left'B> <'col-sm-6 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
        buttons: [],
        language: {
            searchPlaceholder: "Search files"
        },
        scrollX: true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "/api/vue/v1/files-list-datatable",
            type: "get",
            data: function (d) {
                d.folder_id = props.folder.id;
                if (filterColumn != null && filterValue != null) {
                    d[filterColumn] = filterValue;
                }
            },
            error: function () {
                $(".users-error").html("");
                $("#dataTable").append(`<tbody class="users-error"><tr><th colspan="3">No files found in ${props.folder.name || "Root"} Folder</th></tr></tbody>`);
                $("#dataTable_processing").css("display", "none");
            }
        },
        fnInitComplete: function (oSettings, json) {
            fileLists.value = json.data;
            let first = $("#dataTable_wrapper .row div:first");
            first.addClass("col-md-5").removeClass("col-md-6")
            $(first).next().addClass("col-md-7").removeClass("col-md-6")
            $(".dataTables_filter label").addClass("d-flex justify-between-between align-items-center gap-2")
        },

        "aoColumns": [
            {
                data: 'title',
                name: 'title',

            },
            {
                data: "type",
                name: "type",
            },
            {
                data: "folder.model_type",
                name: "folder.model_type",
                orderable: false,
            },
            {
                data: "size",
                name: "size",
            },
            {
                data: "createdBy.name",
                name: "createdBy.name",
                orderable: false,
            },
            {
                data: "updated_at",
                name: "updated_at",
            },
            {
                data: null,
                orderable: false,
            },
        ],
        "columnDefs": [
            {
                "targets": 0,
                orderable: true,
                render: function (type, row, data, meta) {
                    let iconClass = "ri-gallery-fill text-success";

                    if (data.extension == "pdf") {
                        iconClass = "ri-file-pdf-fill text-danger";
                    }
                    if (data.extension == "doc" || data.extension == "docx") {
                        iconClass = "ri-file-word-fill text-secondary";
                    }
                    if (data.extension == "zip") {
                        iconClass = "ri-file-zip-fill text-dark";
                    }

                    return `<a href="javascript:void(0)" class="d-flex align-items-center view-file-button" data-id="${data.id}">
                        <div class="flex-shrink-0 fs-17 me-2 filelist-icon"><i
                            class="${iconClass} align-bottom"></i></div>
                        <div class="flex-grow-1 filelist-name">${data.title}</div>
                    </a>`;
                }
            },
            {
                "targets": 1,
                orderable: true,
                render: function (type, row, data, meta) {
                    return data.type;
                }
            },
            {
                "targets": 2,
                orderable: true,
                render: function (type, row, data, meta) {
                    let modelType = data.folder?.model_type || "-";
                    return modelType.substring(modelType.lastIndexOf("\\") + 1);
                }
            },
            {
                "targets": 3,
                orderable: true,
                render: function (type, row, data, meta) {
                    return data.formatted_size
                }
            },
            {
                "targets": 4,
                orderable: true,
                render: function (type, row, data, meta) {
                    if (!data.created_by) return "-";
                    return `<div class="d-flex align-items-center" title="${data.created_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.created_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.created_by.first_name}<p title="Created at" class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
                                </div>`;
                }
            },
            {
                "targets": 5,
                orderable: true,
                render: function (type, row, data, meta) {
                    if (!data.updated_by) return `<div class="flex-grow-1"><p class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>`;
                    return `<div class="d-flex align-items-center" title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.first_name}<p title="Created at" class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
                                </div>`;
                }
            },
            {
                targets: 6,
                orderable: false,
                render: function (type, row, data, meta) {
                    let editFileContentButton = "";
                    if (data.data_editable) {
                        editFileContentButton = `<li><a class="dropdown-item edit-file-content-button" data-id="${data.id}" href="javascript:void(0)">Edit Document Data</a></li>`
                    }

                    let generatePDFFileButton = "";
                    if (isDocumentTemplate(data.slug)) {
                        generatePDFFileButton = `<li><a class="dropdown-item generate-pdf-file-button" data-slug="${data.slug}" href="javascript:void(0)">Write Document</a></li>`;
                    }



                    let deleteFileButton = ``;
                    if (!isDocumentTemplate(data.slug)) {
                        deleteFileButton = `<li><a class="dropdown-item delete-file-button" href="javascript:void(0)" data-id="${data.id}">Delete</a></li>`;
                    }

                    return `<div class="d-flex gap-3 justify-content-center">
                                <div class="dropdown"> <button class="btn btn-light btn-icon btn-sm dropdown" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false"> <i
                                            class="ri-more-fill align-bottom"></i> </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item view-file-button" data-id="${data.id}" href="javascript:void(0)">View</a></li>
                                        <li><a class="dropdown-item edit-file" data-id="${data.id}"  href="javascript:void(0)">Rename</a></li>
                                        ${editFileContentButton}
                                        ${generatePDFFileButton}
                                        <li><a class="dropdown-item" href="${data.path}" target="_blank">Download</a></li>
                                        ${deleteFileButton}
                                    </ul>
                                </div>
                            </div>`;
                }
            }
        ]
    });
}

const applyFilter = (column, value) => {
    if (value == "all") {
        initDatatable(null, null);
        return;
    }
    initDatatable(column, value);
}

defineExpose({
    applyFilter
})

watch(() => props.folder, (folder) => {
    initDatatable();
})
const fileUpdated = () => {
   $("#datatable").DataTable().ajax.reload();
    showingUpdateFileModal.value = false;
}
onMounted(() => {
    initDatatable();

    $(document).on("click", ".edit-file-content-button", async function () {
        let id = $(this).data("id");
        // await getFile(id);
        //find it in fileLists and pass to showEditFileContent
        let file = fileLists.value.find(file => file.id === id);

        showEditFileContent(file)
    })

    $(document).on("click", ".generate-pdf-file-button", function () {
        let slug = $(this).data("slug");
        toggleGeneratePDFFileModal(slug)
    })

    $(document).on("click", ".delete-file-button", function () {
        let id = $(this).data("id");
        showRemoveFile(id)
    })

    $(document).on("click", ".view-file-button", async function () {
        let id = $(this).data("id");
        await getFile(id);
        toggleViewFileModal(file.value, true)
    })
    $(document).on("click", ".edit-file", async function () {

        let id = $(this).data("id");
        console.log(id)
        await getFile(id);
        toggleUpdateFileModal(file.value, true)
    })

    const toggleUpdateFileModal = (selectedFile = null, open = true) => {
        showingUpdateFileModal.value = open;
        file.value = selectedFile;
    }
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
</style>
