<script setup>
import {onMounted, ref} from "vue";

const props = defineProps({
    onCloseModal: {type: Function, required: true},
    getSubmissions: {type: Function, required: true},
    toggleSnackbar: {type: Function, required: true},
});

const dialog = ref(true)

const formSubmitting = ref(false)

const selectedProperty = ref(null);

const formRef = ref(null);
const formIsValid = ref(false);

const properties = ref({
    data: [],
    loading: false
});

const searchProperties = async (query) => {
    properties.value.loading = true;
    try {
        const {data} = await axios.get('/reporting/admin/listing-verifications/get-properties-for-verification', {
            params: {term: query}
        });
        if (data.data) {
            properties.value.data = data.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        properties.value.loading = false;
    }
}

const addMoreDocument = () => {
    selectedProperty.value.other_documents.push({
        title: "",
        file: null,
        type: null,
    })
}

const removeDocument = (index) => {
    selectedProperty.value.other_documents.splice(index, 1)
}

const submitForm = async () => {
    formRef.value?.validate();
    if (formIsValid.value) {
        formSubmitting.value = true;
        try {
            const formData = new FormData();
            // Append all primitive fields
            for (const [key, value] of Object.entries(selectedProperty.value)) {
                if (typeof value !== 'object') {
                    formData.append(key, value);
                }
            }

            selectedProperty.value.documents?.map((item, index) => {
                formData.append(`documents[${index}][id]`, item.id)
                formData.append(`documents[${index}][title]`, item.title)
                formData.append(`documents[${index}][path]`, item.path)
                formData.append(`documents[${index}][selected]`, item.selected)
                if (item.type){
                    formData.append(`documents[${index}][type]`, item.type)
                }
            })

            selectedProperty.value.other_documents?.map((item, index) => {
                if (item.file) {
                    formData.append(`other_documents[${index}][title]`, item.title)
                    formData.append(`other_documents[${index}][file]`, item.file)
                    if (item.type) {
                        formData.append(`other_documents[${index}][type]`, item.type)
                    }
                }
            })

            const {data} = await axios.post('/reporting/admin/listing-verifications/submit-for-verification',
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            );
            if (data.status) {
                closeModal();
                props.getSubmissions();
                props.toggleSnackbar(data.message)
            } else {
                props.toggleSnackbar(data.message, "error")
            }

        } catch (error) {
            console.error("error: ", error);
        } finally {
            formSubmitting.value = false;
        }
    }
}

const closeModal = () => {
    props.onCloseModal()
    selectedProperty.value = null;
}

const selectProperty = (item = null) => {
    selectedProperty.value = item;
    if (item) {
        selectedProperty.value.other_documents = [];
    }
}

const documentTypes = [
    'authorization', 'ownership', 'identification', 'representationPao', 'representationId', 'others'
];

const selectDocumentType = (value, index, key) => {
    selectedProperty.value[key][index]['type'] = value;
}

const getPurposeColor = (purpose) => {
    switch (purpose) {
        case 'Residential for sale':
            return 'primary';
        case 'Residential for rent':
            return 'primary';
        case 'Commercial for sale':
            return 'warning';
        case 'Commercial for rent':
            return 'warning';
        default:
            return 'primary';
    }
}

onMounted(async () => {
    await searchProperties("");
})
</script>

<template>

    <v-dialog v-model="dialog" persistent max-width="800">
        <v-card>

            <v-card-title class="border-b">
                Add New Submission
            </v-card-title>

            <v-card-text>
                <v-row>

                    <v-col
                        v-if="properties.loading"
                        cols="12"
                        class="d-flex flex-column align-center justify-center py-4"
                    >
                        <VProgressCircular size="24" indeterminate color="secondary" class="mb-2"/>
                        <span class="text-body-2 text-secondary">Loading available listings...</span>
                    </v-col>

                    <v-col v-else cols="12">
                        <v-list v-if="selectedProperty" border rounded bg-color="grey-lighten-4">
                            <v-list-item>
                                <div class="d-flex align-center gap-2 ">
                                    <VImg
                                        v-if="selectedProperty?.thumbnail"
                                        :src="selectedProperty?.thumbnail"
                                        :lazy-src="selectedProperty?.thumbnail"
                                        min-height="60"
                                        max-height="60"
                                        min-width="60"
                                        max-width="60"
                                        rounded
                                        cover
                                    />
                                    <VAvatar v-else rounded border size="60"></VAvatar>

                                    <div>
                                        <div>
                                            <v-chip
                                                v-if="selectedProperty?.for"
                                                variant="tonal"
                                                class="mr-2"
                                                :color="getPurposeColor(selectedProperty?.for)" size="x-small"
                                            >
                                                {{ selectedProperty?.for }}
                                            </v-chip>
                                            <span class="text-caption">{{
                                                    selectedProperty?.ref_no
                                                }} - {{ selectedProperty?.category }}</span>
                                        </div>
                                        <v-list-item-title class="text-body-2">{{
                                                selectedProperty?.title
                                            }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle class="text-caption">{{
                                                selectedProperty?.location
                                            }}
                                        </v-list-item-subtitle>
                                    </div>
                                </div>

                                <template v-slot:append>
                                    <v-icon
                                        icon="mdi-close"
                                        class="cursor-pointer"
                                        @click="selectProperty(null)"
                                    ></v-icon>
                                </template>
                            </v-list-item>
                        </v-list>

                        <v-list v-else max-height="550px" class="overflow-y-auto p-0">
                            <v-list-item
                                v-for="(item,index) in properties.data"
                                :key="item.id"
                                class="cursor-pointer"
                                :class="index === properties.data.length - 1 ? '' : 'mb-3'"
                                @click="selectProperty(item)"
                            >
                                <div class="d-flex align-center gap-2 ">
                                    <VImg
                                        v-if="item?.thumbnail"
                                        :src="item?.thumbnail"
                                        :lazy-src="item?.thumbnail"
                                        min-height="60"
                                        max-height="60"
                                        min-width="60"
                                        max-width="60"
                                        rounded
                                        cover
                                    />
                                    <VAvatar v-else rounded border size="60"></VAvatar>

                                    <div>
                                        <div>
                                            <v-chip
                                                v-if="item?.for"
                                                variant="tonal"
                                                class="mr-2"
                                                :color="getPurposeColor(item?.for)" size="x-small"
                                            >
                                                {{ item?.for }}
                                            </v-chip>
                                            <span class="text-caption">{{ item?.ref_no }} - {{
                                                    item?.category
                                                }}</span>
                                        </div>
                                        <v-list-item-title class="text-body-2">{{ item?.title }}</v-list-item-title>
                                        <v-list-item-subtitle class="text-caption">{{
                                                item?.location
                                            }}
                                        </v-list-item-subtitle>
                                    </div>
                                </div>

                            </v-list-item>
                        </v-list>
                    </v-col>

                </v-row>

                <v-row v-if="selectedProperty">
                    <v-col cols="12">
                        <v-form ref="formRef" v-model="formIsValid">
                            <v-row align="center" v-for="(item, index) in selectedProperty?.documents"
                                   :key="item.id">
                                <v-col cols="12" md="4">
                                    <v-checkbox v-model="item.selected" :label="item?.title" hide-details/>
                                </v-col>
                                <v-col cols="12" md="8">
                                    <v-select
                                        label="Select Document Type"
                                        :items="documentTypes"
                                        density="compact"
                                        size="small"
                                        @update:modelValue="(value) => selectDocumentType(value, index, 'documents')"
                                        variant="outlined"
                                        hide-details
                                        :rules="[
                                            (v) => {
                                              if (item.selected) {
                                                return !!v || 'Type is required';
                                              }
                                              return true;
                                            }
                                          ]"
                                    >
                                        <template #append>
                                            <v-btn :href="item.path" target="_blank" v-tooltip="`View Document`"
                                                   color="primary" variant="tonal" size="small" icon rounded>
                                                <v-icon icon="mdi-open-in-new"></v-icon>
                                            </v-btn>
                                        </template>
                                    </v-select>
                                </v-col>
                            </v-row>

                            <v-divider/>

                            <v-row
                                dense
                                align="center"
                                v-for="(item, index) in selectedProperty?.other_documents"
                                :key="index"
                            >
                                <v-col cols="12" md="4">
                                    <v-text-field
                                        v-model="item.title"
                                        label="Document Title"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                        :rules="[(v) => !!v || 'Title is required']"
                                    />
                                </v-col>

                                <v-col cols="12" md="4">
                                    <v-select
                                        label="Document Type"
                                        :items="documentTypes"
                                        variant="outlined"
                                        density="compact"
                                        @update:modelValue="(value) => selectDocumentType(value, index, 'other_documents')"
                                        hide-details
                                        :rules="[(v) => !!v || 'Type is required']"
                                    ></v-select>
                                </v-col>

                                <v-col cols="12" md="4">
                                    <v-file-input
                                        v-model="item.file"
                                        clearable
                                        label="Upload File"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                        prepend-icon=""
                                        :rules="[(v) => !!v || 'File is required']"
                                    >
                                        <template #append>
                                            <v-btn
                                                @click="removeDocument(index)"
                                                color="error"
                                                size="small"
                                                rounded
                                                variant="tonal"
                                                icon="mdi-delete-outline"
                                                title="Remove document"
                                            />
                                        </template>
                                    </v-file-input>
                                </v-col>

                            </v-row>

                            <div
                                :class="[selectedProperty?.other_documents.length > 0 ? 'mt-5' : '', 'text-center']">
                                <v-btn
                                    @click="addMoreDocument"
                                    prepend-icon="mdi-plus"
                                    text="Add New Document"
                                    color="secondary"
                                />
                            </div>

                        </v-form>

                    </v-col>
                </v-row>

            </v-card-text>

            <v-card-actions class="d-flex align-center justify-end border-t pt-3">

                <v-btn @click="closeModal" :disabled="formSubmitting" color="primary" size="small" variant="tonal">
                    Cancel
                </v-btn>
                <v-btn v-if="selectedProperty" @click="submitForm" :loading="formSubmitting" color="success"
                       size="small"
                       variant="elevated"
                >
                    Submit
                </v-btn>

            </v-card-actions>

        </v-card>
    </v-dialog>

</template>

<style scoped>
.chart {
    width: 100% !important;
    height: 400px;
}
</style>
