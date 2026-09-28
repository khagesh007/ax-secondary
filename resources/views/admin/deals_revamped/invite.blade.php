@extends('layouts.master-without-nav')
@section('title')
    Assign Lead Form
@endsection
@section('meta_description', 'Lead Generation Form')
@section('css')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        div.dataTables_wrapper div.dataTables_filter input {
            border-color: var(--vz-input-border);
            background-color: var(--vz-input-bg);
            color: var(--vz-body-color);
            line-height: 1.5;
            padding: 0.5rem 0.9rem 0.5rem 1.025rem;
            border-radius: 0.25rem;
            font-size: .8125rem;
        }
    </style>

    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/intl-tel-input/css/intlTelInput.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
@endsection

@section('content')
    @include('admin.partials.loader')

    <div class="auth-page-wrapper pt-2" style="margin-bottom: 30px;">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-2">
                            <img src="{{ asset('assets/images/logo/ax-short-logo.png') }}" alt="">
                        </div>

                    </div>
                </div>
                <div class="row  justify-content-center formDiv">
                    <div class="col-md-12" id="leadDetails">
                        <div class="card mt-4">

                            <div class="card-body p-4">

                                <div>
                                    <div id="editLeadForm" class="">
                                        <div class="row mt-2">
                                            <div class="col-md-12 text-center">
                                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                    <input type="radio" class="changeForm btn-check lead-disable-on-view-mode" checked name="metaData-leadtype" id="leadtype1"
                                                        autocomplete="off" value="individual">
                                                    <label class="btn btn-sm btn-outline-secondary" for="leadtype1">Individual</label>

                                                    <input type="radio" class="changeForm btn-check lead-disable-on-view-mode" name="metaData-leadtype" id="leadtype2"
                                                        autocomplete="off" value="company">
                                                    <label class="btn btn-sm btn-outline-secondary" for="leadtype2">Company</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">

                                            <input type="hidden" name="lead-id" id="lead-id">
                                            <input type="hidden" name="deal_id" id="lead-deal_id">
                                            {{-- // For View And Edit Mode (Lead) --}}
                                                <input type="text" hidden id="metaData-lead_mode" name="metaData-lead_mode" value="view">
                                            {{-- // For View And Edit Mode (Lead) --}}
                                            {{-- Company Fields --}}
                                            <div class="row">
                                                <h4 class="companyForm d-none">Company Details</h4>
                                                <div class="row col-md-6 mt-3 companyForm d-none">
                                                    <div class="col-md-4">
                                                        <label for="name" class="form-label">Full Legal Name<span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            id="metaData-company_first_name" name="metaData-company_first_name" placeholder="Company Name">
                                                    </div>
                                                </div>


                                                <div class="row col-md-6 mt-3 companyForm d-none">
                                                    <div class="col-md-4">
                                                        <label for="company_trading_name" class="form-label">Company Trading Name<span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            id="metaData-company_trading_name" name="metaData-company_trading_name" value=""
                                                            placeholder="Company Trading Name">
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 companyForm d-none">
                                                    <div class="col-md-4">
                                                        <label for="company_trade_license_no" class="form-label">Company Trade License No
                                                            {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                        </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        {{-- rental-prop primary-sale secondary-sale --}}
                                                        <input type="text"
                                                            class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                            id="metaData-company_trade_license_no" name="metaData-company_trade_license_no"
                                                            value="" placeholder="Company Trade License No">
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 companyForm d-none">
                                                    <div class="col-md-4">
                                                        <label for="company_coi" class="form-label">Company COI
                                                            {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                        </label>
                                                        <p class="text-muted fs-10">Country of incorporation/reg. </p>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                            id="metaData-company_coi" name="metaData-company_coi" value=""
                                                            placeholder="Company COI">
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 companyForm d-none">
                                                    <div class="col-md-4">
                                                        <label for="company_coi_date" class="form-label">Company COI Date
                                                            {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                        </label>
                                                        <p class="text-muted fs-10">Date of incorporation/reg. </p>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date"
                                                            class="form-control inputs   reset-validations lead-disable-on-view-mode"
                                                            id="metaData-company_coi_date" name="metaData-company_coi_date" value=""
                                                            placeholder="Company COI Date">
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 companyForm d-none">
                                                    <div class="col-md-4">
                                                        <label for="company_principal_address" class="form-label">Registered Address<span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            id="metaData-company_principal_address" name="metaData-company_principal_address"
                                                            value="" placeholder="Company Principal Address">
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 companyForm d-none">
                                                    <div class="col-md-4">
                                                        <label for="trade_license_issue_date" class="form-label">Trade License Issue Date
                                                            {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                        </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date"
                                                               class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                               id="metaData-trade_license_issue_date" name="metaData-trade_license_issue_date"
                                                               value="" placeholder="Trade License Issue Date">
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 companyForm d-none">
                                                    <div class="col-md-4">
                                                        <label for="trade_license_expiry_date" class="form-label">Trade License Expiry Date
                                                            {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                        </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date"
                                                            class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                            id="metaData-trade_license_expiry_date" name="metaData-trade_license_expiry_date"
                                                            value="" placeholder="Trade License Expiry Date">
                                                    </div>
                                                </div>

                                                <div class="row col-md-6 mt-3 companyForm d-none">
                                                    <div class="col-md-4">
                                                        <label for="trade_license_issue_authority" class="form-label">Issued By
                                                            {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                        </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                            id="metaData-trade_license_issue_authority" name="metaData-trade_license_issue_authority"
                                                            value="" placeholder="Trade License Issuing Authority">
                                                    </div>
                                                </div>
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
                                                <div class="col-md-6 mr-4">
                                                    <span id="editLeadFormCloseBtn" class="btn float-end btn-danger btn-sm" style="margin-right: 48px">Close</span>
                                                </div>
                                                {{-- Close Lead Form Button  --}}
                                                <div class="row col-md-6 mt-3 individualForm companyForm    ">
                                                    <div class="col-md-4">
                                                        <label for="name" class="form-label">First Name <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                        <br><small class="text-muted">As per passport</small>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations  lead-disable-on-view-mode"
                                                            id="lead-first_name" name="lead-first_name" required placeholder="Enter First Name">
                                                    </div>
                                                </div>

                                                <div class="row col-md-6 mt-3 individualForm ">
                                                    <div class="col-md-4">
                                                        <label for="name" class="form-label">Last Name <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                        <br>
                                                            <small class="text-muted">As per passport</small>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            id="lead-last_name" name="lead-last_name" value="" placeholder="Enter Last Name">
                                                    </div>
                                                </div>

                                                {{-- <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="address" class="form-label">Residential
                                                            Address<span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs  primary-sale secondary-sale reset-validations "
                                                            name="lead-address" id="lead-address" required placeholder="Enter Address"
                                                            autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger addressValidation">
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                <div class="row col-md-6 mt-3 individualForm companyForm">
                                                    <div class="col-md-4">
                                                        <label for="address" class="form-label">Residential Address
                                                            <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="lead-address" id="lead-address" required placeholder="Enter Residential Address"
                                                            autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger addressValidation">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm">
                                                    <div class="col-md-4">
                                                        <label for="dob" class="form-label">Date of Birth <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date"
                                                            class="form-control inputs rental-prop   primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="lead-dob" id="lead-dob" required placeholder="Enter DOB" autocomplete="off"
                                                            data-intl-tel-input-id="0">
                                                        <div class="text-danger dobValidation">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="gender" class="form-label">Gender <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                                    </div>
                                                    <div class="col-md-8">
                                                        <select
                                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs  lead-disable-on-view-mode"
                                                            id="lead-gender" name="lead-gender" required>

                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="nationality_id" class="form-label">Nationality <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                                    </div>
                                                    <div class="col-md-8">
                                                        <select
                                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                                                            id="lead-nationality_id" name="lead-nationality_id" required>
                                                            @foreach ($countries ?? [] as $country)
                                                                <option value="{{ $country->id }}">
                                                                    {{ $country->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="mobile" class="form-label">Mobile
                                                            <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="tel"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations checkDuplicate"
                                                            name="lead-mobile" id="lead-mobile" required placeholder="Enter Mobile"
                                                            autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger mobileValidation">
                                                        </div>
                                                        <small>example:+971585858176</small>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="whatsapp" class="form-label">Whatsapp
                                                            <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="tel"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                                                            name="lead-whatsapp" id="lead-whatsapp" required placeholder="Enter Whatsapp"
                                                            autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger whatsappValidation">
                                                        </div>
                                                        <small>example:+971585858176</small>

                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="phone" class="form-label">Phone
                                                            <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="tel"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations"
                                                            name="lead-phone" id="lead-phone" required placeholder="Enter Phone" autocomplete="off"
                                                            data-intl-tel-input-id="0">
                                                        <div class="text-danger phoneValidation">
                                                        </div>
                                                        <small>example:+971585858176</small>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="email" class="form-label">Email <span class="required">*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="email"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="lead-email" id="lead-email" required placeholder="Enter Email" autocomplete="off"
                                                            data-intl-tel-input-id="0">
                                                        <div class="text-danger emailValidation">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row ">
                                                {{-- <hr class="mt-3"> --}}
                                                <div class="row col-md-12 mt-3">
                                                    <hr>
                                                </div>
                                                <h4>Source Details</h4>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="source_id" class="form-label">Source *</label>
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

                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="lead_medium_id" class="form-label">Medium *</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select
                                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs "
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
                                            <div class="row ">
                                                <div class="row col-md-12 mt-3">
                                                    <hr>
                                                </div>
                                                <h4>Other Details</h4>
                                                <div class="row col-md-6 mt-3 individualForm">
                                                    <div class="col-md-4">
                                                        <label for="aliases" class="form-label">Aliases</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                            name="metaData-aliases" id="metaData-aliases"  placeholder="Enter Aliases"
                                                            autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger aliasesValidation">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="place_of_birth" class="form-label">Place of Birth <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">

                                                        <select
                                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                                                            id="metaData-place_of_birth" name="metaData-place_of_birth" required>

                                                            @foreach ($countries ?? [] as $country)
                                                                <option value="{{ $country->name }}">
                                                                    {{ $country->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="passport_issue_country" class="form-label">Passport Issuing Country <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select
                                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                                                            id="metaData-passport_issue_country" name="metaData-passport_issue_country" required>
                                                            @foreach ($countries ?? [] as $country)
                                                                <option value="{{ $country->id }}">
                                                                    {{ $country->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 d-none">
                                                    <div class="col-md-4">
                                                        <label for="name_on_passport" class="form-label">Name on Passport <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="metaData-name_on_passport" id="metaData-name_on_passport" required
                                                            placeholder="Name on Passport" autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger ">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="passport_number" class="form-label">Passport Number <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="metaData-passport_number" id="metaData-passport_number" required
                                                            placeholder="Enter Passport" autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger passportValidation">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="passport_issue_date" class="form-label">Passport Issue Date<span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="metaData-passport_issue_date" id="metaData-passport_issue_date" required
                                                            placeholder="Enter Passport Issue Date" autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger passport_issue_dateValidation">
                                                        </div>
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

                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="passport_expiry" class="form-label">Passport Expiry Date<span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="metaData-passport_expiry" id="metaData-passport_expiry" required
                                                            placeholder="Enter Passport Issue Date" autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger passport_expiryValidation">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="fund_source" class="form-label">Fund Source <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">

                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="metaData-fund_source" id="metaData-fund_source" placeholder="Enter Fund Source"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="occupation" class="form-label">Occupation <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">

                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="metaData-occupation" id="metaData-occupation" required
                                                            placeholder="Enter Occupation" autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger occupationValidation"> </div>

                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="employer_name" class="form-label">Employer / Establishment Name<span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="metaData-employer_name" id="metaData-employer_name" required
                                                            placeholder="Enter Employer Name" autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger annual_incomeValidation">
                                                        </div>


                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="annual_income" class="form-label">Annual Income <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="number"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="metaData-annual_income" id="metaData-annual_income" required
                                                            placeholder="Enter Annual Income" autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger annual_incomeValidation">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="years_in_role" class="form-label">Years In Present Role <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="number"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="metaData-years_in_role" id="metaData-years_in_role" required
                                                            placeholder="Enter Years In Role" autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger years_in_roleValidation">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="establishment_address" class="form-label">Employer / Establishment
                                                            Address<span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                            name="metaData-establishment_address" id="metaData-establishment_address" required
                                                            placeholder="Enter Establishment Address" autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger establishment_addressValidation">
                                                        </div>
                                                    </div>
                                                </div>
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
                                                <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                    <div class="col-md-4">
                                                        <label for="politically_exposed_person" class="form-label">Current/former
                                                            Politically Exposed Person (PEP)? <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale  label-reset-validations "></span></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select
                                                            class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                                                            id="metaData-politically_exposed_person" name="metaData-politically_exposed_person"
                                                            required>
                                                            <option value="YES">Yes</option>
                                                            <option value="No">No</option>

                                                        </select>
                                                    </div>
                                                </div>

                                            </div>



                                            {{-- Secondary Leads Data --}}
                                            <div class="row" id="secondary_leads">

                                            </div>



                                            <div class="row col-md-12 mt-3 mb-3">
                                                <div class="col-md-12 ">
                                                    <button class="btn btn-primary lead-disable-on-view-mode" type="button" id="add-secondary-lead">Submit</button>
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


                                            if(mobileInput.getNumber() == ""){
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

                                            if(mobileInput2.getNumber() == ""){
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

                                            if(mobileInput3.getNumber() == ""){
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

                                            if($("#lead-mobile").val() != ''){
                                                $("#lead-mobile").attr("disabled", true);
                                            }
                                            if($("#lead-phone").val() != ''){
                                                $("#lead-phone").attr("disabled", true);
                                            }
                                            if($("#lead-whatsapp").val() != ''){
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
                                        <button type="button" class="btn float-end btn-danger btn-sm" onclick="$('#secondary_child_` + childIndex + `').remove()">X</button>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row mt-3 ">
                                            <div class="col-md-4">
                                                <label for="name" class="form-label">First Name <span
                                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text"
                                                    class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                    name="secondary_first_name[]"
                                                    required
                                                    placeholder="Enter First Name">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <label for="name" class="form-label">Last Name <span
                                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text"
                                                    class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                    name="secondary_last_name[]"
                                                    required
                                                    placeholder="Enter Last Name">

                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <label for="secondary_nationality" class="form-label">Nationality <span
                                                            class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                            </div>
                                            <div class="col-md-8">
                                                <select
                                                        class="form-control  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode nationality_id_`+childIndex+`"
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
                                                    class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode "
                                                    required
                                                     name="secondary_email[]"  placeholder="Enter Email Address">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <label for="mobile" class="form-label">Mobile <span
                                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="number"
                                                    class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                    required
                                                     name="secondary_mobile[]"
                                                     placeholder="Enter Mobile Number">

                                            </div>
                                        </div>
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

                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->



                    </div>

                    <div class="col-md-8 mt-5  d-none" id="successMessage">
                        <div class="card ">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                </div>
                                <div class="p-2 mt-4 ">
                                    <div class="row text-center">

                                        <div class="col-md-12">
                                            <h4>Lead Transferred Successfully.</h4>

                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                    </div>

                    <div class="col-md-8 mt-5  d-none" id="alreadyAssigned">
                        <div class="card ">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                </div>
                                <div class="p-2 mt-4 ">
                                    <div class="row text-center">

                                        <div class="col-md-12">
                                            <h4 id="alreadyAssignedMsg"></h4>

                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                    </div>

                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> {{ getApp('name') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
@endsection
@section('script')

    <script src="{{ URL::asset('assets/libs/particles.js/particles.js.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/particles.app.js') }}"></script>
    <script src="{{ asset('assets/libs/intl-tel-input/js/intlTelInput.min.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>

    <script src="{{ asset('assets/js/helpers.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>


        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })
        $(document).on('submit', '#mainForm', function(event) {
            // $("#saveBtn").prop('disabled', true);
            // $('#screen_loader').removeClass('d-none');
            event.preventDefault();

            // function save() {
                var formData = new FormData($("#mainForm")[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    url: "{{ route('api.thirdparty.leadAssignmentPost') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        $('#screen_loader').addClass('d-none');
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $("#saveBtn").prop('disabled', true);
                            $('#leadDetails').addClass('d-none');
                            $('#successMessage').removeClass('d-none');
                        }

                        if (result.status == 3) {
                        $('#alreadyAssigned').removeClass('d-none');
                        $('#leadDetails').addClass('d-none');
                        $('#alreadyAssignedMsg').html(result.msg);
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        $("#saveBtn").prop('disabled', false);
                        let data = jqXhr.responseJSON;
                        console.table(data);
                        if (data.errors) {
                            $.each(data.errors, function(index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                        if (jqXhr.status == 500 || jqXhr.status == 400) {
                            showToastify(data.message, "error")
                        }

                        if (data.message == "CSRF token mismatch.") {
                            showToastify('CSRF Token Mismatch. Please refresh the page.', 'error');
                        }
                        $('#screen_loader').addClass('d-none');

                    },
                });




        });
    </script>
@endsection
