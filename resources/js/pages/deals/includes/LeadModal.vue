<template>

    <div class="modal fade" id="leadFormModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">View/Update Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="leadModalClosed()"></button>
                </div>
                <div class="modal-body">
                    <div v-if="!modalLoader">
                        <form @submit.prevent="onSubmit" enctype="multipart/form-data">
                            <div class="row gy-2">
                                <div class="col-lg-9">

                                  <div class="row">
                                    <div class="col-md-3 col-sm-12 mt-2">Lead/Buyer Type
                                    </div>
                                    <div class="col-md-4 col-sm-12 mt-2">
                                      <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="type"
                                               value="INDIVIDUAL"
                                               :checked="lead.type == 'INDIVIDUAL'"
                                               v-model="lead.type"
                                               @change="typeChanged"
                                        >
                                        <label class="form-check-label" for="inlineRadio1">Individual</label>
                                      </div>
                                      <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               v-model="lead.type"
                                               name="type"
                                               :checked="lead.type == 'COMPANY'"
                                               @change="typeChanged"
                                               value="COMPANY">
                                        <label class="form-check-label" for="inlineRadio2">Company</label>
                                      </div>

                                    </div>
                                    <div class="col-6">

                                    </div>

                                  </div>
                                    <div class="row">
                                        <div class="col-12">


                                                <div class="border">
                                                    <ul class="nav nav-pills custom-hover-nav-tabs" role="tablist">
                                                        <li class="nav-item" role="presentation" style="width: 50%;">
                                                            <a style="min-width: 200px; width: 100%;" href="#custom-hover-customere" data-bs-toggle="tab" aria-expanded="false" class="nav-link active" aria-selected="true" role="tab">
                                                                <i class="ri-user-fill nav-icon nav-tab-position"></i>
                                                                <h5 class="nav-titl nav-tab-position m-0">Customer</h5>
                                                            </a>
                                                        </li>
                                                        <li class="nav-item" role="presentation" style="width: 50%;">
                                                            <a style="min-width: 200px;width: 100%;" href="#documents-tab-pane" data-bs-toggle="tab" aria-expanded="true" class="nav-link" aria-selected="false" role="tab" tabindex="-1">
                                                                <i class="ri-file-text-line nav-icon nav-tab-position"></i>
                                                                <h5 class="nav-titl nav-tab-position m-0">Documents</h5>
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <div class="card-body" style=" min-height: 500px;">
                                                    <div class="tab-content text-muted">
                                                        <div class="tab-pane active show" id="custom-hover-customere" role="tabpanel">
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
                                                                               v-model="lead.last_name"
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
                                                                               name="alias"
                                                                               id="aliases"
                                                                                v-model="lead.aliases"
                                                                               placeholder="Enter aliases" >


                                                                    </div>
                                                                    <div class="col-md-2 col-sm-12 mt-2">Address</div>
                                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                                        <input type="text" class="form-control inputs"
                                                                               name="address"
                                                                               v-model="lead.address"

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
                                                                               v-model="lead.mobile"
                                                                               placeholder="Enter mobile"
                                                                               readonly
                                                                        >
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
                                                                               v-model="lead.phone"
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
                                                                               v-model="lead.email"
                                                                               placeholder="Enter email address">
                                                                        <div class="invalid-feedback"> Please enter a
                                                                            valid email address.
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
                                                                        ></v-select>
                                                                        <div class="invalid-feedback"> Please choose
                                                                            nationality.
                                                                        </div>
                                                                    </div>


                                                                </div>

                                                                    <div class="row">

                                                                        <div class="col-md-2 col-sm-12 mt-2">Gender</div>
                                                                        <div class="col-md-4 col-sm-12 mt-2">







                                                                            <select class="form-select inputs"
                                                                                    name="gender"
                                                                                    id="gender"
                                                                                    v-model="lead.gender"
                                                                            >
                                                                                <option selected disabled value="">Select
                                                                                    gender
                                                                                </option>
                                                                                <option :selected="lead.gender === 'Male'" value="Male">Male
                                                                                </option>
                                                                                <option :selected="lead.gender === 'Female'" value="Female">
                                                                                    Female
                                                                                </option>
                                                                                <option :selected="lead.gender === 'Unknown'" value="Unknown">
                                                                                    Unknown
                                                                                </option>
                                                                            </select>


                                                                            <div class="invalid-feedback"> Please choose
                                                                                gender.
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-2 col-sm-12 mt-2">Passport
                                                                            number
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2">
                                                                            <input
                                                                                v-model="lead.passport_number"
                                                                                type="text" class="form-control inputs"
                                                                                name="passport_number" id="passport_number"
                                                                                placeholder="Enter passport number">
                                                                            <div class="invalid-feedback"> Please enter
                                                                                passport number.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2 col-sm-12 mt-2">Date of birth
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2">
                                                                            <flatpickr data-date-format="d/m/Y"
                                                                                        placeholder="Enter date of birth"
                                                                                        class="form-control"
                                                                                        name="dob" v-model="lead.dob"/>
                                                                            <div class="invalid-feedback"> Please enter date
                                                                                of birth.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2 col-sm-12 mt-2">Passport issue date
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2">
                                                                            <flatpickr data-date-format="d/m/Y"
                                                                                       placeholder="Enter passport issue date"
                                                                                       class="form-control" name="passport_issue_date"
                                                                                       id="passport_issue_date"
                                                                                       v-model="lead.passport_issue_date"/>

                                                                        </div>




                                                                        <div class="col-md-2 col-sm-12 mt-2">Place of birth
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2">
                                                                            <input type="text" class="form-control inputs"
                                                                                   v-model="lead.place_of_birth"
                                                                                   placeholder="Enter place of birth">

                                                                        </div>



                                                                        <div class="col-md-2 col-sm-12 mt-2">Passport
                                                                            expiry date
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2">
                                                                            <flatpickr data-date-format="d/m/Y"
                                                                                        placeholder="Enter passport expiry date"
                                                                                        class="form-control" name="passport_expiry"
                                                                                        id="passport_expiry"
                                                                                        v-model="lead['passport_expiry']"/>
                                                                            <div class="invalid-feedback"> Please enter
                                                                                passport expiry date.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2 col-sm-12 mt-2">UAE National
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="uae_national"
                                                                                       id="inlineRadio1" value="1"
                                                                                       :checked="lead.uae_national == '1'"
                                                                                       v-model="lead.uae_national"
                                                                                       @change="uaeNationalChanged"
                                                                                >
                                                                                <label class="form-check-label" for="inlineRadio1">Yes</label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio"
                                                                                       v-model="lead.uae_national"
                                                                                       name="uae_national"
                                                                                       :checked="lead.uae_national == '0'"
                                                                                       @change="uaeNationalChanged"
                                                                                       id="inlineRadio2" value="0">
                                                                                <label class="form-check-label" for="inlineRadio2">No</label>
                                                                            </div>

                                                                        </div>
                                                                      <div class="col-md-2 col-sm-12 mt-2">Name on the passport</div>
                                                                      <div class="col-md-4 col-sm-12 mt-2">
                                                                        <input
                                                                            v-model="lead.name_on_passport"
                                                                            type="text" class="form-control inputs"
                                                                            name="name_on_passport" id="name_on_passport"
                                                                            placeholder="Enter name on the passport">

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


                                                                        <div class="col-md-2 col-sm-12 mt-2">Country of
                                                                            residence
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2"><input
                                                                            v-model="lead.country_of_residence"
                                                                            type="text" class="form-control inputs"
                                                                            name="country_of_residence" id="country_of_residence"
                                                                            placeholder="Enter country of residence">
                                                                            <div class="invalid-feedback"> Please enter
                                                                                country of residence.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2 col-sm-12 mt-2">Emirates ID copy
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2"><input
                                                                            type="file" class="form-control inputs"
                                                                            name="emirates_id_copy" id="emirates_id_copy"
                                                                            placeholder="Enter country of residence">
                                                                            <div class="invalid-feedback"> Please select am emirat id
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </Transition>
                                                                <transition>
                                                                    <div class="row" v-if="lead.uae_national == '0'">
                                                                        <div class="col-md-2 col-sm-12 mt-2">UAE Residency
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="uae_resident"
                                                                                       id="inlineRadio1" value="1"
                                                                                       :checked="lead.uae_residency == '1'"
                                                                                       v-model="lead.uae_residency"
                                                                                >
                                                                                <label class="form-check-label" for="inlineRadio1">Yes</label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio"
                                                                                       v-model="lead.uae_residency"
                                                                                       name="uae_resident"
                                                                                       :checked="lead.uae_residency == '0'"
                                                                                       id="inlineRadio2" value="0">
                                                                                <label class="form-check-label" for="inlineRadio2">No</label>
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
                                                                            placeholder="Enter residency visa no.">
                                                                            <div class="invalid-feedback"> Please enter residency visa no.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2 col-sm-12 mt-2">Emirates ID No.
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2"><input
                                                                            v-model="lead.emirates_id"
                                                                            type="text" class="form-control inputs"
                                                                            name="emirates_id" id="emirates_id"
                                                                            placeholder="Enter Emirates id.">
                                                                            <div class="invalid-feedback"> Please enter Emirates id no.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2 col-sm-12 mt-2">Visa expiry date
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-12 mt-2"><input
                                                                            v-model="lead.visa_expiry_date"
                                                                            type="text" class="form-control inputs"
                                                                            name="visa_expirty_date" id="visa_expirty_date"
                                                                            placeholder="Enter visa expiry date.">
                                                                            <div class="invalid-feedback"> Please enter Visa expirty date.
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </transition>

                                                                <div class="row">
                                                                    <div class="col-md-2 col-sm-12 mt-2">Fund Source
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                                        <select class="form-select inputs"
                                                                                name="fund_source"
                                                                                id="fund_source"
                                                                                v-model="lead.fund_source">
                                                                            <option selected disabled value="">Select</option>
                                                                            <option v-for="(item, index) in fundSource" :value="index"
                                                                                    :key="index">
                                                                                {{ item }}
                                                                            </option>
                                                                        </select>

                                                                    </div>

                                                                    <div v-if="lead.fund_source == 'OTHER'" class="col-md-2 col-sm-12 mt-2">
                                                                        Other
                                                                    </div>
                                                                    <div v-if="lead.fund_source == 'OTHER'" class="col-md-4 col-sm-12 mt-2">
                                                                        <input type="text" class="form-control inputs"
                                                                               name="fund_source_other"
                                                                               id="fund_source_other"
                                                                               v-model="lead.fund_source_other"
                                                                               placeholder="Enter other fund source">
                                                                        <div class="invalid-feedback"> Please enter
                                                                            other fund source.
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
                                                                               placeholder="Enter occupation">
                                                                        <div class="invalid-feedback"> Please enter
                                                                            occupation.
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 col-sm-12 mt-2">Years In Present Role
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                                        <input type="text" class="form-control inputs"
                                                                               name="occupation"
                                                                               id="occupation"
                                                                               v-model="lead.year_of_experience"
                                                                               placeholder="Enter years in present role">
                                                                        <div class="invalid-feedback"> Please enter
                                                                            years of experience in current role.
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 col-sm-12 mt-2">Employer / Establishment Name
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                                        <input type="text" class="form-control inputs"
                                                                               name="employer_name"
                                                                               id="employer_name"
                                                                               v-model="lead.employer_name"
                                                                               placeholder="Enter years in present role">
                                                                        <div class="invalid-feedback"> Please enter
                                                                            employer name
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 col-sm-12 mt-2">Employer / Establishment Address
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                                        <input type="text" class="form-control inputs"
                                                                               name="employer_address"
                                                                               id="employer_address"
                                                                               v-model="lead.employer_address"
                                                                               placeholder="Enter employer address">
                                                                        <div class="invalid-feedback"> Please enter
                                                                            employer address
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-2 col-sm-12 mt-2">
                                                                        Annual Income *
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                                        <input type="text" class="form-control inputs"
                                                                               name="annual_income"
                                                                               id="annual_income"
                                                                               v-model="lead.annual_income"
                                                                               placeholder="Enter annual income">
                                                                        <div class="invalid-feedback"> Please enter
                                                                            annual income
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 col-sm-12 mt-2">
                                                                        Payment Method *
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-12 mt-2">
                                                                        <select class="form-select inputs"
                                                                                name="payment_method"
                                                                                id="payment_method"
                                                                                v-model="lead.payment_method">
                                                                            <option selected disabled value="">Select</option>
                                                                            <option value="CASH">Cash</option>
                                                                            <option value="CHEQUE">Cheque</option>
                                                                            <option value="BANK_TRANSFER">Bank Transfer</option>
                                                                            <option value="MORTGAGE">Mortgage</option>
                                                                            <option value="CRYPTO">Crypto</option>
                                                                            <option value="OTHER">Other</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 col-sm-12 mt-2">
                                                                        Are you current/former Politically Exposed Person (PEP)? *
                                                                    </div>
                                                                    <div class="col-md-6 col-sm-12 mt-2">
                                                                        <select class="form-select inputs"
                                                                                name="payment_method"
                                                                                id="payment_method"
                                                                                v-model="lead.political">
                                                                            <option selected disabled value="">Select</option>
                                                                            <option value="YES">Yes</option>
                                                                            <option value="NO">No</option>
                                                                            <option value="OTHER">Other</option>

                                                                        </select>
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
                                                                           placeholder="Enter first name" required>
                                                                    <div class="invalid-feedback"> Please enter
                                                                        first name.
                                                                    </div>
                                                                </div>


                                                                <div class="col-md-6  col-sm-12 mt-2">Former Name (If any)
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    <input type="text" class="form-control inputs"
                                                                           name="last_name"
                                                                           id="last_name"
                                                                           v-model="lead.company_former_name"
                                                                           placeholder="Enter former name">
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">Trading Name (If any)
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    <input type="text" class="form-control inputs"
                                                                           name="last_name"
                                                                           id="last_name"
                                                                           v-model="lead.company_trading_name"
                                                                           placeholder="Enter trading name">
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">Trade License Number
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    <input type="text" class="form-control inputs"
                                                                           name="last_name"
                                                                           id="last_name"
                                                                           v-model="lead.company_trad_license_no"
                                                                           placeholder="Enter trading number">
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    Country of incorporation/registration
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                  <v-select
                                                                      v-model="lead.company_coi"
                                                                      :reduce="country => country.id"
                                                                      :options="countries"
                                                                      label="name"
                                                                      @search="countries"
                                                                      placeholder="Country of incorporation"
                                                                  ></v-select>

                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    Date of incorporation/registration
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                  <flatpickr type="date" data-date-format="d/m/Y"
                                                                              v-model="lead.company_coi_date"
                                                                              class="form-control"   name="company_coi_date"
                                                                              placeholder="Enter Date pf incorporation" id="company_coi_date"/>

                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">Registered Address
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    <input type="text" class="form-control inputs"
                                                                           name="last_name"
                                                                           id="last_name"
                                                                           v-model="lead.address"
                                                                           placeholder="Enter trading name">
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">Address of Principal Place of Business
                                                                    (if different from Registered Address)
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    <input type="text" class="form-control inputs"
                                                                           name="last_name"
                                                                           id="last_name"
                                                                           v-model="lead.company_principal_address"
                                                                           placeholder="Enter Address of Principal Place of Business">
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    Telephone number
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    <input type="text" class="form-control inputs"
                                                                           name="last_name"
                                                                           id="last_name"
                                                                           v-model="lead.phone"
                                                                           placeholder="Enter Telephone number">
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    Email address
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    <input type="text" class="form-control inputs"
                                                                           name="last_name"
                                                                           id="last_name"
                                                                           v-model="lead.email"
                                                                           placeholder="Enter email">
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    Trade license issue date
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    <flatpickr type="date" data-date-format="d/m/Y"
                                                                                class="form-control"   name="trade_license_issue_date"
                                                                                placeholder="Enter trade license issue date" id="trade_license_issue_date"

                                                                                v-model="lead.trade_license_issue_date"/>

                                                                </div>

                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    Trade license expiry date
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">

                                                                    <flatpickr type="date" data-date-format="d/m/Y"
                                                                                class="form-control"   name="trade_license_expiry_date"
                                                                                placeholder="Enter trade license expiry date" id="trade_license_expiry_date"
                                                                                v-model="lead.trade_license_expiry_date"/>
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    Trade license issuing authority
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    <input type="text" class="form-control inputs"
                                                                           name="last_name"
                                                                           id="last_name"
                                                                           v-model="lead.trade_license_issuing_authority"
                                                                           placeholder="Enter issuing authority">
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    Does this company have any UBO?
                                                                </div>
                                                                <div class="col-md-6  col-sm-12 mt-2">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="uae_national"
                                                                               id="inlineRadio1" value="1"
                                                                               :checked="lead.have_ubo == '1'"
                                                                               v-model="lead.have_ubo"
                                                                        >
                                                                        <label class="form-check-label" for="inlineRadio1">Yes</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                               v-model="lead.have_ubo"
                                                                               name="uae_national"
                                                                               :checked="lead.have_ubo == '0'"
                                                                               id="inlineRadio2" value="0">
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
                                                                            <th>Full name, Alias

                                                                            </th>
                                                                          <th>
                                                                            <button type="button" class="btn btn-sm btn-outline-success float-end" @click="addUbo()">
                                                                              <i class="mdi mdi-plus-thick"></i>
                                                                            </button>
                                                                          </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <tr v-if="lead.lead_meta" v-for="(ubo, index) in ubo_list" :key="index">
                                                                            <td>{{index+1}}</td>
                                                                            <td>{{ubo.json.first_name}} {{ubo.json.last_namee}}
                                                                            </td>
                                                                          <td>

                                                                            <button type="button" class="btn btn-sm btn-outline-danger float-end" @click="addUbo()">
                                                                              <i class="mdi mdi-delete"></i>
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm btn-outline-info float-end" @click="addUbo()">
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
                                                                                   :href="leadDocumentFromDb[index].path" target="_blank"
                                                                                   class="btn btn-outline-success btn-icon waves-effect waves-light"
                                                                                   style="padding: 0.3rem;"><i class="ri-eye-line"></i></a>
                                                                                <button v-if="leadDocumentFromDb[index]"
                                                                                        class="btn btn-outline-danger btn-icon waves-effect waves-light"
                                                                                        style="padding: 0.3rem;" type="button"
                                                                                        @click="deleteLeadDocument(lead.id,leadDocumentFromDb[index].id)"
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
                                <div class="col-lg-3">
                                    <div class="row g-2">
                                        <h5 class="mt-3">Other Information</h5>
                                        <div class="col-md-12">
                                            <label for="zip" class="form-label">Name: </label>

                                           <span> {{lead.first_name}} {{lead.last_name}}</span>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="zip" class="form-label">Lead Source: </label>

