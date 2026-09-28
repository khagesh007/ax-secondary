<template>
    <div class="row">
        <PropertyByUser @propertySelected="propertySelected" :multiple="false"></PropertyByUser>
        <div class="col-md-6 left-form" :style="{ maxHeight: '700px', overflowY: 'auto' }">
            <div class="row">
                <div class="col-12">
                    <h4>Marketing Form A - Leasing Fillable</h4>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Property Usage</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_for"
                                        id="property_for-Commercial" value="Commercial" v-model="formData.property_for">
                                    <label class="form-check-label" for="property_for-Commercial">Commercial</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_for"
                                        id="property_for-Residential" value="Residential" v-model="formData.property_for">
                                    <label class="form-check-label" for="property_for-Residential">Residential</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="lsting_consultant" class="form-label">Listing Consultant</label>
                            <input type="text" class="form-control" id="lsting_consultant"
                                v-model="formData.lsting_consultant" @change="handleOnInputChange">
                        </div>
                        <div class="col-md-6">
                            <label for="property_reference_no" class="form-label">Property Reference No</label>
                            <input type="text" class="form-control" id="property_reference_no"
                                v-model="formData.property_reference_no" @change="handleOnInputChange">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>Landlord Details</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="landlord_name" class="form-label">Landlord Name</label>
                            <input type="text" class="form-control" id="landlord_name" v-model="formData.landlord_name" @change="handleOnInputChange">
                        </div>
                    </div>

                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="passport" class="form-label">Passport Number</label>
                            <input type="text" class="form-control" id="passport" v-model="formData.passport" @change="handleOnInputChange">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="emirates_id_no" class="form-label">Emirates ID No</label>
                                <input type="text" id="emirates_id_no" class="form-control"
                                    v-model="formData.emirates_id_no" @change="handleOnInputChange">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="passport_expiry" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="passport_expiry" v-model="formData.passport_expiry" @change="handleOnInputChange">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>Property Details</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Property Type</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="category" id="category-Villa"
                                        value="Villa" v-model="formData.category" @change="handleOnInputChange">
                                    <label class="form-check-label" for="category-Villa">TH/Villa</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="category" id="category-Apartment"
                                        value="Apartment" v-model="formData.category" @change="handleOnInputChange">
                                    <label class="form-check-label" for="category-Apartment">Apartment</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">Property Furnished</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_furnished"
                                        id="property_furnished-FURNISHED" value="FURNISHED"
                                        v-model="formData.property_furnished" @change="handleOnInputChange">
                                    <label class="form-check-label" for="property_furnished-FURNISHED">Furnished</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_furnished"
                                        id="property_furnished-UNFURNISHED" value="UNFURNISHED"
                                        v-model="formData.property_furnished" @change="handleOnInputChange">
                                    <label class="form-check-label" for="property_furnished-UNFURNISHED">Unfurnished</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_furnished"
                                        id="property_furnished-SEMI_FURNISHED" value="SEMI_FURNISHED"
                                        v-model="formData.property_furnished" @change="handleOnInputChange">
                                    <label class="form-check-label" for="property_furnished-SEMI_FURNISHED">Semi
                                        Furnished</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="basiInput" class="form-label">Property Status</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_status"
                                        id="property_status-Vacant" value="Vacant" v-model="formData.property_status" @change="handleOnInputChange">
                                    <label class="form-check-label" for="property_status-Vacant">Vacant</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="property_status"
                                        id="property_status-Tenanted" value="Tenanted" v-model="formData.property_status" @change="handleOnInputChange">
                                    <label class="form-check-label" for="property_status-Tenanted">Tenanted</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="availability_date" class="form-label">Available Date</label>
                            <input type="date" class="form-control" id="availability_date"
                                v-model="formData.availability_date" @change="handleOnInputChange">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="building_name" class="form-label">Building Name</label>
                            <input type="text" class="form-control" id="building_name" v-model="formData.building_name" @change="handleOnInputChange">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="unit_no" class="form-label">Unit No</label>
                                <input type="text" id="unit_no" class="form-control" v-model="formData.unit_no" @change="handleOnInputChange">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="street" class="form-label">Street Name/No</label>
                            <input type="text" class="form-control" id="street" v-model="formData.street" @change="handleOnInputChange">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="community" class="form-label">Community</label>
                                <input type="text" id="community" class="form-control" v-model="formData.community" @change="handleOnInputChange">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="build_up_area" class="form-label">BUA(SqFt)</label>
                            <input type="text" class="form-control" id="build_up_area" v-model="formData.build_up_area" @change="handleOnInputChange">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="plot_size" class="form-label">Plot Size(SqFt)</label>
                                <input type="text" id="plot_size" class="form-control" v-model="formData.plot_size" @change="handleOnInputChange">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-4">
                            <label for="bedrooms" class="form-label">Bedrooms</label>
                            <input type="text" class="form-control" id="bedrooms" v-model="formData.bedrooms" @change="handleOnInputChange">
                        </div>
                        <div class="col-md-4">
                            <div>
                                <label for="bathrooms" class="form-label">Bathrooms</label>
                                <input type="text" id="bathrooms" class="form-control" v-model="formData.bathrooms" @change="handleOnInputChange">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div>
                                <label for="parking" class="form-label">Parking</label>
                                <input type="text" id="parking" class="form-control" v-model="formData.parking" @change="handleOnInputChange">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="rental_amount" class="form-label">Rental Amount</label>
                            <input type="number" class="form-control" id="rental_amount" v-model="formData.rental_amount" @change="handleOnInputChange">
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div>
                            <label for="commission" class="form-label">Commission</label>
                            <input type="text" class="form-control" id="commission" v-model="formData.commission" @change="handleOnInputChange">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row mt-2" id="">
                        <div class="col-12">
                            <h5>Terms and Conditions</h5>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-12">
                            <label for="basiInput" class="form-label">The landlord/legal representative has agreed to
                                appoint AX CAPITAL Real Estate as its</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="landlord_agreed_to_appoint_as"
                                        id="landlord_agreed_to_appoint_as-Exclusive" value="Exclusive"
                                        v-model="formData.landlord_agreed_to_appoint_as" @change="handleOnInputChange">
                                    <label class="form-check-label"
                                        for="landlord_agreed_to_appoint_as-Exclusive">Exclusive</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="landlord_agreed_to_appoint_as"
                                        id="landlord_agreed_to_appoint_as-Non-Exclusive" value="Non-Exclusive"
                                        v-model="formData.landlord_agreed_to_appoint_as" @change="handleOnInputChange">
                                    <label class="form-check-label" for="landlord_agreed_to_appoint_as-Non-Exclusive">Non
                                        Exclusive</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="basiInput" class="form-label">Advertise the above property for a period of </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="advertise_period_of_month"
                                        id="advertise_period_of_month-1" value="1"
                                        v-model="formData.advertise_period_of_month" @change="handleOnInputChange">
                                    <label class="form-check-label" for="advertise_period_of_month-1">1 month</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="advertise_period_of_month"
                                        id="advertise_period_of_month-2" value="2"
                                        v-model="formData.advertise_period_of_month" @change="handleOnInputChange">
                                    <label class="form-check-label" for="advertise_period_of_month-2">2 Months</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="advertise_period_of_month"
                                        id="advertise_period_of_month-3" value="3"
                                        v-model="formData.advertise_period_of_month" @change="handleOnInputChange">
                                    <label class="form-check-label" for="advertise_period_of_month-3">3 Months</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2" id="">
                        <div class="col-md-6">
                            <label for="advertise_from" class="form-label">From</label>
                            <input type="date" class="form-control" id="advertise_from" v-model="formData.advertise_from" @change="handleOnInputChange">
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="advertise_to" class="form-label">To</label>
                                <input type="date" id="advertise_to" class="form-control" v-model="formData.advertise_to" @change="handleOnInputChange">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <iframe id="pdfIFrame" :src="docURl" frameborder="0" class="responsive-iframe" width="100%"
                height="700px"></iframe>
        </div>
    </div>
