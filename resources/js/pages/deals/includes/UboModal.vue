<template>
    <div id="uboModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success ">
                    <h5 class="modal-title text-white" id="myModalLabel">Add update UBO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="onSubmit" enctype="multipart/form-data">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-2 col-sm-12 mt-2">First Name*
                        </div>
                        <div class="col-md-4 col-sm-12 mt-2">
                            <input type="text" class="form-control inputs"
                                   name="first_name"
                                   id="first_name"
                                   v-model="formData.first_name"
                                   placeholder="Enter first name" required>
                            <div class="invalid-feedback"> Please enter
                                first name.
                            </div>
                        </div>


                        <div class="col-md-2  col-sm-12 mt-2">Last Name
                        </div>
                        <div class="col-md-4  col-sm-12 mt-2">
                            <input type="text" class="form-control inputs"
                                   name="last_name"
                                   id="last_name"
                                   v-model="formData.last_name"
                                   placeholder="Enter last name">
                            <div class="invalid-feedback"> Please enter last
                                name.
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12 mt-2">Aliases (If any)</div>
                        <div class="col-md-4 col-sm-12 mt-2"
                             id="mobileDiv1">
                            <input type="tel"
                                   class="form-control pl-40 inputs"
                                   name="mobile"
                                   id="mobile"
                                   v-model="formData.alias_name"
                                   placeholder="Enter aliases">


                        </div>
                        <div class="col-md-2 col-sm-12 mt-2">Address</div>
                        <div class="col-md-4 col-sm-12 mt-2">
                            <input type="text" class="form-control inputs"
                                   name="address"
                                   v-model="formData.address"

                                   id="address" placeholder="Enter address">
                            <div class="invalid-feedback"> Please enter
                                address.
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12 mt-2">Mobile*</div>
                        <div class="col-md-4 col-sm-12 mt-2"
                             id="mobileDiv1">
                            <input type="tel"
                                   class="form-control pl-40 inputs"
                                   name="mobile"
                                   id="mobile" required
                                   v-model="formData.mobile"
                                   placeholder="Enter mobile">
                            <div class="text-danger mobileValidation">
                            </div>

                            <div class="invalid-feedback"> Please enter a
                                valid mobile number.
                            </div>
                            <div class="text-danger"
                                 id="mobile-exists"></div>
                        </div>
                        <div class="col-md-2 col-sm-12 mt-2">Telephone Number*</div>
                        <div class="col-md-4 col-sm-12 mt-2"
                             id="mobileDiv1">
                            <input type="tel"
                                   class="form-control pl-40 inputs"
                                   name="phone"
                                   id="phone"
                                   v-model="formData.phone"
                                   placeholder="Enter phone number">
                            <div class="text-danger mobileValidation">
                            </div>

                            <div class="invalid-feedback"> Please enter a
                                valid mobile number.
                            </div>
                            <div class="text-danger"
                                 id="mobile-exists"></div>
                        </div>

                        <div class="col-md-2 col-sm-12 mt-2">Email</div>
                        <div class="col-md-4 col-sm-12 mt-2">
                            <input type="email" class="form-control inputs"
                                   name="email"
                                   id="email"
                                   v-model="formData.email"
                                   placeholder="Enter email address">
                            <div class="invalid-feedback"> Please enter a
                                valid email address.
                            </div>
                            <div class="text-danger"
                                 id="email-exists"></div>
                        </div>
                        <div class="col-6"></div>

                    </div>

                    <Transition>
                        <div class="row">
                            <div class="col-md-2  col-sm-12 mt-2">Nationality
                            </div>
                            <div class="col-md-4  col-sm-12 mt-2">

                                <v-select
                                    v-model="formData.nationality_id"
                                    :reduce="country => country.id"
                                    :options="countries"
                                    label="nationality"
                                    @search="countries"
                                    placeholder="Type to search countries"
                                ></v-select>
                                <div class="invalid-feedback"> Please choose
                                    nationality.
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12 mt-2">Gender</div>
                            <div class="col-md-4 col-sm-12 mt-2">
                                <select class="form-select inputs"
                                        name="gender"
                                        id="gender"
                                        v-model="formData.gender"
                                >
                                    <option selected disabled value="">Select
                                        gender
                                    </option>
                                    <option :selected="formData.gender === 'Male'" value="Male">Male
                                    </option>
                                    <option :selected="formData.gender === 'Female'" value="Female">
                                        Female
                                    </option>
                                    <option :selected="formData.gender === 'Unknown'" value="Unknown">
                                        Unknown
                                    </option>
                                </select>


                                <div class="invalid-feedback"> Please choose
                                    gender.
                                </div>
                            </div>
                        </div>
                    </Transition>
<!--                            <div class="col-md-2 col-sm-12 mt-2">Date of birth-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2">-->

