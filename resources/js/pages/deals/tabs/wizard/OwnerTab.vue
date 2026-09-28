<template>
    <section id="wizard-p-0" role="tabpanel" aria-labelledby="wizard-h-0" class="body current" aria-hidden="false" style="">
        <div v-show="placeholder" class="row">

            <div class="col-12">
                <CardPlaceholder></CardPlaceholder>
            </div>
        </div>
        <div v-if="owner && !placeholder">
            <form @submit.prevent="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12">
                        <div class="border d-none">
                            <ul class="nav nav-pills custom-hover-nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation" style="width: 50%;">
                                    <a style="min-width: 200px; width: 100%;" href="#custom-hover-customere"
                                        data-bs-toggle="tab" aria-expanded="false" class="nav-link active"
                                        aria-selected="true" role="tab">
                                        <i class="ri-user-fill nav-icon nav-tab-position"></i>
                                        <h5 class="nav-titl nav-tab-position m-0">Information</h5>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" style="width: 50%;">
                                    <a style="min-width: 200px;width: 100%;" href="#documents-tab-pane" data-bs-toggle="tab"
                                        aria-expanded="true" class="nav-link" aria-selected="false" role="tab"
                                        tabindex="-1">
                                        <i class="ri-file-text-line nav-icon nav-tab-position"></i>
                                        <h5 class="nav-titl nav-tab-position m-0">Documents</h5>
                                    </a>
                                </li>

                            </ul>
                        </div>
                        <div class="card-body" style=" min-height: 500px;">
                            <div class="tab-content text-muted">
                                <div class="tab-pane active show" id="custom-hover-customere" role="tabpanel">
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-center">

                                            <!-- Radio Buttons -->
                                            <div class="btn-group" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check" name="btnradio" id="btnradio1"
                                                    value="INDIVIDUAL" :checked="owner.type == 'INDIVIDUAL'"
                                                    v-model="owner.type" autocomplete="off" checked="">
                                                <label class="btn btn-sm btn-outline-secondary" for="btnradio1"><i
                                                        class="ri-user-2-line" style="vertical-align: sub;"></i>
                                                    Individual</label>

                                                <input type="radio" class="btn-check" id="btnradio2" v-model="owner.type"
                                                    name="btnradio" :checked="owner.type == 'COMPANY'" value="COMPANY"
                                                    autocomplete="off">
                                                <label class="btn btn-sm btn-outline-secondary" for="btnradio2">Company <i
                                                        class="ri-team-line" style="vertical-align: bottom;"></i></label>
                                            </div>
                                        </div>

                                        <!-- <div class="col-2"></div> -->



                                    </div>
                                    <div class="row mt-2" v-if="owner.type == 'COMPANY'">

                                        <div class="col-2">Company Name</div>
                                        <div class="col-4">
                                            <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.company.error}" v-model="owner.company" name="company"
                                                id="company" placeholder="Enter company" :required="requiredFields.company">
                                                <div class="invalid-feedback">
                                                {{ fieldErrors.company.message }}
                                            </div>
                                        </div>
                                        <div class="col-2">Trade License</div>
                                        <div class="col-4">
                                            <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.trade_license.error}" v-model="owner.trade_license"
                                                name="trade_license" id="first_name" placeholder="Enter trade license"
                                                :required="requiredFields.trade_license">
                                            <div class="invalid-feedback">
                                                {{ fieldErrors.trade_license.message }}
                                            </div>
                                        </div>
                                        <div class="col-2 mt-2">Expiry Date of License</div>
                                        <div class="col-4 mt-2">
                                            <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.trade_license_expiry.error}" v-model="owner.trade_license_expiry"
                                                name="trade_license_expiry" id="trade_license_expiry"
                                                placeholder="Enter trade license expiry" :required="requiredFields.trade_license_expiry">
                                                <div class="invalid-feedback">
                                                {{ fieldErrors.trade_license_expiry.message }}
                                            </div>
                                        </div>
                                        <hr>
                                    </div>

                                    <div class="row">

                                        <div class="col-6">

                                            <div class="row">
                                                <div class="col-4">First Name</div>
                                                <div class="col-8">
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.first_name.error}"
                                                        v-model="owner.first_name" name="first_name" id="first_name"
                                                        placeholder="Enter first name" :required="requiredFields.first_name">
                                                    <div class="invalid-feedback">
                                                        {{ fieldErrors.first_name.message }}
                                                    </div>
                                                </div>

                                                <div class="col-4 mt-2">Last Name</div>
                                                <div class="col-8 mt-2">
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.last_name.error}" v-model="owner.last_name"
                                                        name="last_name" id="last_name" placeholder="Last name" :required="requiredFields.last_name">
                                                        <div class="invalid-feedback">
                                                        {{ fieldErrors.last_name.message }}
                                                    </div>
                                                </div>
                                                <div class="col-4 mt-2">Aliases (If any)</div>
                                                <div class="col-8 mt-2">
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.aliases.error}" v-model="owner.aliases"
                                                        name="aliases" id="aliases" placeholder="Aliases name if any" :required="requiredFields.aliases">
                                                        <div class="invalid-feedback">
                                                        {{ fieldErrors.aliases.message }}
                                                    </div>
                                                </div>

                                                <div class="col-4 mt-2">Residential Address</div>
                                                <div class="col-8 mt-2">
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.address.error}" v-model="owner.address"
                                                        name="address" id="address" placeholder="Enter address" :required="requiredFields.address">
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.address.message }}
                                                        </div>
                                                </div>

                                                <div class="col-4 mt-2">Date Of Birth</div>
                                                <div class="col-8 mt-2">
                                                    <flat-pickr data-date-format="d/m/Y" placeholder="Enter date of birth"
                                                        class="form-control" :class="{ 'is-invalid': fieldErrors.dob.error}" name="dob" id="dob" v-model="owner.dob" :required="requiredFields.dob"/>
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.dob.message }}
                                                        </div>
                                                </div>
                                                <div class="col-4 mt-2">Place Of Birth</div>
                                                <div class="col-8 mt-2">
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.pob.error}" v-model="owner.pob" name="pob"
                                                        id="pob" placeholder="Place of birth" :required="requiredFields.pob">
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.pob.message }}
                                                        </div>
                                                </div>

                                                <div class="col-4 mt-2">Gender</div>
                                                <div class="col-8 mt-2">
                                                    <VueMultiselect v-model="owner.gender" :allowEmpty="true"
                                                        :searchable="true" :close-on-select="true" :show-label="true"
                                                        :options="genders" :multiple="false" :selectLabel="''" :class="{ 'is-invalid': fieldErrors.gender.error}" :required="requiredFields.gender">
                                                    </VueMultiselect>
                                                    <div class="invalid-feedback">
                                                            {{ fieldErrors.gender.message }}
                                                        </div>
                                                </div>
                                                <div class="col-4 mt-2">Nationality</div>
                                                <div class="col-8 mt-2" v-if="countries_list">
                                                    <v-select v-model="owner.nationality_id" :reduce="country => country.id"
                                                        :options="countries" label="nationality" @search="countries"
                                                        placeholder="Type to search countries" :required="requiredFields.nationality_id" :class="{ 'is-invalid': fieldErrors.nationality_id.error}"></v-select>
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.nationality_id.message }}
                                                        </div>
                                                </div>


                                                <div class="col-12 mt-2"></div>

                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-4">Mobile</div>
                                                <div class="col-8">
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.mobile.error}" v-model="owner.mobile"
                                                        name="first_name" id="first_name" placeholder="Mobile" :required="requiredFields.mobile">
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.mobile.message }}
                                                        </div>
                                                </div>
                                                <div class="col-4 mt-2">Whatsapp</div>
                                                <div class="col-8 mt-2">
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.whatsapp.error}" v-model="owner.whatsapp"
                                                        name="whatsapp" id="whatsapp" placeholder="Whatsapp number"
                                                        :required="requiredFields.whatsapp">
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.whatsapp.message }}
                                                        </div>
                                                </div>
                                                <div class="col-4 mt-2">Phone</div>
                                                <div class="col-8 mt-2">
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.phone.error}" v-model="owner.phone"
                                                        name="phone" id="phone" placeholder="Phone number" :required="requiredFields.phone">
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.phone.message }}
                                                        </div>
                                                </div>
                                                <div class="col-4 mt-2">Email</div>
                                                <div class="col-8 mt-2">
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.email.error}" v-model="owner.email"
                                                        name="email" id="email" placeholder="Email" :required="requiredFields.email">
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.email.message }}
                                                        </div>
                                                </div>

                                                <div class="col-4 mt-2">Passport</div>
                                                <div class="col-8 mt-2">
                                                    <input type="text" class="form-control" :class="{ 'is-invalid': fieldErrors.passport.error}" v-model="owner.passport"
                                                        name="passport" id="passport" placeholder="Enter passport number" :required="requiredFields.passport">
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.passport.message }}
                                                        </div>
                                                </div>
                                                <div class="col-4 mt-2">Passport Issue Date</div>
                                                <div class="col-8 mt-2">

                                                    <flat-pickr data-date-format="d/m/Y" placeholder="Passport issue date"
                                                        class="form-control" name="passport_issue_date" :class="{ 'is-invalid': fieldErrors.passport_issue_date.error}"
                                                        id="passport_issue_date" v-model="owner.passport_issue_date" :required="requiredFields.passport_issue_date"/>
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.passport_issue_date.message }}
                                                        </div>
                                                </div>
                                                <div class="col-4 mt-2">Passport Expiry</div>
                                                <div class="col-8 mt-2">
                                                    <flat-pickr data-date-format="d/m/Y" placeholder="Passport expiry date"
                                                        class="form-control" name="passport_expiry_date" :class="{ 'is-invalid': fieldErrors.passport_expiry.error}"
                                                        id="passport_expiry_date" v-model="owner.passport_expiry" :required="requiredFields.passport_expiry"/>
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.passport_expiry.message }}
                                                        </div>

                                                </div>
                                                <div class="col-4 mt-2">Passport Issuing Country</div>
                                                <div class="col-8 mt-2" v-if="countries_list">
                                                    <v-select v-model="owner.passport_country_id"
                                                        :reduce="country => country.id" :options="countries" label="name"
                                                        @search="countries"
                                                        placeholder="Type to search countries" :class="{ 'is-invalid': fieldErrors.passport_country_id.error}" :required="requiredFields.passport_country_id"></v-select>
                                                        <div class="invalid-feedback">
                                                            {{ fieldErrors.passport_country_id.message }}
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2 col-sm-12 mt-2">UAE National
                                        </div>
                                        <div class="col-md-4 col-sm-12 mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="uae_national"
                                                    id="inlineRadio1" value="1" :checked="owner.uae_national == '1'"
                                                    v-model="owner.uae_national" :class="{ 'is-invalid': fieldErrors.uae_national.error}">
                                                <label class="form-check-label" for="inlineRadio1">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" v-model="owner.uae_national"
                                                    name="uae_national" :checked="owner.uae_national == '0'"
                                                    id="inlineRadio2" value="0" :class="{ 'is-invalid': fieldErrors.uae_national.error}">
                                                <label class="form-check-label" for="inlineRadio2">No</label>
                                            </div>

                                        </div>

                                        <transition>
                                            <div class="col-md-6">
                                                <div class="row" v-if="owner.uae_national == '0'">
                                                    <div class="col-md-4 col-sm-12 mt-2">UAE Residency
                                                    </div>
                                                    <div class="col-md-8 col-sm-12 mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="uae_resident"
                                                                id="inlineRadio1" value="1"
                                                                :checked="owner.uae_residency == '1'"
                                                                v-model="owner.uae_residency" :class="{ 'is-invalid': fieldErrors.uae_residency.error}">
                                                            <label class="form-check-label" for="inlineRadio1">Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                v-model="owner.uae_residency" name="uae_resident"
                                                                :checked="owner.uae_residency == '0'" id="inlineRadio2"
                                                                value="0" :class="{ 'is-invalid': fieldErrors.uae_residency.error}">
                                                            <label class="form-check-label" for="inlineRadio2">No</label>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </Transition>
                                    </div>
                                    <Transition>
                                        <div class="row" v-if="(owner.uae_national == '1')">
                                            <div class="col-md-2 col-sm-12 mt-2 d-none">Emirates ID
                                            </div>
                                            <div class="col-md-4 col-sm-12 mt-2 d-none">
                                                <input v-model="owner.emirates_id" type="text" class="form-control inputs"
                                                    name="emirates_id" id="emirates_id" placeholder="Enter Emirates ID">
                                                <div class="invalid-feedback"> Please enter
                                                    Emirates ID
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-12 mt-2 d-none">Emirates id
                                                expiry
                                            </div>
                                            <div class="col-md-4 col-sm-12 mt-2 d-none">
                                                <flat-pickr type="date" data-date-format="d/m/Y" class="form-control"
                                                    name="emirates_id_expiry" placeholder="Enter Emirates ID Expiry"
                                                    id="passport_expiry" value="" v-model="owner.dob" />
                                                <div class="invalid-feedback"> Please enter
                                                    Emirates ID expiry.
                                                </div>
                                            </div>


                                            <div class="col-md-2 col-sm-12 mt-2 d-none">Emirates ID copy
                                            </div>
                                            <div class="col-md-4 col-sm-12 mt-2 d-none">
                                                <input type="file"
                                                    class="form-control inputs" name="emirates_id_copy"
                                                    id="emirates_id_copy" placeholder="Enter country of residence" :class="{ 'is-invalid': fieldErrors.emirates_id_copy.error}">
                                                <div class="invalid-feedback"> Please select am emirat id
                                                </div>
                                            </div>
                                        </div>
                                    </Transition>

                                </div>
                                <div class="tab-pane" id="documents-tab-pane" role="tabpanel">
                                    <div class="row mt-2">
                                        <div class="col-12 mt-2">
                                            <h4> Owner Related Documents</h4>

                                        </div>
                                        <!--                                        <div class="col-6" v-for="(item, index) in documents" :key="index">-->
                                        <!--                                            <div class="row">-->
                                        <!--                                                <div class="col-md-6 col-sm-12 mt-2">-->
                                        <!--                                                    {{ item }}-->
                                        <!--                                                </div>-->
                                        <!--                                                <div class="col-md-6 col-sm-12 mt-2">-->


                                        <!--                                                    <div class="input-group">-->
                                        <!--                                                        <input type="file" class="form-control fileInputs inputs"-->
                                        <!--                                                               :name="'document_'+index"-->
                                        <!--                                                               :id="'document_'+index"-->
                                        <!--                                                               @change="onFileChange(index,item)"-->
                                        <!--                                                               placeholder="Enter first name"-->
                                        <!--                                                               aria-label="Upload"-->
                                        <!--                                                        >-->

                                        <!--                                                        <a v-if="documentFromDb[index]"-->
                                        <!--                                                           :href="documentFromDb[index].path" target="_blank"-->
                                        <!--                                                           class="btn btn-outline-success btn-icon waves-effect waves-light"-->
                                        <!--                                                           style="padding: 0.3rem;"><i class="ri-eye-line"></i></a>-->
                                        <!--                                                        <button v-if="documentFromDb[index]"-->
                                        <!--                                                                class="btn btn-outline-danger btn-icon waves-effect waves-light"-->
                                        <!--                                                                style="padding: 0.3rem;" type="button"-->
                                        <!--                                                                @click="deleteDocument(documentFromDb[index].id, 'contact_media')"-->
                                        <!--                                                        ><i class=" ri-delete-bin-line"></i></button>-->

                                        <!--                                                    </div>-->
                                        <!--                                                </div>-->
                                        <!--                                            </div>-->
                                        <!--                                        </div>-->
                                        <div class="col-6" v-for="(item, index) in contact.documents" :key="index">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 mt-2">
                                                    {{ item.title }}
                                                </div>
                                                <div class="col-md-6 col-sm-12 mt-2">


                                                    <div class="input-group">
                                                        <input type="file" class="form-control fileInputs inputs"
                                                            :name="'document_' + index" :id="'document_' + index"
                                                            @change="onFileChange(index, item)"
                                                            placeholder="Enter first name" aria-label="Upload">

                                                        <a v-if="item" :href="item.path" target="_blank"
                                                            class="btn btn-outline-success btn-icon waves-effect waves-light"
                                                            style="padding: 0.3rem;"><i class="ri-eye-line"></i></a>
                                                        <button v-if="item"
                                                            class="btn btn-outline-danger btn-icon waves-effect waves-light"
                                                            style="padding: 0.3rem;" type="button"
                                                            @click="deleteDocument(item.id, 'contact_media')"><i
                                                                class=" ri-delete-bin-line"></i></button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-6" v-for="(file, index) in fileInputs" :key="index">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 mt-2">
                                                    <VueMultiselect :id="index" v-model="file.selectOption"
                                                        :allowEmpty="true" :searchable="true" :close-on-select="true"
                                                        :show-label="true" label="name" :options="fileTitleOptions"
                                                        :multiple="false" :taggable="true" :selectLabel="''" track-by="name"
                                                        @Select="dynamicDocTypeSelected($event, file)"
                                                        @tag="addDynamicDocType($event, file)">
                                                    </VueMultiselect>
                                                    <input type="hidden" :name="'document_dyn_title' + index"
                                                        :id="'document_dyn_title' + index" v-model="file.title"
                                                        placeholder="Title" />
                                                </div>
                                                <div class="col-md-6 col-sm-12 mt-2">
                                                    <input type="file" class="form-control fileInputs inputs"
                                                        :name="'document_dyn_' + index" :id="'document_dyn_' + index"
                                                        @change="onDynamicFileChange(index, file)" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-sm btn-ghost-success mt-2" type="button"
                                                @click="addMore">Add More
                                            </button>
                                        </div>

                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-12 mt-2">
                                            <h4> Property Documents</h4>

                                        </div>
                                    </div>
                                    <div class="row" v-if="property">
                                        <div class="col-3" v-for="(property_doc, index) in property.other_documents"
                                            :key="index">
                                            <div class="card">
                                                <div class="card-body">
                                                    <a :href="property_doc.path" target="_blank"> <img class="img-thumbnail"
                                                            alt="200x200" width="220"
                                                            src="https://themesbrand.com/velzon/html/default/assets/images/small/img-4.jpg"
                                                            data-holder-rendered="true">
                                                    </a>
                                                </div>
                                                <div class="card-footer">
                                                    {{ property_doc.title }}
                                                    <br>
                                                    <span class="text-muted">Type: {{ property_doc.type }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="actions clearfix mt-5">
                    <div class="row">
                        <div class="col-9"></div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-success action-save float-end" @click="onSubmit(true)">
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
                            <button type="submit" class="btn btn-success  float-end" @click="onSubmit(false)"
                                style="margin-right: 10px;">
                                <span v-if="showBtnLoader" class="d-flex align-items-center">
                                    <span class="spinner-border flex-shrink-0" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>

                                </span>
                                <span v-else>Save</span>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>
</template>
<script setup>
import { inject, onBeforeMount, onMounted, ref, watch } from "vue";
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';
import UserSelect from "../../../../components/formElements/UserSelect.vue";
import PropertySelect from "../../../../components/formElements/PropertySelect.vue";
import { useDealStore } from "../../store/dealState";
import useGlobalHelper from "../../../../composables/helpers/global";
import CardPlaceholder from "../../../../utilities/placeholder/CardPlaceholder";
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/material_blue.css';
import useDeals from "../../composables/deals";
import DynamicSelect from "vue-dynamic-select";
import VueMultiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

const {
    updateContact,
    contact,
    getContact,
    updateContactDocuments,
    deleteMedia,
    property,
    getProperty,
    data,
    errors
} = useDeals();
const countries = inject('countries'); //this is coming from provide in Wizard.vue
const countries_list = ref([]);
const deal_state = useDealStore();
const placeholder = ref(true);
const owner = ref({});
const emit = defineEmits(['next_step']);
const showBtnLoader = ref(false);
const showBtnLoaderSave = ref(false);
const selectedDocument = ref({
    other_documents: []
});

const propertyDocuments = ref([]);
const documentFromDb = ref({});
const genders = [
    'Male', 'Female', 'Unknown'
];
const formData = ref({
    gender: owner.gender ?? null,

});


const fieldErrors = ref({
    company: {
        error: false,
        message: "Please enter company name"
    },
    trade_license: {
        error: false,
        message: "Please enter trade license number"
    },
    trade_license_expiry: {
        error: false,
        message: "Please select trade license expiry date"
    },

    first_name: {
        error: false,
        message: "Please enter first name"
    },
    last_name: {
        error: false,
        message: "Please enter last name"
    },
    aliases: {
        error: false,
        message: "Please enter aliases"
    },
    address: {
        error: false,
        message: "Please enter address"
    },
    dob: {
        error: false,
        message: "Please enter date of birth"
    },
    pob: {
        error: false,
        message: "Please enter place of birth"
    },
    gender: {
        error: false,
        message: "Please select gender"
    },
    nationality_id: {
        error: false,
        message: "Please select nationality"
    },
    uae_national: {
        error: false,
        message: "Please select UAE national"
    },
    uae_residency: {
        error: false,
        message: "Please select uae residency"
    },
    emirates_id_copy: {
        error: false,
        message: "Please select emirates ID copy"
    },

    mobile: {
        error: false,
        message: "Please enter mobile number"
    },
    whatsapp: {
        error: false,
        message: "Please enter whatsapp number"
    },
    phone: {
        error: false,
        message: "Please enter phone number"
    },
    email: {
        error: false,
        message: "Please enter email"
    },
    passport: {
        error: false,
        message: "Please enter passport number"
    },
    passport_issue_date: {
        error: false,
        message: "Please select passport issue date"
    },
    passport_expiry: {
        error: false,
        message: "Please select passport expiry date"
    },
    passport_country_id: {
        error: false,
        message: "Please select passport issuing country"
    },
})

const requiredFields = ref({
    company: false,
    trade_license: false,
    trade_license_expiry: false,

    first_name: true,
    last_name: true,
    aliases: false,
    address: true,
    dob: false,
    pob: false,
    gender: false,
    nationality_id: false,
    uae_national: false,
    uae_residency: false,
    emirates_id_copy: false,

    mobile: true,
    whatsapp: true,
    phone: false,
    email: true,
    passport: false,
    passport_issue_date: false,
    passport_expiry: false,
    passport_country_id: false,
});

watch(() => owner.value.type, (val) => {
    if(val == 'COMPANY'){
        requiredFields.value.company = true;
        requiredFields.value.trade_license = true;
        requiredFields.value.trade_license_expiry = false;
    }else{
        requiredFields.value.company = false;
        requiredFields.value.trade_license = false;
        requiredFields.value.trade_license_expiry = false;
    }
});

const resetFieldErrors = () => {
    Object.keys(fieldErrors.value).map(key => {
        fieldErrors.value[key].error = false;
    })
}



const userSelected = (user) => {
    console.log(user);
    // formData.value.agent_id = user.id;
}
//////////////////////////////////////////////////////////////////
/////////-----------documents section codes -----------///////////////
//////////////////////////////////////////////////////////////////
const fileInputs = ref([
    {
        title: '',
        file: null,
        selectOption: {}
    }
]);
const addMore = () => {
    fileInputs.value.push({ title: '', file: null }); // Add another title and file input
}

const documents = ref({
    passport: 'Passport Copy',
    visa: 'Visa Copy',
    deed: 'Property Deed Documents',
    rera: 'RERA Documents'
})
const fileTitleOptions = [
    { name: 'Passport Copy', code: 'passport' },
    { name: 'Visa Copy', code: 'vc' },
    { name: 'Property Deed Documents', code: 'pdd' }
];
const addDynamicDocType = (newTag, target) => {
    const tag = {
        name: newTag,
        code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
    }
    //options.value.push(tag)
    fileTitleOptions.push(tag)
    //this.value.push(tag)
    //set the value of the select tag
    target.selectOption = tag;
}
const dynamicDocTypeSelected = (option, target) => {
    target.title = option.name;
}
const onFileChange = (name, title) => {
    let file = document.getElementById('document_' + name).files[0];
    //  formData.value[name] = URL.createObjectURL(file);
    selectedDocument.value['other_documents'].push({
        name: name,
        title: title,
        file: file
    });
}
const onDynamicFileChange = (index, title) => {
    let file = document.getElementById('document_dyn_' + index).files[0];
    let name = document.getElementById('document_dyn_title' + index);
    name = name.value;

    //  formData.value[name] = URL.createObjectURL(file);
    selectedDocument.value['other_documents'].push({
        name: name,
        title: name,
        file: file
    });
}

//on submit is handling async functions so we need to use async await

const onSubmit = async (next) => {
    resetFieldErrors();

    if(next){
        const isInvalid = Object.keys(owner.value).map(key => {
            if(requiredFields.value[key] != undefined){
                if(requiredFields.value[key] == true && (owner.value[key] == "" || owner.value[key] == null)){
                    fieldErrors.value[key].error = true
                    return true;
                }
                fieldErrors.value[key].error = false
                return false
            }
            return false;
        }).filter(item => item).length > 0;

        if(isInvalid){
            return true;
        }
    }

    if (next == true) {
        showBtnLoaderSave.value = true;
    } else {
        showBtnLoader.value = true;
    }


    // let formData1 = new FormData();
    //
    // formData1.append('id', owner.value.id);
    // selectedDocument.value['other_documents'].forEach((item, index) => {
    //     formData1.append('other_documents[' + index + '][file]', item.file);
    //     formData1.append('other_documents[' + index + '][title]', item.name);
    // });
    owner.value.deal_id = deal_state.data.id;
    await updateContact(owner.value);
    if (errors.value) {
        console.error('Validation error:', errors.value);

    } else {
        //form submitted successfully
        if (next == true) {
            emit('next_step', 'leads');
        }
    }
    // await updateContactDocuments(formData1);
    setTimeout(() => {
        getContact(owner.value.id);
        // //make file inputs with class 'fileInputs' empty
        // let inputs = document.querySelectorAll('.fileInputs');
        // inputs.forEach((input) => {
        //     input.value = '';
        // });
        showBtnLoader.value = false;
        showBtnLoaderSave.value = false;
        // emit('next_step', 'leads');
    }, 1000);
}


const setDocuments = (data) => {
    documentFromDb.value = {};
    if (data != null) {

        for (let [key, value] of Object.entries(data)) {
            documentFromDb.value[value.title] = value;
        }
    }
}
onBeforeMount(() => {
    owner.value = deal_state.data.contact;


});
watch(() => contact.value, (val) => {
    if (val != null) {
        placeholder.value = false;
        setDocuments(val.documents);
        if (val.contact_meta != null) {
            for (let [key, value] of Object.entries(val.contact_meta)) {
                if ('passport_country_id' == value.meta_key) {
                    owner.value.passport_country_id = value.meta_value != null ? parseInt(value.meta_value) : null;
                } else {
                    owner.value[value.meta_key] = value.meta_value;
                }


            }
        }
    }
});
watch(() => documentFromDb, (val) => {
    if (val != null) {

        setDocuments(val);
    }
});
watch(() => property, (val) => {
    if (val != null) {
        console.log(val);
        propertyDocuments.value = val.other_documents;
    }
});


const deleteDocument = (id) => {
    deleteMedia(id, 'contact');
    setTimeout(() => {
        getContact(owner.value.id);
    }, 2000);

}
onMounted(() => {
    owner.passport_issue_date = (owner.passport_issue_date != null && owner.passport_issue_date != undefined) ? owner.passport_issue_date : null;
    if (owner.value != null && owner.value != undefined) {
        getContact(owner.value.id);

    }

    if (owner.value != null && owner.value != undefined) {
        placeholder.value = false;
    }
    getProperty(deal_state.data.property_id);
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
</style>