</template>
<script setup>
import { ref, defineExpose, onMounted, watch, inject } from 'vue';

import useFolders from '../../composables/folders';
import PropertyByUser from '../../../../components/formElements/PropertyByUser.vue';

const { generatePDF } = useFolders();

const getProperty = inject("getProperty");

const props = defineProps({
    property: {
        type: Object,
        required: true
    },
    file: {
        type: Object || null,
        required: false
    }
});

const emit = defineEmits(["setDownloadFileUrl"]);

const docURl = ref(`${documentsPath}/marketing-form-a-leasing-fillable-sample.pdf`)

const formData = ref({
    filename: '',
    type: "marketing-form-a-leasing-fillable",
    file_id: '',
    property_id: '',
    property_for: '',
    lsting_consultant: '',
    property_reference_no: '',
    landlord_name: '',
    passport: '',
    emirates_id_no: '',
    passport_expiry: '',
    category: '',
    property_furnished: '',

    property_status: '',
    availability_date: '',
    building_name: '',
    unit_no: '',
    street: '',
    community: '',
    build_up_area: '',
    plot_size: '',
    bedrooms: '',
    bathrooms: '',
    parking: '',
    rental_amount: '',
    commission: '',
    landlord_agreed_to_appoint_as: '',
    advertise_period_of_month: '',
    advertise_from: '',
    advertise_to: '',
})