<!--                                            <v-select-->
<!--                                                v-model="lead.source_id"-->
<!--                                                :reduce="leadSource => leadSource.id"-->
<!--                                                :options="leadSource"-->
<!--                                                label="name"-->
<!--                                                @search="leadSource"-->
<!--                                                placeholder="Type to search lead source"-->
<!--                                            ></v-select>-->
                                            <i  v-if="leadProp.lead_source != null" :class="leadProp.lead_source.icon" style="font-size: 20px;"></i>
                                            {{ leadProp?.lead_source?.name }}

                                        </div>
                                        <div class="col-md-12">
                                            <label for="zip" class="form-label">Lead Medium*</label>
                                            <br>
                                            <i :class="lead.lead_medium.icon" style="font-size: 20px;"></i>
                                            {{ lead?.lead_medium?.name }}
                                        </div>
                                        <div class="col-12" id="messageDiv">
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
                                    <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                       data-bs-dismiss="modal" @click="leadModalClosed()"><i
                                        class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="submit" class="btn btn-success action-add">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div v-else>
                        <!-- Border spinner -->
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <div>
        <UboModal :lead="lead"></UboModal>
    </div>
</template>
<script setup>
import {ref, onMounted, watchEffect, onUnmounted, inject, watch} from 'vue'
import {useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
import useLeads from "../../../pages/leads/composables/leads";
import axios from "axios";
import flatpickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/material_blue.css';
import UboModal from "./UboModal.vue";
import DynamicSelect from 'vue-dynamic-select'

const emit = defineEmits(['removeLeadModal','refreshLeadList']);
const modalLoader = ref(true);
import useGlobalHelper from "../../../composables/helpers/global";
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';

const {leadSource, getLeadSource,} = useLeads();
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
    }
});
const genders = ref(
    {value: 'Male', text: 'Male'},
    {value: 'Female', text: 'Female'},
    {value: 'Unknown', text: 'Unknown'}
);
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

