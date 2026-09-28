<template>
    <div>
        <div id="dealCreateFormModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" aria-labelledby="myModalLabel"
             aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-soft-danger">
                        <h5 class="modal-title" id="myModalLabel">Create New Deal</h5>
                        <button type="button" @click="removeModal()" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5 class="fs-15">
                            You are creating a deal for property with REF NO.: {{property.ref_no}}
                        </h5>
                        <hr>

                        <div class="row">
                            <h5 class="col-12 text-center">
                                {{formData.leadType === 'buyer' ? 'Seller' : 'Owner'}} Details
                            </h5>
                            <div class="col-12">

                                <div>
                                    <form @submit.prevent="" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-12">

                                                <div class="card-body" style=" min-height: 500px;">
                                                    <div class="tab-content text-muted">

                                                            <div class="row">
                                                                <div class="col-12 d-flex justify-content-center">

                                                                    <!-- Radio Buttons -->
                                                                    <div class="btn-group" role="group"
                                                                         aria-label="Basic radio toggle button group">
                                                                        <input type="radio" class="btn-check"
                                                                               name="btnradio" id="btnradio1"
                                                                               value="INDIVIDUAL"
                                                                               :checked="formData.owner_type === 'INDIVIDUAL'"
                                                                               v-model="formData.owner_type" autocomplete="off"
                                                                               checked="">
                                                                        <label class="btn btn-sm btn-outline-secondary"
                                                                               for="btnradio1"><i
                                                                            class="ri-user-2-line"
                                                                            style="vertical-align: sub;"></i>
                                                                            Individual</label>

                                                                        <input type="radio" class="btn-check"
                                                                               id="btnradio2" v-model="formData.owner_type"
                                                                               name="btnradio"
                                                                               :checked="formData.owner_type === 'COMPANY'"
                                                                               value="COMPANY"
                                                                               autocomplete="off">
                                                                        <label class="btn btn-sm btn-outline-secondary"
                                                                               for="btnradio2">Company <i
                                                                            class="ri-team-line"
                                                                            style="vertical-align: bottom;"></i></label>
                                                                    </div>
                                                                </div>

                                                                <!-- <div class="col-2"></div> -->


                                                            </div>
                                                            <div class="row mt-2" v-if="formData.owner_type == 'COMPANY'">
                                                                <h5 class="mt-2">Company Information</h5>
                                                                <div class="col-2 text-label mt-2">Company Name *</div>
                                                                <div class="col-4 mt-2">
                                                                    <input type="text" class="form-control"
                                                                           :class="{ 'is-invalid': fieldErrors.owner_company.error}"
                                                                           v-model="formData.owner_company" name="company"
                                                                           id="company" placeholder="Enter company"
                                                                           :required="requiredFields.owner_company">
                                                                    <div class="invalid-feedback">
                                                                        {{ fieldErrors.owner_company.message }}
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 text-label mt-2">Trade Name *</div>
                                                                <div class="col-4 mt-2">
                                                                    <input type="text" class="form-control"
                                                                           :class="{ 'is-invalid': fieldErrors.owner_trade_license_name.error}"
                                                                           v-model="formData.owner_trade_license_name"
                                                                           name="trade_license" id="first_name"
                                                                           placeholder="Enter company trade name"
                                                                           :required="requiredFields.owner_trade_license_name">
                                                                    <div class="invalid-feedback">
                                                                        {{ fieldErrors.owner_trade_license_name.message }}
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 mt-2 text-label">Trade License *</div>
                                                                <div class="col-4 mt-2">
                                                                    <input type="text" class="form-control"
                                                                           :class="{ 'is-invalid': fieldErrors.owner_trade_license.error}"
                                                                           v-model="formData.owner_trade_license"
                                                                           name="trade_license" id="first_name"
                                                                           placeholder="Enter trade license"
                                                                           :required="requiredFields.owner_trade_license">
                                                                    <div class="invalid-feedback">
                                                                        {{ fieldErrors.owner_trade_license.message }}
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 mt-2 text-label">Expiry Date of License *</div>
                                                                <div class="col-4 mt-2">
                                                                    <input type="date" class="form-control"
                                                                           :class="{ 'is-invalid': fieldErrors.owner_trade_license_expiry.error}"
                                                                           v-model="formData.owner_trade_license_expiry"
                                                                           name="trade_license_expiry"
                                                                           id="trade_license_expiry"
                                                                           placeholder="Enter trade license expiry"
                                                                           :required="requiredFields.owner_trade_license_expiry">
                                                                    <div class="invalid-feedback">
                                                                        {{ fieldErrors.owner_trade_license_expiry.message }}
                                                                    </div>
                                                                </div>
                                                                <hr class="mt-3">
                                                            </div>

                                                            <div class="row">
                                                                <h5 class="mt-2" id="contact-information-title">{{formData.owner_type === 'COMPANY' ? "Representative Person's Information" : 'Personal Information'}}</h5>
                                                                <div class="col-6">

                                                                    <div class="row">
                                                                        <div class="col-4 text-label">First Name *</div>
                                                                        <div class="col-8">
                                                                            <input type="text" class="form-control"
                                                                                   :class="{ 'is-invalid': fieldErrors.owner_first_name.error}"
                                                                                   v-model="formData.owner_first_name"
                                                                                   name="first_name" id="first_name"
                                                                                   placeholder="Enter first name"
                                                                                   :required="requiredFields.owner_first_name">
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_first_name.message }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-4 mt-2 text-label">Last Name *</div>
                                                                        <div class="col-8 mt-2">
                                                                            <input type="text" class="form-control"
                                                                                   :class="{ 'is-invalid': fieldErrors.owner_last_name.error}"
                                                                                   v-model="formData.owner_last_name"
                                                                                   name="last_name" id="last_name"
                                                                                   placeholder="Last name"
                                                                                   :required="requiredFields.owner_last_name">
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_last_name.message }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-4 mt-2 text-label">Mobile *</div>
                                                                        <div class="col-8 mt-2">




                                                                            <input type="text" class="form-control"
                                                                                   :class="{ 'is-invalid': fieldErrors.owner_mobile.error}"
                                                                                   v-model="formData.owner_mobile"
                                                                                   name="contact-mobile" id="contact-mobile"
                                                                                   placeholder="Mobile"
                                                                                   :required="requiredFields.owner_mobile"
                                                                            @keyup="formData.owner_whatsapp = formData.owner_mobile"
                                                                            >
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_mobile.message }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 mt-2 text-label">Whatsapp *</div>
                                                                        <div class="col-8 mt-2">
                                                                            <input type="text" class="form-control"
                                                                                   :class="{ 'is-invalid': fieldErrors.owner_whatsapp.error}"
                                                                                   v-model="formData.owner_whatsapp"
                                                                                   name="contact-whatsapp" id="contact-whatsapp"
                                                                                   placeholder="Whatsapp number"
                                                                                   :required="requiredFields.owner_whatsapp">
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_whatsapp.message }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 mt-2 text-label">Email *</div>
                                                                        <div class="col-8 mt-2">
                                                                            <input type="email" class="form-control"
                                                                                   :class="{ 'is-invalid': fieldErrors.owner_email.error}"
                                                                                   v-model="formData.owner_email"
                                                                                   name="email" id="email"
                                                                                   placeholder="Email"
                                                                                   :required="requiredFields.owner_email">
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_email.message }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-4 mt-2 text-label">Gender *</div>
                                                                        <div class="col-8 mt-2">
                                                                            <v-select
                                                                                v-model="formData.owner_gender"
                                                                                :options="genders"
                                                                                :multiple="false"
                                                                                placeholder="Select gender"
                                                                                :class="{ 'is-invalid': fieldErrors.owner_gender.error}"
                                                                                :required="requiredFields.owner_gender"
                                                                            ></v-select>

                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_gender.message }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 mt-2 text-label">Nationality *</div>
                                                                        <div class="col-8 mt-2" v-if="countries">
                                                                            <v-select v-model="formData.owner_nationality_id"
                                                                                      :reduce="country => country.id"
                                                                                      :options="countries"
                                                                                      label="nationality"
                                                                                      @search="countries"
                                                                                      placeholder="Type to search countries"
                                                                                      :required="requiredFields.owner_nationality_id"
                                                                                      :class="{ 'is-invalid': fieldErrors.owner_nationality_id.error}"></v-select>
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_nationality_id.message }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 mt-2 text-label">Phone</div>
                                                                        <div class="col-8 mt-2">
                                                                            <input type="text" class="form-control"
                                                                                   :class="{ 'is-invalid': fieldErrors.owner_phone.error}"
                                                                                   v-model="formData.owner_phone"
                                                                                   name="phone" id="contact-phone"
                                                                                   placeholder="Phone number"
                                                                                   :required="requiredFields.owner_phone">
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_phone.message }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12 mt-2"></div>

                                                                    </div>
                                                                </div>

                                                                <div class="col-6">
                                                                    <div class="row">







                                                                        <div class="col-4 text-label" >Passport *</div>
                                                                        <div class="col-8" >
                                                                            <input type="text" class="form-control"
                                                                                   :class="{ 'is-invalid': fieldErrors.owner_passport.error}"
                                                                                   v-model="formData.owner_passport"
                                                                                   name="passport" id="passport"
                                                                                   placeholder="Enter passport number"
                                                                                   :required="requiredFields.owner_passport">
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_passport.message }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 mt-2 text-label">Passport Issue Date
                                                                        </div>
                                                                        <div class="col-8 mt-2">

                                                                            <flat-pickr data-date-format="d/m/Y"
                                                                                        placeholder="Passport issue date"
                                                                                        class="form-control"
                                                                                        name="passport_issue_date"
                                                                                        :class="{ 'is-invalid': fieldErrors.owner_passport_issue_date.error}"
                                                                                        id="passport_issue_date"
                                                                                        v-model="formData.owner_passport_issue_date"
                                                                                        :required="requiredFields.owner_passport_issue_date"/>
                                                                            <div class="invalid-feedback">
                                                                                {{
                                                                                    fieldErrors.owner_passport_issue_date.message
                                                                                }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 mt-2 text-label">Passport Expiry</div>
                                                                        <div class="col-8 mt-2">
                                                                            <flat-pickr data-date-format="d/m/Y"
                                                                                        placeholder="Passport expiry date"
                                                                                        class="form-control"
                                                                                        name="passport_expiry_date"
                                                                                        :class="{ 'is-invalid': fieldErrors.owner_passport_expiry.error}"
                                                                                        id="passport_expiry_date"
                                                                                        v-model="formData.owner_passport_expiry"
                                                                                        :required="requiredFields.owner_passport_expiry"/>
                                                                            <div class="invalid-feedback">
                                                                                {{
                                                                                    fieldErrors.owner_passport_expiry.message
                                                                                }}
                                                                            </div>

                                                                        </div>
                                                                        <div class="col-4 mt-2 text-label">Passport Country
                                                                        </div>
                                                                        <div class="col-8 mt-2 text-label" v-if="countries">
                                                                            <v-select
                                                                                v-model="formData.owner_passport_country_id"
                                                                                :reduce="country => country.id"
                                                                                :options="countries" label="name"
                                                                                @search="countries"
                                                                                placeholder="Passport issuing
                                                                            country name"
                                                                                :class="{ 'is-invalid': fieldErrors.owner_passport_country_id.error}"
                                                                                :required="requiredFields.owner_passport_country_id"></v-select>
                                                                            <div class="invalid-feedback">
                                                                                {{
                                                                                    fieldErrors.owner_passport_country_id.message
                                                                                }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 text-label mt-2" >Residential Address
                                                                        </div>
                                                                        <div class="col-8 mt-2" >
                                                                            <input type="text" class="form-control"
                                                                                   :class="{ 'is-invalid': fieldErrors.owner_address.error}"
                                                                                   v-model="formData.owner_address"
                                                                                   name="address" id="address"
                                                                                   placeholder="Enter address"
                                                                                   :required="requiredFields.owner_address">
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_address.message }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 mt-2 text-label">Date Of Birth</div>
                                                                        <div class="col-8 mt-2">
                                                                            <flat-pickr data-date-format="d/m/Y"
                                                                                        placeholder="Enter date of birth"
                                                                                        class="form-control"
                                                                                        :class="{ 'is-invalid': fieldErrors.owner_dob.error}"
                                                                                        name="dob" id="dob"
                                                                                        v-model="formData.owner_dob"
                                                                                        :required="requiredFields.owner_dob"/>
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_dob.message }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 mt-2 text-label">Place Of Birth</div>
                                                                        <div class="col-8 mt-2">
                                                                            <v-select
                                                                                v-model="formData.owner_pob"
                                                                                :reduce="country => country.name"
                                                                                :options="countries" label="name"
                                                                                @search="countries"
                                                                                placeholder="Type to search countries"
                                                                                :class="{ 'is-invalid': fieldErrors.owner_pob.error}"
                                                                                :required="requiredFields.owner_pob"></v-select>

                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_pob.message }}
                                                                            </div>
                                                                        </div>
                                                                        <div v-if="formData.leadType === 'buyer'" class="col-4 mt-2 text-label">Transaction Type *</div>
                                                                        <div v-if="formData.leadType === 'buyer'" class="col-8 mt-2">
                                                                            <v-select
                                                                                v-model="formData.transaction_type"
                                                                                :options="transactionTypes"
                                                                                placeholder="Transaction type"
                                                                                :class="{ 'is-invalid': fieldErrors.transaction_type.error}"
                                                                                :required="requiredFields.transaction_type"></v-select>

                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.transaction_type.message }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 text-label mt-2" for="owner_relationship" v-if="formData.owner_type == 'COMPANY'">Relationship *
                                                                        </div>
                                                                        <div class="col-8 mt-2" v-if="formData.owner_type == 'COMPANY'">
                                                                            <input type="text" class="form-control"
                                                                                   :class="{ 'is-invalid': fieldErrors.owner_relationship.error}"
                                                                                   v-model="formData.owner_relationship"
                                                                                   name="relationship" id="owner_relationship"
                                                                                   placeholder="Relationship with company"
                                                                                   :required="requiredFields.owner_relationship">
                                                                            <div class="invalid-feedback">
                                                                                {{ fieldErrors.owner_relationship.message }}
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </form>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mt-2">

                            <div class="col-3 mt-4">
                                <div class=" text-label">Is the {{formData.leadType}} with you?</div>
                            </div> <div class="col-3 mt-4">
                                    <div class="btn-group" role="group1" aria-label="Basic radio toggle button group1">
                                        <input type="radio" class="btn-check"
                                               name="is_deal_yours" id="leadWith1"
                                               value="1"
                                               v-model="formData.is_deal_yours"
                                               checked="checked">
                                        <label class="btn btn-sm btn-outline-secondary"
                                               for="leadWith1">
                                            Yes</label>
                                        <input type="radio" class="btn-check"
                                               id="leadWith2"
                                               v-model="formData.is_deal_yours"
                                               name="is_deal_yours"
                                               value="0">
                                        <label class="btn btn-sm btn-outline-secondary"
                                               for="leadWith2">No</label>
                                    </div>






                            </div>

                            <div class="col-md-6">
                                <div class="form-group" v-if="formData.is_deal_yours === '0'">
                                    <label class="text-label" for="deal_type">Please select an agent representing the {{formData.leadType}}.</label>
                                    <UserSelect @userSelected="userSelected"
                                                @userSelectionCleared="userCleared"
                                                :roles="['Agent']" :passedUsers="[]"
                                                :multiple="false"
                                    />
                                    <div class="invalid-feedback" id="otherAgentError" :style="{ display: otherAgentError ? 'block' : 'none' }">
                                        {{ fieldErrors.other_agent_id.message }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" id="dealCrateModalCloseBtn" data-bs-dismiss="modal">Close</button>
                        <button type="submit"
                        id="dealCreateFormSubmitBtn"

                        class="btn btn-success action-save float-end" @click="onSubmit(true)">
                                <span v-if="showBtnLoaderSave" class="d-flex align-items-center">
                                    <span class="spinner-border flex-shrink-0" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                    <span class="flex-grow-1 ms-2">
                                        Loading...
                                    </span>
                                </span>
                            <span v-else>Save & Next</span>
                        </button>

                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
</template>
<script setup>
import {onMounted, ref, watch} from "vue";
import UserSelect from "../../../components/formElements/UserSelect.vue";
import VueMultiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';
import CardPlaceholder from "../../../utilities/placeholder/CardPlaceholder.vue";
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';
//import assets/libs/intl-tel-input/js/intlTelInput.min.js
import '../../../../../public/assets/libs/intl-tel-input/js/intlTelInput.min.js'
import useDeals from "../../deals/composables/deals";
import useGlobalHelper from "../../../composables/helpers/global";
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/material_blue.css';
import axios from "axios";
const emit = defineEmits(['modalRemoved']);
const {getCountries, countries} = useGlobalHelper();
const {
    updateContact,
    contact,
    getContact,
    errors, property, getProperty
} = useDeals();
const ownerSectionPlaceholder = ref(false);
const owner = ref({});
const showBtnLoaderSave = ref(false);
const modal = ref(null);
const props = defineProps({
    property_id: {
        type: String,
        required: true,
    },
    owner_id: {
        type: String,
        required: true,

    }
});
const genders = [
    'Male', 'Female', 'Unknown'
];

const transactionTypes = [
    'Cash to Cash', 'Cash to Mortgage', 'Mortgage to Cash', 'Mortgage to Mortgage', 'Rental'
]
const otherAgentError = ref(false);
const formData = ref({
    property_id: props.property_id,
    is_deal_yours: '1',
    other_agent_id: '',
    leadType: 'buyer',
    transaction_type: '',
    //owner details will be here
    owner_first_name: '',
    owner_last_name: '',
    owner_mobile: '',
    owner_phone: '',
    owner_whatsapp: '',
    owner_email: '',
    owner_gender: '',
    owner_nationality_id: '',
    owner_id: props.owner_id ? props.owner_id : '',
    owner_type: 'INDIVIDUAL',
    owner_company: '',
    owner_trade_license: '',
    owner_trade_license_expiry: '',
    owner_trade_license_name: '',
    owner_aliases: '',
    owner_address: '',
    owner_dob: '',
    owner_pob: '',
    owner_passport: '',
    owner_passport_issue_date: '',
    owner_passport_expiry: '',
    owner_passport_country_id: '',
})
const userSelected = (value) => {
       formData.value.other_agent_id = value.id;
}
const userCleared = (value) =>{
    formData.value.other_agent_id = '';

    console.log(formData.value.is_deal_yours)
    if(formData.value.is_deal_yours === '1' || formData.value.is_deal_yours === 1){
        console.log('user is cleared and deal is yours')
        requiredFields.value.other_agent_id = false;
        fieldErrors.value.other_agent_id.error = false;
    }else{
        console.log('user is cleared and deal is others')
        requiredFields.value.other_agent_id = true;
        fieldErrors.value.other_agent_id.error = true;
        otherAgentError.value = true;
        //
        // let otherAgentError = document.getElementById('otherAgentError');
        // otherAgentError.style.display = 'block';
    }
}

const requiredFields = ref({
    other_agent_id: false,
    owner_first_name: true,
    owner_last_name: true,
    owner_mobile: true,
    owner_phone: false,
    owner_whatsapp: true,
    owner_email: true,
    owner_gender: true,
    owner_nationality_id: true,
    owner_company: false,
    owner_trade_license: false,
    owner_trade_license_expiry: false,
    owner_trade_license_name: false,
    owner_relationship: false,
    owner_aliases: false,
    owner_address: false,
    owner_dob: false,
    owner_pob: false,
    owner_passport: true,
    owner_passport_issue_date: false,
    owner_passport_expiry: false,
    owner_passport_country_id: false,
    transaction_type: false,

});
const fieldErrors = ref({
    other_agent_id: {
        error: false,
        message: "Please select an agent"
    },
    owner_company: {
        error: false,
        message: "Please enter company name"
    },
    owner_trade_license: {
        error: false,
        message: "Please enter trade license number"
    },
    owner_trade_license_name: {
        error: false,
        message: "Please enter company trade name"
    },
    owner_trade_license_expiry: {
        error: false,
        message: "Please select trade license expiry date"
    },
    owner_relationship: {
        error: false,
        message: "Please enter relationship with company"
    },

    owner_first_name: {
        error: false,
        message: "Please enter first name"
    },
    owner_last_name: {
        error: false,
        message: "Please enter last name"
    },
    owner_aliases: {
        error: false,
        message: "Please enter aliases"
    },
    owner_address: {
        error: false,
        message: "Please enter address"
    },
    owner_dob: {
        error: false,
        message: "Please enter date of birth"
    },
    owner_pob: {
        error: false,
        message: "Please enter place of birth"
    },
    owner_gender: {
        error: false,
        message: "Please select gender"
    },
    owner_nationality_id: {
        error: false,
        message: "Please select nationality"
    },

    emirates_id_copy: {
        error: false,
        message: "Please select emirates ID copy"
    },

    owner_mobile: {
        error: false,
        message: "Please enter mobile number"
    },
    owner_whatsapp: {
        error: false,
        message: "Please enter whatsapp number"
    },
    owner_phone: {
        error: false,
        message: "Please enter phone number"
    },
    owner_email: {
        error: false,
        message: "Please enter email"
    },
    owner_passport: {
        error: false,
        message: "Please enter passport number"
    },
    owner_passport_issue_date: {
        error: false,
        message: "Please select passport issue date"
    },
    owner_passport_expiry: {
        error: false,
        message: "Please select passport expiry date"
    },
    owner_passport_country_id: {
        error: false,
        message: "Please select passport issuing country"
    },
    transaction_type: {
        error: false,
        message: "Please select a transaction type"
    },
})
watch(() => contact.value, (val) => {

    if (val) {
        owner.value = val;
        if (val.contact_meta != null) {
            for (let [key, value] of Object.entries(val.contact_meta)) {
                if ('passport_country_id' == value.meta_key) {
                    owner.value.passport_country_id = value.meta_value != null ? parseInt(value.meta_value) : null;
                } else {
                    owner.value[value.meta_key] = value.meta_value;
                }


            }
        }

        formData.value.owner_id = val.id;
        formData.value.owner_type = (owner.type !== null && owner.type !=='' && owner.type !== undefined) ? owner.type : 'INDIVIDUAL';
        formData.value.owner_first_name = val.first_name;
        formData.value.owner_last_name = val.last_name;
        formData.value.owner_aliases = val.aliases;
        formData.value.owner_mobile = val.mobile;
        formData.value.owner_phone = val.phone;
        formData.value.owner_whatsapp = val.whatsapp;
        formData.value.owner_email = val.email;
        formData.value.owner_gender = val.gender;
        formData.value.owner_nationality_id = val.nationality_id;
        formData.value.owner_company = val.company;
        formData.value.owner_trade_license = owner.company_trade_license_no;
        formData.value.owner_trade_license_expiry = owner.trade_license_expiry;
        formData.value.owner_relationship = owner.owner_relationship
        formData.value.owner_address = val.address;
        formData.value.owner_dob = val.dob;
        formData.value.owner_pob = owner.value.pob;
        formData.value.owner_passport = val.passport;
        formData.value.owner_passport_issue_date = val.passport_issue_date;
        formData.value.owner_passport_expiry = val.passport_expiry;
        formData.value.owner_passport_country_id = val.passport_country_id;
    }
})

watch(() => formData.value.is_deal_yours, (val) => {

   if(val === '1' || val === 1){
       formData.value.other_agent_id = '';
       requiredFields.value.other_agent_id = false;
       fieldErrors.value.other_agent_id.error = false;
   }else{
       requiredFields.value.other_agent_id = true;
       fieldErrors.value.other_agent_id.error = true;
       otherAgentError.value = true;
   }
}
)
watch(() => formData.value.other_agent_id, (val) => {

   if((val === '' || val === null || val === undefined) && formData.value.is_deal_yours === '1'){
       requiredFields.value.other_agent_id = true;
       fieldErrors.value.other_agent_id.error = true;
       otherAgentError.value = true;
   }else{
       otherAgentError.value = false;
       requiredFields.value.other_agent_id = false;
       fieldErrors.value.other_agent_id.error = false;

   }
}
)
watch(() => formData.value.owner_type, (owner_type) => {

   if(owner_type === 'COMPANY'){
       requiredFields.value.owner_company = true;
       requiredFields.value.owner_trade_license = true;
       requiredFields.value.owner_relationship = true;
       requiredFields.value.owner_trade_license_expiry = true;
       requiredFields.value.owner_trade_license_name = true;

   }else{
       requiredFields.value.owner_company = false;
       requiredFields.value.owner_trade_license = false;
       requiredFields.value.owner_relationship = false;
       requiredFields.value.owner_trade_license_expiry = false;
       requiredFields.value.owner_trade_license_name = false;
   }
}
)
watch(() => property.value, (val) => {
    if (val) {
        let type = 'Buyer';


       if(val.for == 'Residential for sale' || val.for == 'Commercial for sale') {
           if (val.completion_status == 'Ready Primary' || val.completion_status == 'Off-Plan Primary') {
               type = 'Primary Resale';
           } else {
               type = 'Secondary Sale';
           }
           formData.value.leadType  = 'buyer';
           requiredFields.value.transaction_type = true;
       } else if (val.for == 'Residential for rent' || val.for == 'Commercial for rent') {
           type = 'Rental';

           formData.value.leadType  = 'tenant';
           requiredFields.value.transaction_type = false;
       }


    }
})
const resetFieldErrors = () => {
    Object.keys(fieldErrors.value).map(key => {
        fieldErrors.value[key].error = false;
    })
}
const onSubmit = async () => {
    resetFieldErrors();
        const isInvalid = Object.keys(formData.value).map(key => {
            if(requiredFields.value[key] != undefined){

                if(requiredFields.value[key] == true && (formData.value[key] == "" || formData.value[key] == null)){
                    fieldErrors.value[key].error = true
                    return true;
                }
                fieldErrors.value[key].error = false
                return false
            }
            return false;
        }).filter(item => item).length > 0;
        console.log(otherAgentError.value)
    if((formData.value.other_agent_id ==='' || formData.value.other_agent_id === null) && (formData.value.is_deal_yours === '0')){
        requiredFields.value.other_agent_id = true;
        fieldErrors.value.other_agent_id.error = true;
        otherAgentError.value = true;
        return true;
    }
        if(isInvalid){
            return true;
        }
        //dealCreateFormSubmitBtn disable button
        document.getElementById('dealCreateFormSubmitBtn').disabled = true;

        showBtnLoaderSave.value = true;
    await axios.post(route('admin.work.deals.create.from.listing'), formData.value).then(function(response){
        let data = response.data;

      if(data.status === '1') {
          showToastify(data.msg, "success")
          let redirect_url = data.redirect_url;

          if (redirect_url !== null && redirect_url !== undefined && redirect_url !== '') {
             window.location.href = redirect_url;
          }else{
                emit('modalRemoved');
          }
          //click on button #dealCrateModalCloseBtn
            document.getElementById('dealCrateModalCloseBtn').click();
         // $('#dealCreateFormModal').modal('hide');
      }else{
        document.getElementById('dealCreateFormSubmitBtn').disabled = false;
            showToastify(data.msg, "error")
      }
    }).catch(function(error){
        console.log(error);
        document.getElementById('dealCreateFormSubmitBtn').disabled = true;
        if (error.response && error.response.status === 422) {
            // Handle validation errors
            let validationErrors = error.response.data.errors;

            for (let field in validationErrors) {
                if (validationErrors.hasOwnProperty(field)) {
                    let errorMessages = validationErrors[field];
                    errorMessages.forEach(errorMessage => {
                            fieldErrors.value[field].error = true
                            fieldErrors.value[field].message =  errorMessage

                    });
                }
            }

        }else  if (error.response && error.response.status === 500) {
            alert('An error occurred while processing your request. Please try again later.');
        } else {
            // Handle other errors
            console.error('An error occurred:', error.message);
        }
    });

    if (errors.value) {
        console.error('Validation error:', errors.value);
    }
    // await updateContactDocuments(formData1);
    setTimeout(() => {
        //getContact(owner.value.id);
        // showBtnLoader.value = false;
        showBtnLoaderSave.value = false;
    }, 1000);
}
const removeModal = () => {
    modal.value.hide();
    emit('modalRemoved');
}
const handleMobileNumber = (InputField) =>{
    let contactMobileInput = initPhoneValidator(InputField)
    var contactMobileInputElement = document.querySelector("#"+InputField);
    var contactMobileIti = window.intlTelInput(contactMobileInputElement, {
        initialCountry: "ae",
    });
    contactMobileIti.getNumber()

    contactMobileInputElement.addEventListener("countrychange", function() {
        var selectedCountryData = contactMobileIti.getSelectedCountryData();
        var countryCode = selectedCountryData.dialCode;
        var countryFlag = selectedCountryData.iso2;


        if (contactMobileIti.isValidNumber()) {
            $(".contactMobileValidation").html("");
            $('input[name='+InputField+']').val(contactMobileIti.getNumber());
        } else {
            $(".contactMobileValidation").html("Please enter a valid mobile number with country code");
        }
    });
}
onMounted(() => {
    if (props.owner_id) {
        getContact(props.owner_id);
    }
    getCountries();
    getProperty(props.property_id);
    //$('#myModal').modal('show');
    const modalEl = document.getElementById("dealCreateFormModal");
    document.body.appendChild(modalEl);
    modal.value = new bootstrap.Modal(modalEl, {
        keyboard: false,
    });
    modal.value.show();
    handleMobileNumber('contact-mobile')
    handleMobileNumber('contact-whatsapp')
    handleMobileNumber('contact-phone')
})
</script>
<style scoped>
.btn-check:active+.btn-outline-secondary,
.btn-check:checked+.btn-outline-secondary,
.btn-outline-secondary.active,
.btn-outline-secondary.dropdown-toggle.show,
.btn-outline-secondary:active {
    background-color: #c48667;
    border-color: #c48667;
    color: #fff;
}

.btn-outline-secondary {
    border-color: #c48667;
    color: #c48667;
}

.btn-outline-secondary:hover {
    background-color: #c48667;
    border-color: #c48667;
    color: #fff;
}
.text-label{
    font-size: 0.8125rem !important;
    font-weight: 500 !important;
}

</style>
<style>
.vs--single .vs__selected {
    font-size: 13px !important;
}
.vs__selected-options .vs__search{
    font-size: 13px !important;
}
.vs__dropdown-toggle{
    height: 38px !important;
}
</style>