<!--                                <flat-pickr data-date-format="d/m/Y"-->
<!--                                            placeholder="Enter date of birth"-->
<!--                                            class="form-control"-->
<!--                                            name="dob" v-model="formData.dob"/>-->
<!--                                <div class="invalid-feedback"> Please enter date-->
<!--                                    of birth.-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-md-2 col-sm-12 mt-2">Place of birth-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2">-->
<!--                                <input type="text" class="form-control inputs"-->
<!--                                       v-model="formData.place_of_birth"-->
<!--                                       placeholder="Enter place of birth">-->
<!--                                <div class="invalid-feedback"> Please enter date-->
<!--                                    of birth.-->
<!--                                </div>-->
<!--                            </div>-->


<!--                            <div class="col-md-2 col-sm-12 mt-2">Passport-->
<!--                                number-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2">-->
<!--                                <input-->
<!--                                    v-model="lead['passport_number']"-->
<!--                                    type="text" class="form-control inputs"-->
<!--                                    name="passport_number" id="passport_number"-->
<!--                                    placeholder="Enter passport number">-->
<!--                                <div class="invalid-feedback"> Please enter-->
<!--                                    passport number.-->
<!--                                </div>-->
<!--                            </div>-->


<!--                            <div class="col-md-2 col-sm-12 mt-2">Occupation</div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2"><input-->
<!--                                v-model="lead['occupation']"-->
<!--                                type="text" class="form-control inputs"-->
<!--                                name="occupation" id="occupation" placeholder="Enter occupation">-->
<!--                                <div class="invalid-feedback"> Please enter-->
<!--                                    occupation.-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-md-2 col-sm-12 mt-2">Passport-->
<!--                                issue date-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2">-->
<!--                                <flat-pickr data-date-format="d/m/Y"-->
<!--                                            placeholder="Enter passport expiry date"-->
<!--                                            class="form-control" name="passport_expiry"-->
<!--                                            id="passport_expiry"-->
<!--                                            v-model="lead['passport_expiry']"/>-->
<!--                                <div class="invalid-feedback"> Please enter-->
<!--                                    passport expiry date.-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-md-2 col-sm-12 mt-2">Passport-->
<!--                                expiry date-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2">-->
<!--                                <flat-pickr data-date-format="d/m/Y"-->
<!--                                            placeholder="Enter passport expiry date"-->
<!--                                            class="form-control" name="passport_expiry"-->
<!--                                            id="passport_expiry"-->
<!--                                            v-model="lead['passport_expiry']"/>-->
<!--                                <div class="invalid-feedback"> Please enter-->
<!--                                    passport expiry date.-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-md-2 col-sm-12 mt-2">UAE National-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2">-->
<!--                                <div class="form-check form-check-inline">-->
<!--                                    <input class="form-check-input" type="radio" name="uae_national"-->
<!--                                           id="inlineRadio1" value="1"-->
<!--                                           :checked="lead.uae_national == '1'"-->
<!--                                           v-model="lead.uae_national"-->
<!--                                    >-->
<!--                                    <label class="form-check-label" for="inlineRadio1">Yes</label>-->
<!--                                </div>-->
<!--                                <div class="form-check form-check-inline">-->
<!--                                    <input class="form-check-input" type="radio"-->
<!--                                           v-model="lead.uae_national"-->
<!--                                           name="uae_national"-->
<!--                                           :checked="lead.uae_national == '0'"-->
<!--                                           id="inlineRadio2" value="0">-->
<!--                                    <label class="form-check-label" for="inlineRadio2">No</label>-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                    </Transition>-->
<!--                    <Transition>-->
<!--                        <div class="row"-->
<!--                             v-if="(lead.uae_national == '1' && lead.type === 'INDIVIDUAL' )">-->
<!--                            <div class="col-md-2 col-sm-12 mt-2 d-none">Emirates ID-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2 d-none">-->
<!--                                <input-->
<!--                                    v-model="lead.emirates_id"-->
<!--                                    type="text" class="form-control inputs"-->
<!--                                    name="emirates_id" id="emirates_id"-->
<!--                                    placeholder="Enter Emirates ID">-->
<!--                                <div class="invalid-feedback"> Please enter-->
<!--                                    Emirates ID-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-md-2 col-sm-12 mt-2 d-none">Emirates id-->
<!--                                expiry-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2 d-none">-->
<!--                                <flat-pickr data-date-format="d/m/Y"-->
<!--                                            class="form-control" name="emirates_id_expiry"-->
<!--                                            placeholder="Enter Emirates ID Expiry"-->
<!--                                            id="passport_expiry"-->
<!--                                            v-model="lead.emirates_id_expiry"/>-->
<!--                                <div class="invalid-feedback"> Please enter-->
<!--                                    Emirates ID expiry.-->
<!--                                </div>-->
<!--                            </div>-->