const submitForm = async () => {
    emit("setDownloadFileUrl", "");
    const res = await generatePDF(formData.value);
    if (res && res.data && res.data.status === '1') {
        if (res.data.url) {
            docURl.value = res.data.url;
            refreshIFrame();
            emit("setDownloadFileUrl", res.data.url);
        }
    }
}

defineExpose({
    submitForm
})

onMounted(() => {
    formData.value.filename = moment(new Date()).format("YYYY-MM-DD H-m-ss");
});

const refreshIFrame = () => {
    const iframe = document.getElementById('pdfIFrame');
    if (iframe) {
        const contentDoc = iframe.contentDocument;

        if (contentDoc) {
            contentDoc.location.reload(true);
        }
    }
}

const handleOnInputChange = (event) => {
    submitForm();
}


const setFormData = (property, file = null, reset = false) => {
    if (property === null || reset === true) {
        Object.keys(formData.value).map(key => {
            if (key == "filename" || key == "type") {
                return;
            }
            formData.value[key] = '';
        })
        return
    }
    let fileData = null;
    if (file !== null && file !== undefined) {
        fileData = file.data;

        if (file.path) {
            docURl.value = file.path;
            refreshIFrame();
        }

        if(file.slug !== "marketing-form-a-leasing-fillable"){
            formData.value.file_id = file.id;
        }
    }
    if (fileData !== null && fileData !== undefined) {
        Object.keys(fileData).map(key => {
            if(key == "file_id"){
                return
            }
            formData.value[key] = fileData[key] || '';
        })
    } else {
        formData.value.property_id = property.id || '';
        formData.value.property_for = (property.for || '').includes("Residential") ? "Residential" : "Commercial";
        formData.value.lsting_consultant = property.agent?.name || '';
        formData.value.property_reference_no = property.ref_no || '';
        formData.value.landlord_name = (property?.owner?.first_name || '') + " " + (property?.owner?.last_name || '');
        formData.value.passport = property?.owner?.passport || '';
        formData.value.emirates_id_no = property.emirates_id_no || '';
        formData.value.passport_expiry = property?.owner?.passport_expiry || '';
        formData.value.category = property?.category || '';
        formData.value.property_furnished = property.furnished || '';
        formData.value.property_status = property.property_status?.title || '';
        formData.value.availability_date = property.availability_date || '';
        formData.value.building_name = '';
        formData.value.unit_no = property.unit_no || '';
        formData.value.street = property.street || '';

        formData.value.community = property?.community?.name || '';
        formData.value.build_up_area = property.built_up_area || '';
        formData.value.plot_size = property.plot_size || '';
        formData.value.bedrooms = property.bed?.name || '';
        formData.value.bathrooms = property.bath?.name || '';
        formData.value.parking = property.parking || '';
        formData.value.rental_amount = property?.price?.price || '';
        formData.value.commission = property.commission || '';
        formData.value.landlord_agreed_to_appoint_as = (property.exclusive || '') == 1 ? "Exclusive" : "Non-Exclusive";
        formData.value.advertise_period_of_month = property.advertise_period_of_month || '';
        formData.value.advertise_from = property.exclusive_start || '';
        formData.value.advertise_to = property.exclusive_end || '';
    }
}

watch(() => props.property, (property) => {
    setFormData(property, props.file)
})

onMounted(() => {
    if (Object.keys(props.file || {}).length !== 0) {
        setFormData(props.property, props.file)
    }
})

const propertySelected = async (selectedProperty) => {
    await getProperty(selectedProperty?.id)
}

</script>
<style scoped></style>
