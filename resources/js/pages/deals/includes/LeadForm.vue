<template>
    <div class="card-body">
        <div v-if="!modalLoader">
            <form enctype="multipart/form-data">
                <div class="row gy-2">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="border d-none">
                                    <ul class="nav nav-pills custom-hover-nav-tabs" role="tablist">
                                        <li class="nav-item" role="presentation" style="width: 50%;">
                                            <a style="min-width: 200px; width: 100%;" href="#custom-hover-customere"
                                               data-bs-toggle="tab" aria-expanded="false" class="nav-link active"
                                               aria-selected="true" role="tab">
                                                <i class="ri-user-fill nav-icon nav-tab-position"></i>
                                                <h5 class="nav-titl nav-tab-position m-0">Customer</h5>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation" style="width: 50%;">
                                            <a style="min-width: 200px;width: 100%;" href="#documents-tab-pane"
                                               data-bs-toggle="tab" aria-expanded="true" class="nav-link"
                                               aria-selected="false" role="tab" tabindex="-1">
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
                                                <div class="offset-5 col-6">

                                                    <!-- Radio Buttons -->
                                                    <div class="btn-group" role="group"
                                                         aria-label="Basic radio toggle button group">
                                                        <input type="radio" class="btn-check" name="btnradio"
                                                               id="btnradio1" value="INDIVIDUAL"
                                                               :checked="lead.type == 'INDIVIDUAL' || mode == 'new'"
                                                               @change="typeChanged"
                                                               v-model="lead.type"
                                                               autocomplete="off">
                                                        <label class="btn btn-sm btn-outline-secondary" for="btnradio1"><i
                                                            class="ri-user-2-line" style="vertical-align: sub;"></i>
                                                            Individual</label>

                                                        <input type="radio" class="btn-check" id="btnradio2"
                                                               v-model="lead.type"
                                                               name="btnradio"
                                                               @change="typeChanged"
                                                               :checked="lead.type == 'COMPANY'"
                                                               value="COMPANY" autocomplete="off">
                                                        <label class="btn btn-sm btn-outline-secondary" for="btnradio2">Company
                                                            <i class="ri-team-line" style="vertical-align: bottom;"></i></label>
                                                    </div>
                                                </div>

                                                <div class="col-2"></div>

                                            </div>
                                            <div v-if="lead.type === 'INDIVIDUAL'">

                                                <div class="row g-3">
                                                    <h5 class="mt-4">Basic Information</h5>

                                                    <div class="col-md-2 col-sm-12 mt-2">First Name*
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input type="text" class="form-control inputs"
                                                               name="first_name"
                                                               id="first_name"
                                                               v-model="lead.first_name"
                                                               placeholder="Enter first name"
                                                               :class="{ 'is-invalid': individualFieldErrors.first_name.error}"
                                                                :required="individualRequiredFields.first_name">
                                                               <div class="invalid-feedback">
                                                                    {{ individualFieldErrors.first_name.message }}
                                                                </div>
                                                    </div>


                                                    <div class="col-md-2  col-sm-12 mt-2">Last Name
                                                    </div>
                                                    <div class="col-md-4  col-sm-12 mt-2">
                                                        <input type="text" class="form-control inputs"
                                                               name="last_name"
                                                               id="last_name"
                                                               v-model="lead.last_name"
                                                               placeholder="Enter last name"
                                                               :class="{ 'is-invalid': individualFieldErrors.last_name.error}"
                                                                :required="individualRequiredFields.last_name">
                                                               <div class="invalid-feedback">
                                                                    {{ individualFieldErrors.last_name.message }}
                                                                </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">Aliases (If any)</div>
                                                    <div class="col-md-4 col-sm-12 mt-2"
                                                         id="mobileDiv1">
                                                        <input type="tel"
                                                               class="form-control pl-40 inputs"
                                                               name="alias"
                                                               id="aliases"
                                                               v-model="lead.aliases"
                                                               placeholder="Enter aliases"
                                                               :class="{ 'is-invalid': individualFieldErrors.aliases.error}"
                                                                :required="individualRequiredFields.aliases">
                                                               <div class="invalid-feedback">
                                                                    {{ individualFieldErrors.aliases.message }}
                                                                </div>

                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">Residential Address</div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input type="text" class="form-control inputs"
                                                               name="address"
                                                               v-model="lead.address"

                                                               id="address" placeholder="Enter address"
                                                               :class="{ 'is-invalid': individualFieldErrors.address.error}"
                                                                :required="individualRequiredFields.address">
                                                               <div class="invalid-feedback">
                                                                    {{ individualFieldErrors.address.message }}
                                                                </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">Mobile*</div>
                                                    <div class="col-md-4 col-sm-12 mt-2"
                                                         id="mobileDiv1">
                                                        <input type="tel"
                                                               class="form-control pl-40 inputs"
                                                               name="mobile"
                                                               id="mobile" required
                                                               v-model="lead.mobile"
                                                               placeholder="Enter mobile"
                                                               :readonly="lead.id != null ? true : false"
                                                               :class="{ 'is-invalid': individualFieldErrors.mobile.error}"
                                                                :required="individualRequiredFields.mobile"
                                                        >
                                                            <div class="invalid-feedback">
                                                                {{ individualFieldErrors.mobile.message }}
                                                            </div>
                                                        <div class="text-danger mobileValidation">
                                                        </div>
                                                        <div class="text-danger"
                                                             id="mobile-exists"></div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">Telephone Number</div>
                                                    <div class="col-md-4 col-sm-12 mt-2"
                                                         id="mobileDiv1">
                                                        <input type="tel"
                                                               class="form-control pl-40 inputs"
                                                               name="phone"
                                                               id="phone"
                                                               v-model="lead.phone"
                                                               placeholder="Enter phone number"
                                                               :class="{ 'is-invalid': individualFieldErrors.phone.error}"
                                                                :required="individualRequiredFields.phone">
                                                                <div class="invalid-feedback">
                                                                    {{ individualFieldErrors.phone.message }}
                                                                </div>
                                                        <div class="text-danger mobileValidation">
                                                        </div>
                                                        <div class="text-danger"
                                                             id="mobile-exists"></div>
                                                    </div>

                                                    <div class="col-md-2 col-sm-12 mt-2">Email</div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input type="email" class="form-control inputs"
                                                               name="email"
                                                               id="email"
                                                               v-model="lead.email"
                                                               placeholder="Enter email address"
                                                               :class="{ 'is-invalid': individualFieldErrors.email.error}"
                                                                :required="individualRequiredFields.email">
                                                               <div class="invalid-feedback">
                                                                    {{ individualFieldErrors.email.message }}
                                                                </div>
                                                        <div class="text-danger"
                                                             id="email-exists"></div>
                                                    </div>
                                                    <div class="col-md-2  col-sm-12 mt-2">Nationality
                                                    </div>
                                                    <div class="col-md-4  col-sm-12 mt-2">

                                                        <v-select
                                                            v-model="lead.nationality_id"
                                                            :reduce="country => country.id"
                                                            :options="countries"
                                                            label="nationality"
                                                            @search="countries"
                                                            placeholder="Type to search countries"
                                                            :class="{ 'is-invalid': individualFieldErrors.nationality_id.error}"
                                                            :required="individualRequiredFields.nationality_id"
                                                        ></v-select>
                                                        <div class="invalid-feedback">
                                                            {{ individualFieldErrors.nationality_id.message }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">

                                                    <div class="col-md-2 col-sm-12 mt-2">Gender</div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <VueMultiselect
                                                            v-model="lead.gender"
                                                            :allowEmpty="true"
                                                            :searchable="true"
                                                            :close-on-select="true"
                                                            :show-label="true"
                                                            :options="genders"
                                                            :multiple="false"
                                                            :selectLabel="''"
                                                            :class="{ 'is-invalid': individualFieldErrors.gender.error}"
                                                            :required="individualRequiredFields.gender"
                                                        >
                                                        </VueMultiselect>
                                                        <div class="invalid-feedback">
                                                            {{ individualFieldErrors.gender.message }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2 col-sm-12 mt-2">Passport
                                                        Number
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input
                                                            v-model="lead.passport_number"
                                                            type="text" class="form-control inputs"
                                                            name="passport_number" id="passport_number"
                                                            placeholder="Enter passport number"
                                                            :class="{ 'is-invalid': individualFieldErrors.passport_number.error}"
                                                                :required="individualRequiredFields.passport_number">
                                                            <div class="invalid-feedback">
                                                                        {{ individualFieldErrors.passport_number.message }}
                                                                    </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">Date Of Birth
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <flatpickr data-date-format="d/m/Y"
                                                                   placeholder="Enter date of birth"
                                                                   class="form-control"
                                                                   name="dob" v-model="lead.dob"
                                                                   :class="{ 'is-invalid': individualFieldErrors.dob.error}"
                                                                :required="individualRequiredFields.dob"/>
                                                                   <div class="invalid-feedback">
                                                                        {{ individualFieldErrors.dob.message }}
                                                                    </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">Passport Issue Date
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <flatpickr data-date-format="d/m/Y"
                                                                   placeholder="Enter passport issue date"
                                                                   class="form-control" name="passport_issue_date"
                                                                   id="passport_issue_date"
                                                                   v-model="lead.passport_issue_date"
                                                                   :class="{ 'is-invalid': individualFieldErrors.passport_issue_date.error}"
                                                                :required="individualRequiredFields.passport_issue_date"/>
                                                                   <div class="invalid-feedback">
                                                                        {{ individualFieldErrors.passport_issue_date.message }}
                                                                    </div>

                                                    </div>


                                                    <div class="col-md-2 col-sm-12 mt-2">Place Of Birth
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input type="text" class="form-control inputs"
                                                               v-model="lead.place_of_birth"
                                                               placeholder="Enter place of birth"
                                                               :class="{ 'is-invalid': individualFieldErrors.place_of_birth.error}"
                                                                :required="individualRequiredFields.place_of_birth">
                                                               <div class="invalid-feedback">
                                                                {{ individualFieldErrors.place_of_birth.message }}
                                                            </div>

                                                    </div>


                                                    <div class="col-md-2 col-sm-12 mt-2">Passport
                                                        Expiry Date
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <flatpickr data-date-format="d/m/Y"
                                                                   placeholder="Enter passport expiry date"
                                                                   class="form-control" name="passport_expiry"
                                                                   id="passport_expiry"
                                                                   v-model="lead['passport_expiry']"
                                                                   :class="{ 'is-invalid': individualFieldErrors.passport_expiry.error}"
                                                                   :required="individualRequiredFields.passport_expiry"/>
                                                            <div class="invalid-feedback">
                                                                {{ individualFieldErrors.passport_expiry.message }}
                                                            </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">UAE National
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                   name="uae_national"
                                                                   id="inlineRadio1" value="1"
                                                                   :checked="lead.uae_national == '1'"
                                                                   v-model="lead.uae_national"
                                                                   @change="uaeNationalChanged"
                                                                   :class="{ 'is-invalid': individualFieldErrors.uae_national.error}"
                                                            >
                                                            <label class="form-check-label"
                                                                   for="inlineRadio1">Yes</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                   v-model="lead.uae_national"
                                                                   name="uae_national"
                                                                   :checked="lead.uae_national == '0'"
                                                                   @change="uaeNationalChanged"
                                                                   id="inlineRadio2" value="0"
                                                                   :class="{ 'is-invalid': individualFieldErrors.uae_national.error}">
                                                            <label class="form-check-label"
                                                                   for="inlineRadio2">No</label>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">Name On The Passport</div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input
                                                            v-model="lead.name_on_passport"
                                                            type="text" class="form-control inputs"
                                                            name="name_on_passport" id="name_on_passport"
                                                            placeholder="Enter name on the passport"
                                                            :class="{ 'is-invalid': individualFieldErrors.name_on_passport.error}"
                                                            :required="individualRequiredFields.name_on_passport">
                                                            <div class="invalid-feedback">
                                                                {{ individualFieldErrors.name_on_passport.message }}
                                                            </div>
                                                    </div>
                                                </div>

                                                <Transition>
                                                    <div class="row"
                                                         v-if="(lead.uae_national == '1' && lead.type === 'INDIVIDUAL' )">
                                                        <div class="col-md-2 col-sm-12 mt-2 d-none">Emirates ID
                                                        </div>
                                                        <div class="col-md-4 col-sm-12 mt-2 d-none">
                                                            <input
                                                                v-model="lead.emirates_id"
                                                                type="text" class="form-control inputs"
                                                                name="emirates_id" id="emirates_id"
                                                                placeholder="Enter Emirates ID">
                                                            <div class="invalid-feedback"> Please enter
                                                                Emirates ID
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-sm-12 mt-2 d-none">Emirates id
                                                            expiry
                                                        </div>
                                                        <div class="col-md-4 col-sm-12 mt-2 d-none">
                                                            <flatpickr data-date-format="d/m/Y"
                                                                       class="form-control" name="emirates_id_expiry"
                                                                       placeholder="Enter Emirates ID Expiry"
                                                                       id="passport_expiry"
                                                                       v-model="lead.emirates_id_expiry"/>
                                                            <div class="invalid-feedback"> Please enter
                                                                Emirates ID expiry.
                                                            </div>
                                                        </div>


                                                        <div class="col-md-2 col-sm-12 mt-2 d-none">Country of
                                                            residence
                                                        </div>
                                                        <div class="col-md-4 col-sm-12 mt-2 d-none"><input
                                                            v-model="lead.country_of_residence"
                                                            type="text" class="form-control inputs"
                                                            name="country_of_residence" id="country_of_residence"
                                                            placeholder="Enter country of residence">
                                                            <div class="invalid-feedback"> Please enter
                                                                country of residence.
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-sm-12 mt-2 d-none">Emirates ID copy
                                                        </div>
                                                        <div class="col-md-4 col-sm-12 mt-2 d-none"><input
                                                            type="file" class="form-control inputs"
                                                            name="emirates_id_copy" id="emirates_id_copy"
                                                            placeholder="Enter country of residence">
                                                            <div class="invalid-feedback"> Please select am emirat id
                                                            </div>
                                                        </div>
                                                    </div>
                                                </Transition>

                                                <div v-show="lead_type == 'Primary'">

                                                <transition >
                                                    <div class="row" v-if="lead.uae_national == '0'">
                                                        <div class="col-md-2 col-sm-12 mt-2">UAE Residency
                                                        </div>
                                                        <div class="col-md-4 col-sm-12 mt-2">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                       name="uae_resident"
                                                                       id="inlineRadio1" value="1"
                                                                       :checked="lead.uae_residency == '1'"
                                                                       v-model="lead.uae_residency"
                                                                       :class="{ 'is-invalid': individualFieldErrors.uae_residency.error}"
                                                                >
                                                                <label class="form-check-label"
                                                                       for="inlineRadio1">Yes</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                       v-model="lead.uae_residency"
                                                                       name="uae_resident"
                                                                       :checked="lead.uae_residency == '0'"
                                                                       id="inlineRadio2" value="0"
                                                                       :class="{ 'is-invalid': individualFieldErrors.uae_residency.error}">
                                                                <label class="form-check-label"
                                                                       for="inlineRadio2">No</label>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </Transition>
                                                <transition>
                                                    <div class="row" v-if="lead.uae_residency == '1'">
                                                        <div class="col-md-2 col-sm-12 mt-2">Residency Visa No.
                                                        </div>
                                                        <div class="col-md-4 col-sm-12 mt-2"><input
                                                            v-model="lead.residency_visa_no"
                                                            type="text" class="form-control inputs"
                                                            name="residency_visa_no" id="residency_visa_no"
                                                            placeholder="Enter residency visa no."
                                                            :class="{ 'is-invalid': individualFieldErrors.residency_visa_no.error}"
                                                            :required="individualRequiredFields.residency_visa_no">
                                                            <div class="invalid-feedback">
                                                                {{ individualFieldErrors.residency_visa_no.message }}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-sm-12 mt-2">Emirates ID No.
                                                        </div>
                                                        <div class="col-md-4 col-sm-12 mt-2"><input
                                                            v-model="lead.emirates_id"
                                                            type="text" class="form-control inputs"
                                                            name="emirates_id" id="emirates_id"
                                                            placeholder="Enter Emirates id."
                                                            :class="{ 'is-invalid': individualFieldErrors.emirates_id.error}"
                                                            :required="individualRequiredFields.emirates_id">
                                                            <div class="invalid-feedback">
                                                                {{ individualFieldErrors.emirates_id.message }}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-sm-12 mt-2">Visa expiry date
                                                        </div>
                                                        <div class="col-md-4 col-sm-12 mt-2"><input
                                                            v-model="lead.visa_expiry_date"
                                                            type="text" class="form-control inputs"
                                                            name="visa_expirty_date" id="visa_expirty_date"
                                                            placeholder="Enter visa expiry date."
                                                            :class="{ 'is-invalid': individualFieldErrors.visa_expiry_date.error}"
                                                            :required="individualRequiredFields.visa_expiry_date">
                                                            <div class="invalid-feedback">
                                                                {{ individualFieldErrors.visa_expiry_date.message }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </transition>

                                                <div class="row">
                                                    <div class="col-md-2 col-sm-12 mt-2">Fund Source
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">

                                                        <VueMultiselect
                                                            v-model="lead.fund_source"
                                                            :allowEmpty="true"
                                                            :searchable="true"
                                                            :close-on-select="true"
                                                            :show-label="true"
                                                            :options="fundSource"
                                                            :multiple="false"
                                                            :selectLabel="''"
                                                            :class="{ 'is-invalid': individualFieldErrors.fund_source.error}"
                                                            :required="individualRequiredFields.fund_source"
                                                        >
                                                        </VueMultiselect>
                                                        <div class="invalid-feedback">
                                                                {{ individualFieldErrors.fund_source.message }}
                                                            </div>
                                                    </div>

                                                    <div v-if="lead.fund_source == 'OTHER'"
                                                         class="col-md-2 col-sm-12 mt-2">
                                                        Other
                                                    </div>
                                                    <div v-if="lead.fund_source == 'OTHER'"
                                                         class="col-md-4 col-sm-12 mt-2">
                                                        <input type="text" class="form-control inputs"
                                                               name="fund_source_other"
                                                               id="fund_source_other"
                                                               v-model="lead.fund_source_other"
                                                               placeholder="Enter other fund source"
                                                               :class="{ 'is-invalid': individualFieldErrors.fund_source_other.error}"
                                                                :required="individualRequiredFields.fund_source_other">
                                                               <div class="invalid-feedback">
                                                                {{ individualFieldErrors.fund_source_other.message }}
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-12 mt-2">Occupation
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input type="text" class="form-control inputs"
                                                               name="occupation"
                                                               id="occupation"
                                                               v-model="lead.occupation"
                                                               placeholder="Enter occupation"
                                                               :class="{ 'is-invalid': individualFieldErrors.occupation.error}"
                                                                :required="individualRequiredFields.occupation">
                                                               <div class="invalid-feedback">
                                                                {{ individualFieldErrors.occupation.message }}
                                                            </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">Years In Present Role
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input type="text" class="form-control inputs"
                                                               name="occupation"
                                                               id="occupation"
                                                               v-model="lead.year_of_experience"
                                                               placeholder="Enter years in present role"
                                                               :class="{ 'is-invalid': individualFieldErrors.year_of_experience.error}"
                                                                :required="individualRequiredFields.year_of_experience">
                                                               <div class="invalid-feedback">
                                                                {{ individualFieldErrors.year_of_experience.message }}
                                                            </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">Employer / Establishment Name
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input type="text" class="form-control inputs"
                                                               name="employer_name"
                                                               id="employer_name"
                                                               v-model="lead.employer_name"
                                                               placeholder="Enter employer/establishment name"
                                                               :class="{ 'is-invalid': individualFieldErrors.employer_name.error}"
                                                                :required="individualRequiredFields.employer_name">
                                                               <div class="invalid-feedback">
                                                                {{ individualFieldErrors.employer_name.message }}
                                                            </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">Employer / Establishment
                                                        Address
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input type="text" class="form-control inputs"
                                                               name="employer_address"
                                                               id="employer_address"
                                                               v-model="lead.employer_address"
                                                               placeholder="Enter employer address"
                                                               :class="{ 'is-invalid': individualFieldErrors.employer_address.error}"
                                                                :required="individualRequiredFields.employer_address"
                                                               >
                                                               <div class="invalid-feedback">
                                                                {{ individualFieldErrors.employer_address.message }}
                                                            </div>
                                                    </div>

                                                    <div class="col-md-2 col-sm-12 mt-2">
                                                        Annual Income
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <input type="text" class="form-control inputs"
                                                               name="annual_income"
                                                               id="annual_income"
                                                               v-model="lead.annual_income"
                                                               placeholder="Enter annual income"
                                                               :class="{ 'is-invalid': individualFieldErrors.annual_income.error}"
                                                                :required="individualRequiredFields.annual_income">
                                                            <div class="invalid-feedback">
                                                                {{ individualFieldErrors.annual_income.message }}
                                                            </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">
                                                        Payment Method
                                                    </div>
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        <VueMultiselect
                                                            v-model="lead.payment_method"
                                                            :allowEmpty="true"
                                                            :searchable="true"
                                                            :close-on-select="true"
                                                            :show-label="true"
                                                            :options="paymentMethods"
                                                            :multiple="false"
                                                            :selectLabel="''"
                                                            :class="{ 'is-invalid': individualFieldErrors.payment_method.error}"
                                                            :required="individualRequiredFields.payment_method"
                                                        >
                                                        </VueMultiselect>
                                                        <div class="invalid-feedback">
                                                                {{ individualFieldErrors.payment_method.message }}
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                        Are You Current/Former Politically Exposed Person (PEP)?
                                                    </div>
                                                    <div class="col-md-2 col-sm-12 mt-2">
                                                        <VueMultiselect
                                                            v-model="lead.political"
                                                            :allowEmpty="true"
                                                            :searchable="true"
                                                            :close-on-select="true"
                                                            :show-label="true"
                                                            :options="political_option"
                                                            :multiple="false"
                                                            :selectLabel="''"
                                                            :class="{ 'is-invalid': individualFieldErrors.political.error}"
                                                            :required="individualRequiredFields.political"
                                                        >
                                                        </VueMultiselect>
                                                        <div class="invalid-feedback">
                                                                {{ individualFieldErrors.political.message }}
                                                            </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="row" v-if="lead.type === 'COMPANY'">
                                                <div class="col-md-6 col-sm-12 mt-2">Full Legal Name
                                                </div>
                                                <div class="col-md-6 col-sm-12 mt-2">
                                                    <input type="text" class="form-control inputs"
                                                           name="first_name"
                                                           id="first_name"
                                                           v-model="lead.first_name"
                                                           placeholder="Enter first name"
                                                           :class="{ 'is-invalid': companyFieldErrors.first_name.error}"
                                                           :required="companyRequiredFields.first_name">
                                                           <div class="invalid-feedback">
                                                                {{ companyFieldErrors.first_name.message }}
                                                            </div>
                                                </div>


                                                <div class="col-md-6  col-sm-12 mt-2">Former Name (If any)
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <input type="text" class="form-control inputs"
                                                           name="last_name"
                                                           id="last_name"
                                                           v-model="lead.company_former_name"
                                                           placeholder="Enter former name"
                                                           :class="{ 'is-invalid': companyFieldErrors.company_former_name.error}"
                                                           :required="companyRequiredFields.company_former_name"
                                                           >
                                                           <div class="invalid-feedback">
                                                                {{ companyFieldErrors.company_former_name.message }}
                                                            </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">Trading Name (If any)
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <input type="text" class="form-control inputs"
                                                           name="last_name"
                                                           id="last_name"
                                                           v-model="lead.company_trading_name"
                                                           placeholder="Enter trading name"
                                                           :class="{ 'is-invalid': companyFieldErrors.company_trading_name.error}"
                                                           :required="companyRequiredFields.company_trading_name"
                                                           >
                                                           <div class="invalid-feedback">
                                                                {{ companyFieldErrors.company_trading_name.message }}
                                                            </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">Trade License Number
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <input type="text" class="form-control inputs"
                                                           name="last_name"
                                                           id="last_name"
                                                           v-model="lead.company_trad_license_no"
                                                           placeholder="Enter trading number"
                                                           :class="{ 'is-invalid': companyFieldErrors.company_trad_license_no.error}"
                                                           :required="companyRequiredFields.company_trad_license_no"
                                                           >
                                                           <div class="invalid-feedback">
                                                                {{ companyFieldErrors.company_trad_license_no.message }}
                                                            </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    Country Of Incorporation/Registration
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <v-select
                                                        v-model="lead.company_coi"
                                                        :reduce="country => country.id"
                                                        :options="countries"
                                                        label="name"
                                                        @search="countries"
                                                        placeholder="Country of incorporation"
                                                        :class="{ 'is-invalid': companyFieldErrors.company_coi.error}"
                                                           :required="companyRequiredFields.company_coi"
                                                    ></v-select>
                                                    <div class="invalid-feedback">
                                                                {{ companyFieldErrors.company_coi.message }}
                                                            </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    Date Of Incorporation/Registration
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <flatpickr type="date" data-date-format="d/m/Y"
                                                               v-model="lead.company_coi_date"
                                                               class="form-control" name="company_coi_date"
                                                               placeholder="Enter Date Of Incorporation"
                                                               id="company_coi_date"
                                                               :class="{ 'is-invalid': companyFieldErrors.company_coi_date.error}"
                                                                :required="companyRequiredFields.company_coi_date"
                                                               />
                                                               <div class="invalid-feedback">
                                                                {{ companyFieldErrors.company_coi_date.message }}
                                                            </div>

                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">Registered Address
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <input type="text" class="form-control inputs"
                                                           name="last_name"
                                                           id="last_name"
                                                           v-model="lead.address"
                                                           placeholder="Enter Registered Address"
                                                           :class="{ 'is-invalid': companyFieldErrors.address.error}"
                                                                :required="companyRequiredFields.address">
                                                           <div class="invalid-feedback">
                                                                {{ companyFieldErrors.address.message }}
                                                            </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">Address of Principal Place of
                                                    Business
                                                    (if different from Registered Address)
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <input type="text" class="form-control inputs"
                                                           name="last_name"
                                                           id="last_name"
                                                           v-model="lead.company_principal_address"
                                                           placeholder="Enter Address of Principal Place of Business"
                                                           :class="{ 'is-invalid': companyFieldErrors.company_principal_address.error}"
                                                                :required="companyRequiredFields.company_principal_address">
                                                           <div class="invalid-feedback">
                                                                {{ companyFieldErrors.company_principal_address.message }}
                                                            </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    Telephone Number
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <input type="text" class="form-control inputs"
                                                           name="last_name"
                                                           id="last_name"
                                                           v-model="lead.phone"
                                                           placeholder="Enter Telephone number"
                                                           :class="{ 'is-invalid': companyFieldErrors.phone.error}"
                                                                :required="companyRequiredFields.phone">
                                                           <div class="invalid-feedback">
                                                                {{ companyFieldErrors.phone.message }}
                                                            </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    Email Address
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <input type="text" class="form-control inputs"
                                                           name="last_name"
                                                           id="last_name"
                                                           v-model="lead.email"
                                                           placeholder="Enter email"
                                                           :class="{ 'is-invalid': companyFieldErrors.email.error}"
                                                                :required="companyRequiredFields.email">
                                                           <div class="invalid-feedback">
                                                                {{ companyFieldErrors.email.message }}
                                                            </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    Trade License Issue Date
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <flatpickr type="date" data-date-format="d/m/Y"
                                                               class="form-control" name="trade_license_issue_date"
                                                               placeholder="Enter trade license issue date"
                                                               id="trade_license_issue_date"

                                                               v-model="lead.trade_license_issue_date"
                                                               :class="{ 'is-invalid': companyFieldErrors.trade_license_issue_date.error}"
                                                                :required="companyRequiredFields.trade_license_issue_date"
                                                               />
                                                               <div class="invalid-feedback">
                                                                {{ companyFieldErrors.trade_license_issue_date.message }}
                                                            </div>

                                                </div>

                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    Trade License Expiry Date
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">

                                                    <flatpickr type="date" data-date-format="d/m/Y"
                                                               class="form-control" name="trade_license_expiry_date"
                                                               placeholder="Enter trade license expiry date"
                                                               id="trade_license_expiry_date"
                                                               v-model="lead.trade_license_expiry_date"
                                                               :class="{ 'is-invalid': companyFieldErrors.trade_license_expiry_date.error}"
                                                                :required="companyRequiredFields.trade_license_expiry_date"
                                                                />
                                                               <div class="invalid-feedback">
                                                                    {{ companyFieldErrors.trade_license_expiry_date.message }}
                                                                </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    Trade License Issuing Authority
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <input type="text" class="form-control inputs"
                                                           name="last_name"
                                                           id="last_name"
                                                           v-model="lead.trade_license_issuing_authority"
                                                           placeholder="Enter issuing authority"
                                                           :class="{ 'is-invalid': companyFieldErrors.trade_license_issuing_authority.error}"
                                                                :required="companyRequiredFields.trade_license_issuing_authority">
                                                           <div class="invalid-feedback">
                                                                {{ companyFieldErrors.trade_license_issuing_authority.message }}
                                                            </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    Does This Company Have Any UBO?
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="uae_national"
                                                               id="inlineRadio1" value="1"
                                                               :checked="lead.have_ubo == '1'"
                                                               v-model="lead.have_ubo"
                                                               :class="{ 'is-invalid': companyFieldErrors.have_ubo.error}"
                                                        >
                                                        <label class="form-check-label" for="inlineRadio1">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               v-model="lead.have_ubo"
                                                               name="uae_national"
                                                               :checked="lead.have_ubo == '0'"
                                                               id="inlineRadio2" value="0"
                                                               :class="{ 'is-invalid': companyFieldErrors.have_ubo.error}"
                                                               >
                                                        <label class="form-check-label" for="inlineRadio2">No</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2">

                                                </div>
                                                <div class="col-md-6  col-sm-12 mt-2" v-if="lead.have_ubo == '1'">
                                                    <table class="table table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Full Name, Alias

                                                            </th>
                                                            <th>
                                                                <button type="button"
                                                                        class="btn btn-sm btn-outline-success float-end"
                                                                        @click="addUbo()">
                                                                    <i class="mdi mdi-plus-thick"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-if="lead.lead_meta" v-for="(ubo, index) in ubo_list"
                                                            :key="index">
                                                            <td>{{ index + 1 }}</td>
                                                            <td>{{ ubo.json.first_name }} {{ ubo.json.last_namee }}
                                                            </td>
                                                            <td>

                                                                <button type="button"
                                                                        class="btn btn-sm btn-outline-danger float-end"
                                                                        @click="addUbo()">
                                                                    <i class="mdi mdi-delete"></i>
                                                                </button>
                                                                <button type="button"
                                                                        class="btn btn-sm btn-outline-info float-end"
                                                                        @click="addUbo()">
                                                                    <i class="mdi mdi-pencil"></i>
                                                                </button>
                                                            </td>
                                                        </tr>

                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>

                                        </div>
                                        <div class="tab-pane" id="documents-tab-pane" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-2 col-sm-12 mt-2">Other Documents</div>
                                                <div class="col-md-4 col-sm-12 mt-2">

                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-6" v-for="(item, index) in documentsType" :key="index">
                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-12 mt-2">
                                                            {{ item }}
                                                        </div>
                                                        <div class="col-md-8 col-sm-12 mt-2">


                                                            <div class="input-group">
                                                                <input type="file" class="form-control inputs"
                                                                       :name="'document_'+index"
                                                                       :id="'document_'+index"
                                                                       placeholder="Enter first name"
                                                                       @change="onFileChange(index,item)"
                                                                       aria-label="Upload"
                                                                >

                                                                <a v-if="leadDocumentFromDb[index]"
                                                                   :href="leadDocumentFromDb[index].path"
                                                                   target="_blank"
                                                                   class="btn btn-outline-success btn-icon waves-effect waves-light"
                                                                   style="padding: 0.3rem;"><i class="ri-eye-line"></i></a>
                                                                <button v-if="leadDocumentFromDb[index]"
                                                                        class="btn btn-outline-danger btn-icon waves-effect waves-light"
                                                                        style="padding: 0.3rem;" type="button"
                                                                        @click="deleteLeadDocument(lead?.id,leadDocumentFromDb[index].id)"
                                                                ><i class=" ri-delete-bin-line"></i></button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="custom-hover-reviews" role="tabpanel">

                                        </div>
                                    </div>
                                </div><!-- end card-body -->

                            </div>
                        </div>

                        <input type="hidden" name="lead_id" v-model="lead.lead_id">


                    </div>
                    <!-- end col -->
                    <div class="col-lg-12">
                        <div class="row g-2">
                            <h5 class="mt-3">Other Information</h5>

                            <div class="col-md-2">
                                <label for="zip" class="form-label">Lead Source: </label>
          </div><div class="col-md-4">
                                <v-select
                                    v-if="mode == 'new'"
                                    v-model="lead.source_id"
                                    :reduce="leadSource => leadSource.id"
                                    :options="leadSource"
                                    label="name"
                                    @search="leadSource"
                                    placeholder="Type to search lead source"
                                ></v-select>
                                <i v-if="leadProp.lead_source != null" :class="leadProp.lead_source.icon"
                                   style="font-size: 20px;"></i>
                                {{ leadProp?.lead_source?.name }}

                            </div>
                            <div class="col-md-2">
                                <label for="zip" class="form-label">Lead Medium*</label>
                              </div>
                          <div class="col-md-4">
                                <v-select

                                    v-if="mode == 'new'"
                                    v-model="lead.medium_id"
                                    :reduce="leadMediums => leadMediums.id"
                                    :options="leadMediums"
                                    label="name"
                                    @search="leadMediums"
                                    placeholder="Type to search lead medium"
                                ></v-select>
                                <i :class="lead?.lead_medium?.icon" style="font-size: 20px;"></i>
                                {{ lead?.lead_medium?.name }}
                            </div>
                            <div class="col-12"  v-if="mode == 'new'" id="messageDiv">
                                <label for="message" class="form-label">Message from
                                    Customer</label>
                                <div class="text-wrap msg-customer">
                                    {{ lead.message }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="mt-4">
                    <div class="hstack gap-2 justify-content-right float-end">
                        <button type="button" class="btn btn-light " @click="closeForm">Close</button>
                        <button type="submit" class="btn btn-success action-add" @click="onSubmit">Save</button>
                    </div>
                </div>
            </form>
        </div>
        <div v-else>
            <CardPlaceholder></CardPlaceholder>
            <!-- Border spinner -->

        </div>
    </div>
    <div>
        <UboModal :lead="lead" @uboadded="fetchUbo()"></UboModal>
    </div>
</template>
<script setup>
import {ref, onMounted, watchEffect, onUnmounted, inject, watch, provide} from 'vue'
import {useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
import useLeads from "../../../pages/leads/composables/leads";
import axios from "axios";
import flatpickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/material_blue.css';
import UboModal from "./UboModal.vue";
import DynamicSelect from 'vue-dynamic-select'

const emit = defineEmits(['removeLeadModal', 'refreshLeadList', 'closeLeadForm']);
const modalLoader = ref(true);
import useGlobalHelper from "../../../composables/helpers/global";
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';
import CardPlaceholder from "../../../utilities/placeholder/CardPlaceholder.vue";
import VueMultiselect from "vue-multiselect";

const {leadSource, getLeadSource, leadMediums, getLeadMediums} = useLeads();
const {formStatus, getLeadTypes, leadTypes, updateLead, deal, deleteMedia, lead, getLead} = useDeals();
const {humanReadableString} = useGlobalHelper();
const {leadResources} = useDealStore();
const leadTypeOptions = ref([]);
const leadDocumentFromDb = ref({});
const countries = inject('countries');


const ubo_list = ref([]);
const props = defineProps({
    deals: {
        type: Object,
        required: false
    },
    leadProp: {
        type: Object,
        required: true
    },
    mode: {
        type: String,
        required: false,
        default: 'edit'
    },
    lead_type: {
        type: String,
        required: false,
        default: 'Secondary'
    }
});
const genders = [
    'Male', 'Female', 'Unknown'
];
const documents = ref([]);
const individualDocuments = ref({
    passport: 'Passport Copy',
    visa: 'Visa copy',
    eid: 'Emirates ID Copy',
    signed_tenancy_contract: 'Signed Tenancy Contract',
});
const companyDocuments = ref({
    trade_license: 'Trade License',
    poa: 'Company POA',
    moa: 'Memorandum of Association',
    coi: 'Certificate of Incumbency',
    vat: 'VAT Certificate',
    authorized_signatory_doc: 'Authorized Signatory Document',
    signed_tenancy_contract: 'Signed Tenancy Contract',
});
const documentsType = ref({});
const selectedDocument = ref({
    other_documents: []
});
const fundSource = [
    'Employment Income',
    'Sale of Investments or Properties',
    'Business Income',
    'Savings',
    'Other',
];
const paymentMethods = [
    'Cash',
    'Cheque',
    'Bank Transfer',
    'Mortgage',
    'Crypto',
    'Other',
];
const political_option = [
    'Yes',
    'No',
    'Other'
];
const formData = ref({});


const leadType = ref({
    INDIVIDUAL: 'Individual',
    COMPANY: 'Company/Entity',
    REFERRAL: 'Referral',
});
const closeForm = () => {

    emit('closeLeadForm');
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
const removeDocument = (id, model) => {
    deleteMedia(id, model);
}
const typeChanged = () => {
    //find props.leadProp.lead_meta object which has meta_key = type
    let typeData = 'INDIVIDUAL';
    if (props.mode == 'new') {
        let typeData = 'INDIVIDUAL';
    } else {
        let typeData = props.leadProp.lead_meta.find(item => item.meta_key === 'type');
    }
    let type = (lead != null && lead.type != undefined) ? lead.type : typeData;

    // if (type == undefined) {
    //   documentsType.value = {};
    // } else if (type == 'INDIVIDUAL') {
    //   documentsType.value = individualDocuments.value;
    // } else {
    //   documentsType.value = companyDocuments.value;
    // }

    // if(props.leadProp.value.type == undefined){
    //     documentsType.value = {};
    // }else if (props.leadProp.lead.value.type == 'INDIVIDUAL') {
    //     documentsType.value = individualDocuments.value;
    // } else {
    //     documentsType.value = companyDocuments.value;
    // }
}
const deleteLeadDocument = (lead_id, doc_id) => {
    let isDeleted = deleteMedia(doc_id, 'lead');

    setTimeout(() => {
        getLead(props.leadProp.id)
    }, 1000);

}

watchEffect(() => {
    leadTypeOptions.value = leadResources.leadTypes;
    if (props.leadProp) {
        setTimeout(() => {
            modalLoader.value = false;
            $('#leadFormModal').modal('show');
        }, 1000);
    } else {
        modalLoader.value = true;
    }
    props.deals = deal.value;

})
const setDocuments = (data) => {
    leadDocumentFromDb.value = {};

    if (data != null) {
        for (let [key, value] of Object.entries(data)) {
            leadDocumentFromDb.value[value.title] = value;
        }
    }
}

watch(() => leadDocumentFromDb, (val) => {
    if (val != null) {
        setDocuments(val);
    }
});
const uaeNationalChanged = (val) => {
    let selected_value = val.target.value;
    if (selected_value == 1 || selected_value == '1') {
        lead.value.uae_residency = null;
        individualRequiredFields.value.uae_residency = false;
    }else{
        individualRequiredFields.value.uae_residency = true;
    }
}

watch(() => lead.value, (val) => {
    if (val != null) {
        if (lead.type == '' || lead.type == undefined || lead.type == null) {
            lead.value.type = 'INDIVIDUAL';
        }
        typeChanged();
        setDocuments(val.documents);
        if (val.lead_meta != null) {
            for (let [key, value] of Object.entries(val.lead_meta)) {

                if (value.meta_key == 'company_coi') {
                    //convert to integer if value is string
                    lead.value[value.meta_key] = parseInt(value.meta_value) ?? null;
                } else {
                    lead.value[value.meta_key] = value.meta_value;
                }
                if (value.meta_key == 'ubo') {
                    let jsonData = JSON.parse(value.meta_value);
                    value.json = jsonData;
                    ubo_list.value.push(value);

                }

            }


        }
    }
});

const leadModalClosed = () => {
    emit('removeLeadModal');
    modalLoader.value = false;
}
const formUpdateStatus = ref('');

const companyFieldErrors = ref({
    first_name: {
        error: false,
        message: "Please enter legal full name"
    },
    company_former_name: {
        error: false,
        message: "Please enter former name"
    },
    company_trading_name: {
        error: false,
        message: "Please enter trading number"
    },

    company_trad_license_no: {
        error: false,
        message: "Please enter trade license number"
    },
    company_coi: {
        error: false,
        message: "Please select country of incorporation/registration"
    },
    company_coi_date: {
        error: false,
        message: "Please select date of incorporation/registration"
    },
    address: {
        error: false,
        message: "Please enter registered address"
    },
    company_principal_address: {
        error: false,
        message: "Please enter address of principal place of business"
    },
    phone: {
        error: false,
        message: "Please enter telephone number"
    },
    email: {
        error: false,
        message: "Please enter email address"
    },
    trade_license_issue_date: {
        error: false,
        message: "Please select trade license issue date"
    },
    trade_license_expiry_date: {
        error: false,
        message: "Please select trade license expiry date"
    },
    trade_license_issuing_authority: {
        error: false,
        message: "Please enter trade license issuing authority"
    },
    have_ubo: {
        error: false,
        message: "Please select UBO"
    },
})

const companyRequiredFields = ref({
    first_name: true,
    company_former_name: true,
    company_trading_name: true,
    company_trad_license_no: true,
    company_coi: true,
    company_coi_date: true,
    address: true,
    company_principal_address: true,
    phone: true,
    email: true,
    trade_license_issue_date: true,
    trade_license_expiry_date: true,
    trade_license_issuing_authority: true,
    have_ubo: true,
});


const individualFieldErrors = ref({
    first_name: {
        error: false,
        message: "Please enter full name"
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
        message: "Please enter residential address"
    },
    mobile: {
        error: false,
        message: "Please enter Mobile"
    },
    phone: {
        error: false,
        message: "Please enter telephone Number"
    },
    email: {
        error: false,
        message: "Please enter email"
    },
    nationality_id: {
        error: false,
        message: "Please select Nationality"
    },
    gender: {
        error: false,
        message: "Please select gender"
    },
    passport_number: {
        error: false,
        message: "Please enter passport number"
    },
    dob: {
        error: false,
        message: "Please select date of birth"
    },
    passport_issue_date: {
        error: false,
        message: "Please select passport issue date"
    },
    place_of_birth: {
        error: false,
        message: "Please select place of birth"
    },
    passport_expiry: {
        error: false,
        message: "Please select passport expiry date"
    },
    uae_national: {
        error: false,
        message: "Please select UAE national"
    },
    name_on_passport: {
        error: false,
        message: "Please enter name on the passport"
    },
    uae_residency: {
        error: false,
        message: "Please select UAE residency"
    },
    residency_visa_no: {
        error: false,
        message: "Please enter residency visa number"
    },
    emirates_id: {
        error: false,
        message: "Please enter emirates ID number"
    },
    visa_expiry_date: {
        error: false,
        message: "Please select visa expiry date"
    },
    fund_source: {
        error: false,
        message: "Please select fund source"
    },
    occupation: {
        error: false,
        message: "Please enter occupation"
    },
    year_of_experience: {
        error: false,
        message: "Please enter years in present role"
    },
    employer_name: {
        error: false,
        message: "Please enter employer/establishment name"
    },
    employer_address: {
        error: false,
        message: "Please enter employer/establishment Address"
    },
    annual_income: {
        error: false,
        message: "Please enter annual income"
    },
    payment_method: {
        error: false,
        message: "Please select payment method"
    },
    political: {
        error: false,
        message: "Please select are uou current/former politically exposed person (PEP)?"
    },
})

const individualRequiredFields = ref({
    first_name: true,
    last_name: true,
    aliases: true,
    address: true,
    mobile: true,
    phone: true,
    email: true,
    nationality_id: true,
    gender: true,
    passport_number: true,
    dob: true,
    passport_issue_date: true,
    place_of_birth: true,
    passport_expiry: true,
    uae_national: true,
    name_on_passport: true,
    uae_residency: true,
    residency_visa_no: true,
    emirates_id: true,
    visa_expiry_date: true,
    fund_source: true,
    occupation: true,
    year_of_experience: true,
    employer_name: true,
    employer_address: true,
    annual_income: true,
    payment_method: true,
    political: true,
});


const resetFieldErrors = () => {
    Object.keys(companyFieldErrors.value).map(key => {
        companyFieldErrors.value[key].error = false;
    })

    Object.keys(individualFieldErrors.value).map(key => {
        individualFieldErrors.value[key].error = false;
    })
}

const onSubmit = async () => {
    resetFieldErrors();

    if(lead.value.type == "COMPANY"){
        const isInvalid = Object.keys(lead.value).map(key => {
            if(companyRequiredFields.value[key] != undefined){
                if(companyRequiredFields.value[key] == true && (lead.value[key] == "" || lead.value[key] == null)){
                    companyFieldErrors.value[key].error = true
                    return true;
                }
                companyFieldErrors.value[key].error = false
                return false
            }
            return false;
        }).filter(item => item).length > 0;

        if(isInvalid){
            return true;
        }
    }

    if(lead.value.type == "INDIVIDUAL"){
        const isInvalid = Object.keys(individualRequiredFields.value).map(key => {
            if(individualRequiredFields.value[key] != undefined){
                if(individualRequiredFields.value[key] == true && (lead.value[key] == "" || lead.value[key] == null)){
                    individualFieldErrors.value[key].error = true
                    return true;
                }
                individualFieldErrors.value[key].error = false
                return false
            }
            return false;
        }).filter(item => item).length > 0;

        if(isInvalid){
            return true;
        }
    }



    let formData1 = new FormData();
    // for (let [key, value] of Object.entries(formData.value)) {
    //     formData1.append(key, value);
    // }

    for (let [key, value] of Object.entries(lead.value)) {
        formData1.append(key, value ? value : '');
    }
    // selectedDocument.value['other_documents'].forEach((item, index) => {
    //   formData1.append('other_documents[' + index + '][file]', item.file);
    //   formData1.append('other_documents[' + index + '][title]', item.name);
    // });

    formData1.append('agent_id', props.deals.agent_id);
    formData1.append('deal_id', props.deals.id);
    formData1.append('mode', props.mode);
    await updateLead(formData1, lead.value.id, props.deals.id);


    //wait for the updateLead to finish then close the modal if success
    // setTimeout(() => {
    //     $('#leadFormModal').modal('hide');
    // }, 2000);


    props.leadProp = lead.value;
}
watch(() => formStatus.value, (val) => {
    if (formStatus.value == 'SUCCESS') {
        emit('refreshLeadList');
        formStatus.value = 'PENDING';
    }

});
const addUbo = () => {
    $('#uboModal').modal('show');
}
onMounted(() => {

    if (props.leadProp.id != null && props.leadProp.id != undefined) {
        getLead(props.leadProp.id)
    }
    getLeadSource();
    getLeadMediums();


    $('#leadFormModal').modal('show');
    if (lead) {
        // props.lead = lead.value;

        // lead.type = 'INDIVIDUAL'; //(props.leadProp.type == '' && props.leadProp.type == undefined && props.leadProp.type == null) ? 'INDIVIDUAL' : props.leadProp.type;
        if (props.leadProp.documents != null) {
            for (let [key, value] of Object.entries(props.leadProp.documents)) {
                leadDocumentFromDb.value[value.title] = value;
            }
        }


        $('#leadFormModal').modal('show');
        setTimeout(() => {
            modalLoader.value = false;
        }, 600);
    }
    if (!leadResources.leadTypes) {
        getLeadTypes();
        leadResources.leadTypes = leadTypes;
    }
if(lead.value.type == null || props.leadProp.type == null){
    lead.value.type = 'INDIVIDUAL';

}

});

onUnmounted(() => {
    $('#leadFormModal').modal('hide');
    modalLoader.value = true;
});

watch(() => lead.value.uae_residency, (uaeResidency) => {
    if(uaeResidency == null || uaeResidency ==  0){
        individualRequiredFields.value.residency_visa_no = false;
        individualRequiredFields.value.emirates_id = false;
        individualRequiredFields.value.visa_expiry_date = false;
    }else{
        individualRequiredFields.value.residency_visa_no = true;
        individualRequiredFields.value.emirates_id = true;
        individualRequiredFields.value.visa_expiry_date = true;
    }
})
</script>
<style scoped>
.modal {
    position: fixed !important;

}

.msg-customer {
//width: 15rem; border: 1px dashed #00000030;
    overflow-x: auto;
    font-size: 12px;
}

.btn-outline-success {
    border-color: #45cb856b;
    color: #45cb8587;
}

.btn-outline-danger {
    border-color: #f0654859;
    color: #f065486b;
}

.btn-outline-success:hover {
    background-color: #45cb85;
    border-color: #45cb85;
    color: #fff;
}

.btn-outline-danger:hover {
    background-color: #f06548;
    border-color: #f06548;
    color: #fff;
}

.flatpickr-input .form-control[readonly] {
    background-color: white !important;
    opacity: 1;
}

.btn-check:active + .btn-outline-secondary, .btn-check:checked + .btn-outline-secondary, .btn-outline-secondary.active, .btn-outline-secondary.dropdown-toggle.show, .btn-outline-secondary:active {
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