<!--                            <div class="col-md-2 col-sm-12 mt-2">Country of-->
<!--                                residence-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2"><input-->
<!--                                v-model="lead.country_of_residence"-->
<!--                                type="text" class="form-control inputs"-->
<!--                                name="country_of_residence" id="country_of_residence"-->
<!--                                placeholder="Enter country of residence">-->
<!--                                <div class="invalid-feedback"> Please enter-->
<!--                                    country of residence.-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-md-2 col-sm-12 mt-2">Emirates ID copy-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2"><input-->
<!--                                type="file" class="form-control inputs"-->
<!--                                name="emirates_id_copy" id="emirates_id_copy"-->
<!--                                placeholder="Enter country of residence">-->
<!--                                <div class="invalid-feedback"> Please select am emirat id-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </Transition>-->
<!--                    <transition>-->
<!--                        <div class="row">-->
<!--                            <div class="col-md-2 col-sm-12 mt-2">UAE Residency-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2">-->
<!--                                <div class="form-check form-check-inline">-->
<!--                                    <input class="form-check-input" type="radio" name="uae_resident"-->
<!--                                           id="inlineRadio1" value="1"-->
<!--                                           :checked="lead.uae_resident == '1'"-->
<!--                                           v-model="lead.uae_resident"-->
<!--                                    >-->
<!--                                    <label class="form-check-label" for="inlineRadio1">Yes</label>-->
<!--                                </div>-->
<!--                                <div class="form-check form-check-inline">-->
<!--                                    <input class="form-check-input" type="radio"-->
<!--                                           v-model="lead.uae_resident"-->
<!--                                           name="uae_resident"-->
<!--                                           :checked="lead.uae_resident == '0'"-->
<!--                                           id="inlineRadio2" value="0">-->
<!--                                    <label class="form-check-label" for="inlineRadio2">No</label>-->
<!--                                </div>-->

<!--                            </div>-->
<!--                        </div>-->
<!--                    </Transition>-->
<!--                    <transition>-->
<!--                        <div class="row" v-if="lead.uae_resident == '1'">-->
<!--                            <div class="col-md-2 col-sm-12 mt-2">Residency Visa No.-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2"><input-->
<!--                                v-model="lead.residency_visa_no"-->
<!--                                type="text" class="form-control inputs"-->
<!--                                name="residency_visa_no" id="residency_visa_no"-->
<!--                                placeholder="Enter residency visa no.">-->
<!--                                <div class="invalid-feedback"> Please enter residency visa no.-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-md-2 col-sm-12 mt-2">Emirates ID No.-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2"><input-->
<!--                                v-model="lead.emirates_id"-->
<!--                                type="text" class="form-control inputs"-->
<!--                                name="emirates_id" id="emirates_id"-->
<!--                                placeholder="Enter Emirates id.">-->
<!--                                <div class="invalid-feedback"> Please enter Emirates id no.-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-md-2 col-sm-12 mt-2">Visa expiry date-->
<!--                            </div>-->
<!--                            <div class="col-md-4 col-sm-12 mt-2"><input-->
<!--                                v-model="lead.visa_expiry_date"-->
<!--                                type="text" class="form-control inputs"-->
<!--                                name="visa_expirty_date" id="visa_expirty_date"-->
<!--                                placeholder="Enter visa expiry date.">-->
<!--                                <div class="invalid-feedback"> Please enter Visa expirty date.-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->

<!--                    </transition>-->


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary ">Save Changes</button>
            </div>
                </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</template>
<script setup>
import {ref, inject} from 'vue'
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/material_blue.css';

const countries = inject('countries');
const emit = defineEmits(['uboadded']);
import useDeals from "../composables/deals";
import useLeads from "../../../pages/leads/composables/leads";
import axios from "axios";
const {leads} = useLeads();
const errors = ref({});
const props = defineProps({
    lead: {
        type: Object,
        required: false
    },
    ubo : {
        type: Object,
        required : false
    }

});
const formData = ref({
    first_name : '',
    last_name : '',
    alias_name : '',
    address : '',
    mobile : '',
    phone : '',
    email : '',
    nationality_id : '',
    lead_id : props.lead.id
});
const storeLeadUbo = async (data) => {
    errors.value = ''
    try {
        const config = {
            headers: {
                'Content-type': 'multipart/form-data'
            }
        }
        let response = await axios.post('/api/vue/v1/store-lead-ubo-store', data).then(function(response){

            if(response.data.status){
                emit('uboadded');
                $('#uboModal').modal('hide');
                showToastify('UBO profile saved successfully', 'success');
            }else{
                showToastify('Something went wrong', 'error')
            }
            return response;
        }).catch(function(){
            return response;
            //console.log('FAILURE!!');
        });

        return response;


    } catch (e) {
        if (e.response.status === 422) {
            for (const key in e.response.data.errors) {
                errors.value = e.response.data.errors
            }
        }
        return e;
    }

}
const onSubmit = () => {
    formData.value.lead_id = props.lead.id;
    storeLeadUbo(formData.value);

}
</script>
