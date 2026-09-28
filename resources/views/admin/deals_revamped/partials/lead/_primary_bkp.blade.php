<div>
    <div id="editLeadForm" class="d-none">
        <div class="row mt-2">
            <div class="col-md-12 text-center">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="changeForm btn-check" name="lead-leadtype" id="leadtype1"
                        autocomplete="off" value="individual">
                    <label class="btn btn-sm btn-outline-secondary" for="leadtype1">Individual</label>

                    <input type="radio" class="changeForm btn-check" name="lead-leadtype" id="leadtype2"
                        autocomplete="off" value="company">
                    <label class="btn btn-sm btn-outline-secondary" for="leadtype2">Company</label>
                </div>
            </div>
        </div>

        <div class="row mt-3" id="individualForm">
            <input type="hidden" name="lead-id" id="lead-id">
            <input type="hidden" name="deal_id" id="lead-deal_id">
            <div class="col-md-6">
                <div class="row mt-3 companyForm d-none">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Company Name <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                        </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            id="lead-first_name" name="lead-first_name"  placeholder="Company Name">
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row mt-3 companyForm d-none">
                    <div class="col-md-4">
                        <label for="company_former_name" class="form-label">Company Former Name<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                        </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            id="lead-company_former_name" name="lead-company_former_name" value=""
                            placeholder="Company Former Name">
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="row mt-3 companyForm d-none">
                    <div class="col-md-4">
                        <label for="company_trading_name" class="form-label">Company Trading Name<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                        </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            id="lead-company_trading_name" name="lead-company_former_name" value=""
                            placeholder="Company Trading Name">
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row mt-3 companyForm d-none">
                    <div class="col-md-4">
                        <label for="company_trade_license_no" class="form-label">Company Trade License No<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                        </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            id="lead-company_trade_license_no" name="lead-company_former_name" value=""
                            placeholder="Company Trade License No">
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row mt-3 companyForm d-none">
                    <div class="col-md-4">
                        <label for="company_coi" class="form-label">Company Coi<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                        </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            id="lead-company_coi" name="lead-company_former_name" value=""
                            placeholder="Company Coi">
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="row mt-3 companyForm d-none">
                    <div class="col-md-4">
                        <label for="company_coi_date" class="form-label">Company Coi Date<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                        </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            id="lead-company_coi_date" name="lead-company_former_name" value=""
                            placeholder="Company Coi Date">
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row mt-3 companyForm d-none">
                    <div class="col-md-4">
                        <label for="company_principal_address" class="form-label">Company Principal Address<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                        </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            id="lead-company_principal_address" name="lead-company_former_name" value=""
                            placeholder="Company Principal Address">
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row mt-3 companyForm d-none">
                    <div class="col-md-4">
                        <label for="trade_license_expiry_date" class="form-label">Trade License Expiry Date<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                        </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            id="lead-trade_license_expiry_date" name="lead-company_former_name" value=""
                            placeholder="Trade License Expiry Date">
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row mt-3 companyForm d-none">
                    <div class="col-md-4">
                        <label for="trade_license_issue_date" class="form-label">Trade License Issue Date<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                        </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            id="lead-trade_license_issue_date" name="lead-company_former_name" value=""
                            placeholder="Trade License Issue Date">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="row mt-3 companyForm d-none">
                    <hr>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="row mt-3 individualForm">
                    <div class="col-md-4">
                        <label for="name" class="form-label">First name <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations "
                            id="lead-first_name" name="lead-first_name"  required placeholder="John">
                    </div>
                </div>
                <div class="row mt-3 individualForm">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Last name <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            id="lead-last_name" name="lead-last_name" value="" placeholder="Doe">
                    </div>
                </div>
                <div class="row mt-3 individualForm">
                    <div class="col-md-4">
                        <label for="aliases" class="form-label">Aliases
                            <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-aliases" id="lead-aliases" required placeholder="Enter aliases"
                            autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger aliasesValidation">
                        </div>
                    </div>
                </div>
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="address" class="form-label">Residential
                            Address<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs  primary-sale secondary-sale reset-validations "
                            name="lead-address" id="lead-address" required placeholder="Enter address"
                            autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger addressValidation">
                        </div>
                    </div>
                </div>
                <div class="row mt-3 individualForm">
                    <div class="col-md-4">
                        <label for="dob" class="form-label">Date of birth <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="date"
                            class="form-control inputs rental-prop  primary-sale secondary-sale reset-validations "
                            name="lead-dob" id="lead-dob" required placeholder="Enter dob" autocomplete="off"
                            data-intl-tel-input-id="0">
                        <div class="text-danger dobValidation">
                        </div>
                    </div>
                </div>
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="place_of_birth" class="form-label">Place of birth <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">

                        <select
                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs"
                            id="lead-place_of_birth" name="lead-place_of_birth" required>

                            @foreach ($countries ?? [] as $country)
                                <option value="{{ $country->name }}">
                                    {{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="gender" class="form-label">Gender <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                    </div>
                    <div class="col-md-8">
                        <select
                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs  r"
                            id="lead-gender" name="lead-gender" required>

                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="nationality_id" class="form-label">Nationality <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                    </div>
                    <div class="col-md-8">
                        <select
                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs"
                            id="lead-nationality_id" name="lead-nationality_id" required>
                            @foreach ($countries ?? [] as $country)
                                <option value="{{ $country->id }}">
                                    {{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="passport_issue_country" class="form-label">Passport issuing country</label>
                    </div>
                    <div class="col-md-8">
                        <select
                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs"
                            id="lead-passport_issue_country" name="lead-passport_issue_country" required>
                            @foreach ($countries ?? [] as $country)
                                <option value="{{ $country->id }}">
                                    {{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="mobile" class="form-label">Mobile
                            <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="tel"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations checkDuplicate"
                            name="lead-mobile" id="lead-mobile" required placeholder="Enter mobile"
                            autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger mobileValidation">
                        </div>
                        <small>example:+971585858176</small>
                    </div>
                </div>
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="whatsapp" class="form-label">Whatsapp
                            <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="tel"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-whatsapp" id="lead-whatsapp" required placeholder="Enter whatsapp"
                            autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger mobileValidation">
                        </div>
                        <small>example:+971585858176</small>

                    </div>
                </div>
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="phone" class="form-label">Phone
                            <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="tel"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-phone" id="lead-phone" required placeholder="Enter phone" autocomplete="off"
                            data-intl-tel-input-id="0">
                        <div class="text-danger mobileValidation">
                        </div>
                        <small>example:+971585858176</small>
                    </div>
                </div>
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email <span class="required">*</label>
                    </div>
                    <div class="col-md-8">
                        <input type="email"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-email" id="lead-email" required placeholder="Enter email" autocomplete="off"
                            data-intl-tel-input-id="0">
                        <div class="text-danger emailValidation">
                        </div>
                    </div>
                </div>
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="passport_number" class="form-label">Passport Number <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-passport_number" id="lead-passport_number" required
                            placeholder="Enter passport" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger passportValidation">
                        </div>
                    </div>
                </div>
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="passport_issue_date" class="form-label">Passport Issue Date<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                    </div>
                    <div class="col-md-8">
                        <input type="date"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-passport_issue_date" id="lead-passport_issue_date" required
                            placeholder="Enter passport_issue_date" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger passport_issue_dateValidation">
                        </div>
                    </div>
                </div>
                <div class="row mt-3 individualForm companyForm">
                    <div class="col-md-4">
                        <label for="passport_expiry_date" class="form-label">Passport Expiry <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="date"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-passport_expiry_date" id="lead-passport_expiry_date" required
                            placeholder="Enter passport_expiry_date" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger passport_expiry_dateValidation">
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-6 mt-3">
                <div class="row">
                    <div class="col-md-4">
                        <label for="source_id" class="form-label">Source</label>
                    </div>
                    <div class="col-md-8">
                        <select
                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs"
                            id="lead-source_id" name="lead-source_id" required>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source->id }}">
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3">

                <div class="row">
                    <div class="col-md-4">
                        <label for="lead_medium_id" class="form-label">Medium</label>
                    </div>
                    <div class="col-md-8">
                        <select
                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs"
                            id="lead-lead_medium_id" name="lead-lead_medium_id" required>
                            @foreach ($mediums ?? [] as $medium)
                                <option value="{{ $medium->id }}">
                                    {{ $medium->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="fund_source" class="form-label">Fund Source <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <select
                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs"
                            id="lead-fund_source" name="lead-fund_source" required>

                            <option value="1"> 1</option>
                            <option value="2"> 2</option>
                            <option value="3"> 3</option>


                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="occupation" class="form-label">Occupation <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">

                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-occupation" id="lead-occupation" required placeholder="Enter occupation"
                            autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger occupationValidation"> </div>

                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="employer_name" class="form-label">Employer / Establishment Name<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-employer_name" id="lead-employer_name" required
                            placeholder="Enter employer_name" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger annual_incomeValidation">
                        </div>


                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="annual_income" class="form-label">Annual Income <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="number"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-annual_income" id="lead-annual_income" required
                            placeholder="Enter annual_income" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger annual_incomeValidation">
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-6">
                <div class="row mt-3">
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="years_in_role" class="form-label">Years In Present Role <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="number"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-years_in_role" id="lead-years_in_role" required
                            placeholder="Enter years_in_role" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger years_in_roleValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="establishment_address" class="form-label">Employer / Establishment
                            Address<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="text"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="lead-establishment_address" id="lead-establishment_address" required
                            placeholder="Enter establishment_address" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger establishment_addressValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="payment_method" class="form-label">Payment Method<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <select
                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs"
                            id="lead-payment_method" name="lead-payment_method" required>

                            <option value="1">1</option>
                            <option value="2">2</option>

                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="politically_exposed_person" class="form-label">Current/former
                            Politically Exposed Person (PEP)? <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <select
                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs"
                            id="lead-politically_exposed_person" name="lead-politically_exposed_person" required>
                            <option value="YES">Yes</option>
                            <option value="No">No</option>

                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-12 text-end">
                <button class="btn btn-primary btn-sm" type="button" id="add-secondary_lead">Add Secondary Lead</button>
            </div>
            

            <div id="append-secondary-lead" class="row">

            </div>
        </div>


        <div class="col-md-12 text-center">
            <button class="btn btn-primary dealLeadForm" type="submit">Save Lead</button>
        </div>
    </div>
</div>



@section('modals')
<div class="row d-none" id="clone-secondary-lead">
    <div class="col-md-12 mt-2">
        <h6>
            Secondary Lead <span id="leadCounter"></span>
        </h6>
    </div>
    <div class="col-md-6">
        <div class="row mt-3">
            <div class="col-md-4">
                <label for="name" class="form-label">First name <span
                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
            </div>
            <div class="col-md-8">
                <input type="text"
                    class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                    name="secondary_first_name[]"
                    placeholder="John">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <label for="name" class="form-label">Last name <span
                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
            </div>
            <div class="col-md-8">
                <input type="text"
                    class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                    name="secondary_last_name[]" >
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <label for="secondary_nationality" class="form-label">Nationality <span
                            class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

            </div>
            <div class="col-md-8">
                <select
                        class="form-control  rental-prop primary-sale secondary-sale reset-validations inputs"
                         name="secondary_nationality_id[]" required>
                        @foreach ($countries ?? [] as $country)
                        <option value="{{ $country->id }}" >
                            {{ $country->name }}</option>
                    @endforeach
                          
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row mt-3">
            <div class="col-md-4">
                <label for="email" class="form-label">Email<span
                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
            </div>
            <div class="col-md-8">
                <input type="email"
                    class="form-control inputs rental-prop primary-sale secondary-sale reset-validations "
                     name="secondary_email[]"  placeholder="John@axcap.ae">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <label for="mobile" class="form-label">Mobile <span
                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
            </div>
            <div class="col-md-8">
                <input type="text"
                    class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                     name="secondary_mobile[]" >
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-js')
    <script>


        $(document).on('change', '.changeForm', function() {
            let value = $(this).val();
            if (value == 'individual') {
                $('.companyForm').addClass('d-none');
                $('.companyForm input').prop('disabled', true);

                $('.individualForm').removeClass('d-none');
                $('.individualForm input').prop('disabled', false);

            } else {
                $('.individualForm').addClass('d-none');
                $('.individualForm input').prop('disabled', true);

                $('.companyForm').removeClass('d-none');
                $('.companyForm input').prop('disabled', false);

            }
        });

    </script>

    <script>


        $(document).on('click', '#add-secondary_lead', function() {
            $(this).prop('disabled', true);
            let html = $('#clone-secondary-lead').html();
            $('#append-secondary-lead').append(html);
            let count = $('#append-secondary-lead .leadCounter').length;
            $('#append-secondary-lead .leadCounter').html(count);
        let optionsForNationality = '';
            natianlity_list.forEach(element => {
                console.log(element.nationality)
                optionsForNationality += `<option value="${element.id}">${element.nationality}</option>`;
            });
            $("#secondary_nationality_id").append(optionsForNationality);
            $("#secondary_nationality_id").select2({
                placeholder : 'Select nationality'
            });

        });
    </script>
@endpush
