<div>
    <div id="editLeadForm" class="d-none">
        <div class="row mt-2">
            <div class="col-md-12 text-center">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="changeForm btn-check lead-disable-on-view-mode" checked
                        name="metaData-leadtype" id="leadtype1" autocomplete="off" value="individual">
                    <label class="btn btn-sm btn-outline-secondary" for="leadtype1">Individual</label>

                    <input type="radio" class="changeForm btn-check lead-disable-on-view-mode"
                        name="metaData-leadtype" id="leadtype2" autocomplete="off" value="company">
                    <label class="btn btn-sm btn-outline-secondary" for="leadtype2">Company</label>
                </div>
            </div>
        </div>
        <input type="hidden" name="lead-id" id="lead-id">
        <input type="hidden" name="deal_id" id="lead-deal_id">
        {{-- // For View And Edit Mode (Lead) --}}
        <input type="text" hidden id="metaData-lead_mode" name="metaData-lead_mode" value="view">
        <div class="row">
            <div class="col-6"></div>
            <div class="col-md-6 mr-4">
                <span id="editLeadFormCloseBtn" class="btn float-end btn-danger btn-sm"
                    style="margin-right: 48px">Close</span>
            </div>
        </div>
        <div class="row companyForm">

            <div class="col-12">
                <h4 class="companyForm d-none">Company Details</h4>
            </div>
            <div class="col-6">
                <table class="table table-bordered table-border-custom table-form">

                    <tbody>
                        <tr>
                            <td width="30%" class="tableThDark tdAdjust">
                                <label for="name" class="form-label">Full Legal Name<span
                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                </label>
                            </td>
                            <td width="70%" colspan="3" class="p-0">
                                <input type="text"
                                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                    id="metaData-company_first_name" name="metaData-company_first_name"
                                    placeholder="Company Name">

                                <div class="invalid-feedback">
                                    Please enter full legal company name.
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td width="30%" class="tableThDark tdAdjust">
                                <label for="company_trade_license_no" class="form-label">Company Trade License No
                                    {{-- <span
                            class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                </label>
                            </td>
                            <td width="70%" colspan="3" class="p-0">
                                <input type="text"
                                    class="deal-form-control inputs  reset-validations lead-disable-on-view-mode height-45"
                                    id="metaData-company_trade_license_no" name="metaData-company_trade_license_no"
                                    value="" placeholder="Company Trade License No">

                                <div class="invalid-feedback">
                                    Please enter company trade license no.
                                </div>

                            </td>
                        </tr>

                        <tr>
                            <td width="30%" class="tableThDark tdAdjust">
                                <label for="company_coi_date" class="form-label">Company COI Date
                                    {{-- <span
                            class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                </label>

                            </td>
                            <td width="70%" colspan="3" class="p-0">
                                <input type="date"
                                    class="deal-form-control inputs   reset-validations lead-disable-on-view-mode"
                                    id="metaData-company_coi_date" name="metaData-company_coi_date" value=""
                                    placeholder="Company COI Date">
                                <p class="text-muted fs-10">Date of incorporation/reg. </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%" class="tableThDark">
                                <label for="trade_license_issue_date" class="form-label">Trade License Issue Date
                                    {{-- <span
                            class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                </label>
                            </td>
                            <td width="70%" colspan="3">
                                <input type="date"
                                    class="deal-form-control inputs  reset-validations lead-disable-on-view-mode"
                                    id="metaData-trade_license_issue_date" name="metaData-trade_license_issue_date"
                                    value="" placeholder="Trade License Issue Date">


                            </td>
                        </tr>
                        <tr>
                            <td width="30%" class="tableThDark tdAdjust">
                                <label for="trade_license_issue_authority" class="form-label">Trade License Issued By
                                    {{-- <span
                            class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                </label>
                            </td>
                            <td width="70%" colspan="3" class="p-0">
                                <input type="text"
                                    class="deal-form-control inputs  reset-validations lead-disable-on-view-mode"
                                    id="metaData-trade_license_issue_authority"
                                    name="metaData-trade_license_issue_authority" value=""
                                    placeholder="Trade License Issuing Authority">

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-bordered table-border-custom table-form">

                    <tbody>
                        <tr>
                            <td width="30%" class="tableThDark tdAdjust">
                                <label for="company_trading_name" class="form-label">Company Trading Name<span
                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                </label>
                            </td>
                            <td width="70%" colspan="3" class="p-0">
                                <input type="text"
                                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode height-45"
                                    id="metaData-company_trading_name" name="metaData-company_trading_name"
                                    value="" placeholder="Company Trading Name">

                            </td>
                        </tr>
                        <tr>
                            <td width="30%" class="tableThDark tdAdjust">
                                <label for="company_coi" class="form-label">Company COI
                                    {{-- <span
                            class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                </label>

                            </td>
                            <td width="70%" colspan="3" class="p-0">
                                <input type="text"
                                    class="deal-form-control inputs  reset-validations lead-disable-on-view-mode"
                                    id="metaData-company_coi" name="metaData-company_coi" value=""
                                    placeholder="Company COI">
                                <p class="text-muted fs-10">Country of incorporation/reg. </p>
                                <div class="invalid-feedback">
                                    Please enter company trade license no.
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td width="30%" class="tableThDark tdAdjust">
                                <label for="company_principal_address" class="form-label">Registered Address<span
                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                </label>
                            </td>
                            <td width="70%" colspan="3" class="p-0">
                                <input type="text"
                                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                    id="metaData-company_principal_address" name="metaData-company_principal_address"
                                    value="" placeholder="Company Principal Address">

                            </td>
                        </tr>
                        <tr>
                            <td width="30%" class="tableThDark tdAdjust">
                                <label for="trade_license_expiry_date" class="form-label">Trade License Expiry Date
                                    {{-- <span
                            class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                </label>
                            </td>
                            <td width="70%" colspan="3" class="p-0">
                                <input type="date"
                                    class="deal-form-control inputs  reset-validations lead-disable-on-view-mode"
                                    id="metaData-trade_license_expiry_date" name="metaData-trade_license_expiry_date"
                                    value="" placeholder="Trade License Expiry Date">

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mt-3">


            {{-- // For View And Edit Mode (Lead) --}}
            {{-- Company Fields --}}
            <div class="row">
                <div class="row col-md-12 mt-3">
                    <hr>
                </div>
            </div>

            {{-- Individual Fields --}}
            <div class="row ">
                <div class="col-md-6">
                    <h4 class="individualForm">Personal Information</h4>
                    <h4 class="companyForm">Representative Person's Information</h4>
                </div>
                {{-- Close Lead Form Button  --}}
                <div class="row">
                    <div class="col-6">
                        <table class="table table-bordered table-border-custom table-form">

                            <tbody>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="name" class="form-label">First Name <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                            <br><small class="text-muted">As per passport</small>
                                        </label>
                                    </td>
                                    <td width="70%" colspan="3" class="p-0 ">
                                        <input type="text"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations  lead-disable-on-view-mode
                                    height-45
                                    "
                                            id="lead-first_name" name="lead-first_name" required
                                            placeholder="Enter First Name">

                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="address" class="form-label">Residential Address
                                            <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                        </label>
                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="text"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                            name="lead-address" id="lead-address" required
                                            placeholder="Enter Residential Address" autocomplete="off"
                                            data-intl-tel-input-id="0">
                                        <div class="text-danger addressValidation">
                                        </div>

                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="gender" class="form-label">Gender <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" class="tdInput p-0" colspan="3">
                                        <select
                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs  lead-disable-on-view-mode"
                                            id="lead-gender" name="lead-gender" required>

                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>

                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="mobile" class="form-label">Mobile
                                            <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>


                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="tel"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations checkDuplicate"
                                            name="lead-mobile" id="lead-mobile" required placeholder="Enter Mobile"
                                            autocomplete="off" data-intl-tel-input-id="0">
                                        <div class="text-danger mobileValidation">
                                        </div>
                                        <small class="text-muted">example:+971585858176</small>

                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="phone" class="form-label">Phone
                                            <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="tel"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                                            name="lead-phone" id="lead-phone" required placeholder="Enter Phone"
                                            autocomplete="off" data-intl-tel-input-id="0">
                                        <div class="text-danger phoneValidation">
                                        </div>
                                        <small class="text-muted">example:+971585858176</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="col-6">
                        <table class="table table-bordered table-border-custom table-form">

                            <tbody>
                                <tr class="individualForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="name" class="form-label">Last Name <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                            <br>
                                            <small class="text-muted">As per passport</small>
                                        </label>
                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="text"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                            id="lead-last_name" name="lead-last_name" value=""
                                            placeholder="Enter Last Name">

                                    </td>
                                </tr>
                                <tr class="individualForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="dob" class="form-label">Date of Birth <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="date"
                                            class="deal-form-control inputs rental-prop   primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                            name="lead-dob" id="lead-dob" required placeholder="Enter DOB"
                                            autocomplete="off" data-intl-tel-input-id="0">
                                        <div class="text-danger dobValidation">
                                        </div>

                                    </td>
                                </tr>
                                <tr class="individualForm  companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="nationality_id" class="form-label">Nationality <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" class="tdInput p-0" colspan="3">
                                        <select
                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                                            id="lead-nationality_id" name="lead-nationality_id" required>
                                            @foreach ($countries ?? [] as $country)
                                                <option value="{{ $country->id }}">
                                                    {{ $country->name }}</option>
                                            @endforeach
                                        </select>

                                    </td>
                                </tr>
                                <tr class="individualForm  companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="whatsapp" class="form-label">Whatsapp
                                            <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="tel"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                                            name="lead-whatsapp" id="lead-whatsapp" required
                                            placeholder="Enter Whatsapp" autocomplete="off"
                                            data-intl-tel-input-id="0">
                                        <div class="text-danger whatsappValidation">
                                        </div>
                                        <small class="text-muted">example:+971585858176</small>
                                    </td>
                                </tr>
                                <tr class="individualForm  companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="email" class="form-label">Email <span
                                                class="required">*</label>
                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="email"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                            name="lead-email" id="lead-email" required placeholder="Enter Email"
                                            autocomplete="off" data-intl-tel-input-id="0">
                                        <div class="text-danger emailValidation">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>



            </div>
            <div class="row ">
                {{-- <hr class="mt-3"> --}}
                <div class="row col-md-12 mt-3">
                    <hr>
                </div>
                <h4>Source Details</h4>
                <div class="row">
                    <div class="col-6">
                        <table class="table table-bordered table-border-custom table-form">

                            <tbody>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="source_id" class="form-label">Source *</label>
                                    </td>
                                    <td width="70%" class="tdInput p-0" colspan="3">
                                        <select
                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs"
                                            id="lead-source_id" name="lead-source_id" required>
                                            @foreach ($sources ?? [] as $source)
                                                <option value="{{ $source->id }}">
                                                    {{ $source->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6">
                        <table class="table table-bordered table-border-custom table-form">

                            <tbody>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="lead_medium_id" class="form-label">Medium *</label>

                                    </td>
                                    <td width="70%" class="tdInput p-0" colspan="3">
                                        <select
                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs "
                                            id="lead-lead_medium_id" name="lead-lead_medium_id" required>
                                            @foreach ($mediums ?? [] as $medium)
                                                <option value="{{ $medium->id }}">
                                                    {{ $medium->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="row ">
                <div class="row col-md-12 mt-3">
                    <hr>
                </div>
                <h4>Other Details</h4>


                <div class="row">
                    <div class="col-6">
                        <table class="table table-bordered table-border-custom table-form">

                            <tbody>
                                <tr class="individualForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="aliases" class="form-label">Aliases</label>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="text"
                                            class="deal-form-control inputs  reset-validations lead-disable-on-view-mode"
                                            name="metaData-aliases" id="metaData-aliases" placeholder="Enter Aliases"
                                            autocomplete="off" data-intl-tel-input-id="0">
                                        <div class="text-danger aliasesValidation">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="passport_issue_country" class="form-label">Passport Issuing
                                            Country <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" class="tdInput p-0" colspan="3">
                                        <select
                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode height-45"
                                            id="metaData-passport_issue_country"
                                            name="metaData-passport_issue_country" required>
                                            @foreach ($countries ?? [] as $country)
                                                <option value="{{ $country->id }}">
                                                    {{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="passport_issue_date" class="form-label">Passport Issue Date<span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="date"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode height-45"
                                            name="metaData-passport_issue_date" id="metaData-passport_issue_date"
                                            required placeholder="Enter Passport Issue Date" autocomplete="off"
                                            data-intl-tel-input-id="0">
                                        <div class="text-danger passport_issue_dateValidation">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="fund_source" class="form-label">Fund Source <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="text"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                            name="metaData-fund_source" id="metaData-fund_source"
                                            placeholder="Enter Fund Source" autocomplete="off">

                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="employer_name" class="form-label fs-10">Employer / Establishment
                                            Name<span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="text"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                            name="metaData-employer_name" id="metaData-employer_name" required
                                            placeholder="Enter Employer Name" autocomplete="off"
                                            data-intl-tel-input-id="0">
                                        <div class="text-danger annual_incomeValidation">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="years_in_role" class="form-label">Years In Present Role <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="number"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode
                                        height-45
                                        "
                                            name="metaData-years_in_role" id="metaData-years_in_role" required
                                            placeholder="Enter Years In Role" autocomplete="off"
                                            data-intl-tel-input-id="0">
                                        <div class="text-danger years_in_roleValidation">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="politically_exposed_person"
                                            class="form-label fs-10">Current/former
                                            Politically Exposed Person (PEP)? <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale  label-reset-validations "></span></label>

                                    </td>
                                    <td width="70%" class="tdInput p-0" colspan="3">
                                        <select
                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode
                                        height-45
                                        "
                                            id="metaData-politically_exposed_person"
                                            name="metaData-politically_exposed_person" required>
                                            <option value="YES">Yes</option>
                                            <option value="No">No</option>

                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6">
                        <table class="table table-bordered table-border-custom table-form">

                            <tbody>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="place_of_birth" class="form-label">Place of Birth <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>


                                    </td>
                                    <td width="70%" class="tdInput p-0" colspan="3">
                                        <select
                                            class="deal-form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                                            id="metaData-place_of_birth" name="metaData-place_of_birth" required>

                                            @foreach ($countries ?? [] as $country)
                                                <option value="{{ $country->name }}">
                                                    {{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="passport_number" class="form-label">Passport Number <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>


                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="text"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                            name="metaData-passport_number" id="metaData-passport_number" required
                                            placeholder="Enter Passport" autocomplete="off"
                                            data-intl-tel-input-id="0">
                                        <div class="text-danger passportValidation">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="passport_expiry" class="form-label">Passport Expiry Date<span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>


                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="date"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode
                                        height-45
                                        "
                                            name="metaData-passport_expiry" id="metaData-passport_expiry" required
                                            placeholder="Enter Passport Issue Date" autocomplete="off"
                                            data-intl-tel-input-id="0">
                                        <div class="text-danger passport_expiryValidation">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="occupation" class="form-label">Occupation <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>


                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="text"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                            name="metaData-occupation" id="metaData-occupation" required
                                            placeholder="Enter Occupation" autocomplete="off"
                                            data-intl-tel-input-id="0">
                                        <div class="text-danger occupationValidation"> </div>

                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="annual_income" class="form-label">Annual Income <span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>


                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="number"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                            name="metaData-annual_income" id="metaData-annual_income" required
                                            placeholder="Enter Annual Income" autocomplete="off"
                                            data-intl-tel-input-id="0">
                                        <div class="text-danger annual_incomeValidation">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="individualForm companyForm">
                                    <td width="30%" class="tableThDark tdAdjust">
                                        <label for="establishment_address" class="form-label fs-10">Employer /
                                            Establishment
                                            Address<span
                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>


                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="text"
                                            class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode height-45"
                                            name="metaData-establishment_address" id="metaData-establishment_address"
                                            required placeholder="Enter Establishment Address" autocomplete="off"
                                            data-intl-tel-input-id="0">
                                        <div class="text-danger establishment_addressValidation">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>



                {{-- <div class="row col-md-6 mt-3 individualForm  companyForm">
                    <div class="col-md-4">
                        <label for="passport_expiry" class="form-label">Passport Expiry <span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <input type="date"
                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                            name="metaData-passport_expiry" id="metaData-passport_expiry" required
                            placeholder="Enter Passport Expiry" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger passport_expiryValidation">
                        </div>
                    </div>
                </div> --}}

                {{-- <div class="row col-md-6 mt-3 individualForm  companyForm">
                    <div class="col-md-4">
                        <label for="payment_method" class="form-label">Payment Method<span
                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                    </div>
                    <div class="col-md-8">
                        <select
                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                            id="metaData-payment_method" name="metaData-payment_method" required>

                            <option value="Cash">Cash</option>
                            <option value="Mortgage">Mortgage</option>

                        </select>
                    </div>
                </div> --}}


            </div>



            {{-- Secondary Leads Data --}}
            <div class="row" id="secondary_leads">

            </div>



            <div class="row col-md-12 mt-3 mb-3">
                <div class="col-md-12 ">
                    <button class="btn btn-primary lead-disable-on-view-mode" type="button"
                        id="add-secondary-lead">Add Secondary <span class="tenant-buyer-title">Buyer</span></button>
                </div>
            </div>

        </div>

    </div>
</div>


@push('page-js')
    <script>
        // setDateMaxToday('lead-dob');
        // setDateMaxToday('metaData-passport_issue_date');


        // lead-mobile
        let mobileInput = initPhoneValidator('lead-mobile')
        var input = document.querySelector("#lead-mobile");
        var iti = window.intlTelInput(input, {
            initialCountry: "ae",
        });


        // lead-phone
        let mobileInput2 = initPhoneValidator('lead-phone')
        var input2 = document.querySelector("#lead-phone");
        var iti2 = window.intlTelInput(input2, {
            initialCountry: "ae",
        });

        //lead-whatsapp
        let mobileInput3 = initPhoneValidator('lead-whatsapp')
        var input3 = document.querySelector("#lead-whatsapp");
        var iti3 = window.intlTelInput(input3, {
            initialCountry: "ae",
        });

        input.addEventListener("countrychange", function() {
            var selectedCountryData = iti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            var countryFlag = selectedCountryData.iso2;


            if (iti.isValidNumber()) {
                $(".mobileValidation").html("");
                $('input[name="lead-mobile"]').val(iti.getNumber());
            } else {
                $(".mobileValidation").html("Please enter a valid mobile number with Country Code");
            }
        });


        input2.addEventListener("countrychange", function() {
            var selectedCountryData = iti2.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            var countryFlag = selectedCountryData.iso2;


            if (iti2.isValidNumber()) {
                $(".phoneValidation").html("");
                $('input[name="lead-phone"]').val(iti2.getNumber());
            } else {

                $(".phoneValidation").html("Please enter a valid phone number with Country Code");
            }
        });

        input3.addEventListener("countrychange", function() {
            var selectedCountryData = iti3.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            var countryFlag = selectedCountryData.iso2;


            if (iti3.isValidNumber()) {
                $(".whatsappValidation").html("");
                $('input[name="lead-whatsapp"]').val(iti3.getNumber());
            } else {

                $(".whatsappValidation").html("Please enter a valid whatsapp number with Country Code");
            }
        });



        $(document).on('change', '#lead-mobile', function() {


            if (mobileInput.getNumber() == "") {
                $(".mobileValidation").html("");
                return false;
            }

            if (mobileInput.isValidNumber()) {
                $('input[name="lead-mobile"]').val(iti.getNumber());
                $(".mobileValidation").html("");
            } else {
                $(".mobileValidation").html("Please enter a valid mobile number with country code");
            }
        })

        $(document).on('change', '#lead-phone', function() {

            if (mobileInput2.getNumber() == "") {
                $(".phoneValidation").html("");
                return false;
            }


            if (mobileInput2.isValidNumber()) {
                $('input[name="lead-phone"]').val(iti2.getNumber());
                $(".phoneValidation").html("");
            } else {
                $(".phoneValidation").html("Please enter a valid phone number with country code");
            }
        })

        $(document).on('change', '#lead-whatsapp', function() {

            if (mobileInput3.getNumber() == "") {
                $(".whatsappValidation").html("");
                return false;
            }

            if (mobileInput3.isValidNumber()) {
                $('input[name="lead-whatsapp"]').val(iti3.getNumber());
                $(".whatsappValidation").html("");
            } else {
                $(".whatsappValidation").html("Please enter a valid whatsapp number with country code");
            }
        })

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

            if ($("#lead-mobile").val() != '') {
                $("#lead-mobile").attr("disabled", true);
            }
            if ($("#lead-phone").val() != '') {
                $("#lead-phone").attr("disabled", true);
            }
            if ($("#lead-whatsapp").val() != '') {
                $("#lead-whatsapp").attr("disabled", true);
            }
        });


        $(document).on('click', '#add-secondary-lead', function() {
            // get last child of secondary_leads div

            let lastChild = $('#secondary_leads').children().last();
            let childIndex = 1;


            if (lastChild.length > 0) {

                childIndex = parseInt(lastChild.attr('id').split('_')[2]) + 1 ?? 1;
            }
            let title_text = "Buyer";
            if (deal.value.type == "Rental") {
                title_text = "Tenant";
            }

            let html = ` <div class="row" style="padding-right: 32px" id="secondary_child_` +
                childIndex + `">
                <hr class="mt-5">
            <input type="hidden" name="secondary_lead_id[]" value="0">
    <div class="col-md-6 mt-2">
        <h5>
            Secondary ` + title_text + ` <span id="leadCounter"></span>
        </h5>
    </div>
    <div class="col-md-6 mt-2 ">
        <button type="button" class="btn float-end btn-danger btn-sm" onclick="$('#secondary_child_` + childIndex +
                `').remove()">X</button>
    </div>

  <div class="col-md-6 mt-2">
                                        <table class="table table-bordered table-border-custom table-form">

                    <tbody>
                <tr>
                                    <td width="30%" class="tableThDark tdAdjust">
                                      First Name <span
                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                         <input type="text"
                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                    name="secondary_first_name[]"
                    required
                    placeholder="Enter First Name">

                                    </td>
                                </tr>
                                 <tr>
                                    <td width="30%" class="tableThDark tdAdjust">
                                     Email <span
                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                         <input type="email"
                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                    name="secondary_email[]"
                    required
                    placeholder="Enter Email Address">

                                    </td>
                                </tr>
                                 <tr>
                                    <td width="30%" class="tableThDark tdAdjust">
                                     Mobile <span
                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                        <input type="number"
                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                    required
                     name="secondary_mobile[]"
                     placeholder="Enter Mobile Number">

                                    </td>
                                </tr>



                        </tbody>
                        </table>
                                        </div>

    <div class="col-md-6 mt-2">

            <table class="table table-bordered table-border-custom table-form">

                    <tbody>
                <tr>
                                    <td width="30%" class="tableThDark tdAdjust">
                                      Last Name <span
                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                      <input type="text"
                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                    name="secondary_last_name[]"
                    required
                    placeholder="Enter Last Name">

                                    </td>
                                </tr>
                                 <tr>
                                    <td width="30%" class="tableThDark tdAdjust">
                                     Nationality <span
                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                          <select
                        class="form-control  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode nationality_id_` + childIndex + `"
                         name="secondary_nationality_id[]" required>
                        @foreach ($countries ?? [] as $country)
                        <option value="{{ $country->id }}" >
                            {{ $country->name }}</option>
                    @endforeach

                </select>

                                    </td>
                                </tr>




                        </tbody>
                        </table>
    </div>

</div>`
            $('#secondary_leads').append(html);

            $(document).ready(function() {
                $(".nationality_id_" + childIndex).select2();
            });
        });

        // flatpickr('#metaData-company_coi_date', {
        //     "maxDate": new Date(),
        //     allowInput: true,
        //     enableTime: false,
        //     dateFormat: datePickerFormat()
        // });

        // flatpickr('#metaData-trade_license_expiry_date', {
        //     "maxDate": new Date(),
        //     allowInput: true,
        //     enableTime: false,
        //     dateFormat: datePickerFormat()
        // });

        // flatpickr('#metaData-trade_license_issue_date', {
        //     "maxDate": new Date(),
        //     allowInput: true,
        //     enableTime: false,
        //     dateFormat: datePickerFormat()
        // });

        // flatpickr('#lead-dob', {
        //     "maxDate": new Date(),
        //     allowInput: true,
        //     enableTime: false,
        //     dateFormat: datePickerFormat()
        // });

        // flatpickr('#metaData-passport_issue_date', {
        //     "maxDate": new Date(),
        //     allowInput: true,
        //     enableTime: false,
        //     dateFormat: datePickerFormat()
        // });

        // flatpickr('#metaData-passport_expiry', {
        //     "maxDate": new Date(),
        //     allowInput: true,
        //     enableTime: false,
        //     dateFormat: datePickerFormat()
        // });
    </script>
@endpush