const fundSource = ref({
    EMPLOYMENT_INCOME: 'Employment Income',
    SALE_OF_INVESTMENTS: 'Sale of Investments or Properties',
    BUSINESS_INCOME: 'Business Income',
    SAVINGS: 'Savings',
    OTHER: 'Other',
});

const formData = ref({});


const leadType = ref({
    INDIVIDUAL: 'Individual',
    COMPANY: 'Company/Entity',
    REFERRAL: 'Referral',
});

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
    let typeData = props.leadProp.lead_meta.find(item => item.meta_key === 'type');
    let type = (lead != null && lead.type != undefined) ? lead.type : typeData;

    if (type == undefined) {
        documentsType.value = {};
    } else if (type == 'INDIVIDUAL') {
        documentsType.value = individualDocuments.value;
    } else {
        documentsType.value = companyDocuments.value;
    }

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
        }, 2000);
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
    }
}

watch(() => lead.value, (val) => {
    if (val != null) {
      if(lead.type == '' || lead.type == undefined || lead.type == null){
        lead.value.type = 'INDIVIDUAL';
      }
        typeChanged();
        setDocuments(val.documents);
        if (val.lead_meta != null) {
            for (let [key, value] of Object.entries(val.lead_meta)) {

          if(value.meta_key == 'company_coi'){
            //convert to integer if value is string
            lead.value[value.meta_key] = parseInt(value.meta_value) ?? null;
          }else{
            lead.value[value.meta_key] = value.meta_value;
          }
                if(value.meta_key == 'ubo'){
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
const onSubmit = async () => {
    let formData1 = new FormData();
    // for (let [key, value] of Object.entries(formData.value)) {
    //     formData1.append(key, value);
    // }

    for (let [key, value] of Object.entries(lead.value)) {
        formData1.append(key, value ? value : '');
    }
    selectedDocument.value['other_documents'].forEach((item, index) => {
        formData1.append('other_documents[' + index + '][file]', item.file);
        formData1.append('other_documents[' + index + '][title]', item.name);
    });

    await updateLead(formData1, lead.value.id,props.deals.id);



    //wait for the updateLead to finish then close the modal if success
    // setTimeout(() => {
    //     $('#leadFormModal').modal('hide');
    // }, 2000);


    props.leadProp = lead.value;
}
watch(() => formStatus.value, (val) => {
  if(formStatus.value == 'SUCCESS') {
    emit('refreshLeadList');
    formStatus.value = 'PENDING';
  }

});
const addUbo = () => {
    $('#uboModal').modal('show');
}
onMounted(() => {

    getLead(props.leadProp.id)
    getLeadSource();


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
        }, 1000);
    }
    if (!leadResources.leadTypes) {
        getLeadTypes();
        leadResources.leadTypes = leadTypes;
    }
    // typeChanged();

});

onUnmounted(() => {
    $('#leadFormModal').modal('hide');
    modalLoader.value = true;
});

</script>
<style scoped>
.modal {
    position: fixed !important;

}

.msg-customer {
    width: 15rem;
    border: 1px dashed #00000030;
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
</style>
