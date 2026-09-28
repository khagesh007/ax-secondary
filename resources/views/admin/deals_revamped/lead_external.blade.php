@extends('layouts.master-without-nav')
@section('title')
   Update Details Form
@endsection
@section('meta_description', 'Lead Generation Form')
@section('css')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />


    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/intl-tel-input/css/intlTelInput.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
        <style>
              .form-control {
            border: 1px solid #e19f7d87 !important;
        }

        .select2-selection {
            border: 1px solid #e19f7d87 !important;
        }
        </style>
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
                            @php

                            $leadtype = $lead_type?->meta_value ?? 'INDIVIDUAL';
                            @endphp
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-8 offset-3">
                                        @if($leadtype == 'INDIVIDUAL')
                                        <h3 class="ax-text">KYC FORM PROPAERTY PURCHASE (INDIVIDUAL)</h3>
                                        @else
                                        <h3 class="ax-text">KYC FORM PROPERTY PURCHASE - ENTITY</h3>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div id="editLeadForm" class="">

                                        <form id="mainForm">
                                            <div class="row mt-3">



                                                <input type="hidden" value="{{$invite->invite_code}}" name="invite_code">

                                                <div class="row @if($leadtype == 'INDIVIDUAL')d-none @endif">
                                                    <h4 class="companyForm @if($leadtype == 'INDIVIDUAL')d-none @endif">Company Details</h4>
                                                    <div class="row col-md-6 mt-3 companyForm ">
                                                        <div class="col-md-4">
                                                            <label for="name" class="form-label">Full Legal Name<span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                id="company_first_name"
                                                                 value="{{$meta['company_first_name'] ?? ''}}"
                                                                name="company_first_name"
                                                                placeholder="Company Name">
                                                        </div>
                                                    </div>


                                                    <div class="row col-md-6 mt-3 companyForm">
                                                        <div class="col-md-4">
                                                            <label for="company_trading_name" class="form-label">Company
                                                                Trading Name<span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                id="company_trading_name"
                                                                  value="{{$meta['company_trading_name'] ?? ''}}"
                                                                name="company_trading_name" value=""
                                                                placeholder="Company Trading Name">
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 companyForm ">
                                                        <div class="col-md-4">
                                                            <label for="company_trade_license_no" class="form-label">Company
                                                                Trade License No
                                                                {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                            </label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            {{-- rental-prop primary-sale secondary-sale --}}
                                                            <input type="text"
                                                                class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                                id="company_trade_license_no"
                                                                   value="{{$meta['company_trade_license_no'] ?? ''}}"
                                                                name="company_trade_license_no" value=""
                                                                placeholder="Company Trade License No">
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 companyForm ">
                                                        <div class="col-md-4">
                                                            <label for="company_coi" class="form-label">Company COI
                                                                {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                            </label>

                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                                id="company_coi" name="company_coi"
                                                                  value="{{$meta['company_coi'] ?? ''}}"
                                                                value="" placeholder="Company COI">
                                                                <p class="text-muted fs-10">Country of incorporation/reg. </p>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 companyForm ">
                                                        <div class="col-md-4">
                                                            <label for="company_coi_date" class="form-label">Company COI
                                                                Date
                                                                {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                            </label>

                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="date"
                                                                class="form-control inputs   reset-validations lead-disable-on-view-mode"
                                                                id="company_coi_date"
                                                                value="{{$meta['company_coi_date'] ?? ''}}"
                                                                name="company_coi_date" value=""
                                                                placeholder="Company COI Date">
                                                                <p class="text-muted fs-10">Date of incorporation/reg. </p>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 companyForm ">
                                                        <div class="col-md-4">
                                                            <label for="company_principal_address"
                                                                class="form-label">Registered Address<span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                id="company_principal_address"
                                                                name="company_principal_address"
                                                                 value="{{$meta['company_principal_address'] ?? ''}}"
                                                                placeholder="Company Principal Address">
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 companyForm ">
                                                        <div class="col-md-4">
                                                            <label for="trade_license_issue_date" class="form-label">Trade
                                                                License Issue Date
                                                                {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                            </label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="date"
                                                                class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                                id="trade_license_issue_date"
                                                                name="trade_license_issue_date"
                                                                  value="{{$meta['trade_license_issue_date'] ?? ''}}"
                                                                placeholder="Trade License Issue Date">
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 companyForm ">
                                                        <div class="col-md-4">
                                                            <label for="trade_license_expiry_date"
                                                                class="form-label">Trade License Expiry Date
                                                                {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                            </label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="date"
                                                                class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                                id="trade_license_expiry_date"
                                                                name="trade_license_expiry_date"
                                                                 value="{{$meta['trade_license_expiry_date'] ?? ''}}"
                                                                placeholder="Trade License Expiry Date">
                                                        </div>
                                                    </div>

                                                    <div class="row col-md-6 mt-3 companyForm">
                                                        <div class="col-md-4">
                                                            <label for="trade_license_issue_authority"
                                                                class="form-label">Trade license Issuing Authority
                                                                {{-- <span
                                                                class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span> --}}
                                                            </label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs  reset-validations lead-disable-on-view-mode"
                                                                id="trade_license_issue_authority"
                                                                name="trade_license_issue_authority"
                                                                value="{{$meta['trade_license_issue_authority'] ?? ''}}"
                                                                placeholder="Trade License Issuing Authority">
                                                        </div>
                                                    </div>

                                                </div>

                                                {{-- Individual Fields --}}
                                                <div class="row mt-4">
                                                    <hr>
                                                    <div class="col-md-6">
                                                        @if($leadtype == 'INDIVIDUAL')
                                                        <h4 class="individualForm">Personal Information</h4>
                                                        @else
                                                        <h4 class="companyForm">Representative Person's Information</h4>
                                                        @endif
                                                    </div>
                                                    {{-- Close Lead Form Button  --}}
                                                    <div class="col-md-6 mr-4">

                                                    </div>
                                                    {{-- Close Lead Form Button  --}}
                                                    <div class="row col-md-6 mt-3 individualForm companyForm    ">
                                                        <div class="col-md-4">
                                                            <label for="name" class="form-label">
                                                                @if($leadtype == 'INDIVIDUAL')
                                                                First Name
                                                                @else
                                                                Full Name
                                                                @endif *
                                                                <span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                                <br><small class="text-muted">As per passport</small>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations  lead-disable-on-view-mode"
                                                                id="first_name" name="first_name" required
                                                                value="{{ $lead->first_name }}"
                                                                placeholder="Enter First Name">
                                                        </div>
                                                    </div>

                                                    <div class="row col-md-6 mt-3 individualForm ">
                                                        @if($leadtype == 'INDIVIDUAL')
                                                        <div class="col-md-4">
                                                            <label for="name" class="form-label">Last Name *<span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                                <br>
                                                                <small class="text-muted">As per passport</small>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                id="last_name" name="last_name"
                                                                value="{{ $lead->last_name }}"
                                                                placeholder="Enter Last Name">
                                                        </div>
                                                        @endif
                                                    </div>


                                                    <div class="row col-md-6 mt-3 individualForm companyForm">
                                                        <div class="col-md-4">
                                                            <label for="address" class="form-label">Residential Address *
                                                                <span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="address" id="address" required
                                                                placeholder="Enter Residential Address"
                                                                value="{{ $lead->address }}" autocomplete="off"
                                                                data-intl-tel-input-id="0">
                                                            <div class="text-danger addressValidation">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 individualForm">
                                                        <div class="col-md-4">
                                                            <label for="dob" class="form-label">Date of Birth *<span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="date"
                                                                class="form-control inputs rental-prop   primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="dob" id="dob" required
                                                                placeholder="Enter DOB" autocomplete="off"
                                                                value="{{ $lead->dob }}" data-intl-tel-input-id="0">
                                                            <div class="text-danger dobValidation">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="gender" class="form-label">Gender *<span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                                        </div>
                                                        <div class="col-md-8">
                                                            <select
                                                                class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs  lead-disable-on-view-mode"
                                                                id="gender" name="gender" required>

                                                                <option value="Male">Male</option>
                                                                <option value="Female">Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="nationality_id" class="form-label">Nationality *
                                                                <span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                                        </div>
                                                        <div class="col-md-8">
                                                            <select
                                                                class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                                                                id="lead-nationality_id" name="nationality_id"
                                                                required>
                                                                <option value="" disabled>Select Nationality</option>
                                                                @foreach ($countries ?? [] as $country)
                                                                    <option
                                                                    @if(isset($lead->nationality_id) &&$lead->nationality_id == $country->id) selected @endif
                                                                    value="{{ $country->id }}">
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
                                                                name="mobile" id="mobile" required
                                                                placeholder="Enter Mobile" autocomplete="off"
                                                                value="{{ $lead->mobile }}" readonly
                                                                data-intl-tel-input-id="0">
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
                                                                name="whatsapp" id="lead-whatsapp"
                                                                value="{{ $lead->whatsapp }}" required
                                                                placeholder="Enter Whatsapp" autocomplete="off"
                                                                data-intl-tel-input-id="0">
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
                                                                name="phone" id="lead-phone"
                                                                value="{{ $lead->phone }}" required
                                                                placeholder="Enter Phone" autocomplete="off"
                                                                data-intl-tel-input-id="0">
                                                            <div class="text-danger phoneValidation">
                                                            </div>
                                                            <small>example:+971585858176</small>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="email" class="form-label">Email <span
                                                                    class="required">*</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="email"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="email" value="{{ $lead->email }}"
                                                                id="lead-email" readonly placeholder="Enter Email"
                                                                autocomplete="off" data-intl-tel-input-id="0">
                                                            <div class="text-danger emailValidation">
                                                            </div>
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
                                                                name="aliases" id="aliases"
                                                                value="{{$meta['aliases'] ?? ''}}"
                                                                placeholder="Enter Aliases" autocomplete="off"
                                                                data-intl-tel-input-id="0">
                                                            <div class="text-danger aliasesValidation">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="place_of_birth" class="form-label">Place of Birth
                                                                <span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                        </div>
                                                        <div class="col-md-8">

                                                            <select
                                                                class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                                                                id="place_of_birth"
                                                                name="place_of_birth" required>

                                                                @foreach ($countries ?? [] as $country)
                                                                    <option value="{{ $country->name }}"
                                                                        @if(isset($meta['place_of_birth']) && $meta['place_of_birth'] == $country->name) selected @endif>
                                                                        {{ $country->name }}</option>


                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="passport_issue_country"
                                                                class="form-label">Passport Issuing Country <span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select
                                                                class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                                                                id="passport_issue_country"

                                                                name="passport_issue_country" required>
                                                                @foreach ($countries ?? [] as $country)
                                                                    <option
                                                                    @if(isset($meta['passport_issue_country']) && $meta['passport_issue_country'] == $country->id) selected @endif
                                                                    {{$meta['passport_issue_country'] ?? ''}} value="{{ $country->id }}">
                                                                        {{ $country->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="passport_number" class="form-label">Passport
                                                                Number <span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="passport_number"
                                                                id="passport_number" required
                                                                 value="{{$meta['passport_number'] ?? ''}}"
                                                                placeholder="Enter Passport" autocomplete="off"
                                                                data-intl-tel-input-id="0">
                                                            <div class="text-danger passportValidation">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="passport_issue_date" class="form-label">Passport
                                                                Issue Date<span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="date"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="passport_issue_date"
                                                                 value="{{$meta['passport_issue_date'] ?? ''}}"
                                                                id="passport_issue_date" required
                                                                placeholder="Enter Passport Issue Date" autocomplete="off"
                                                                data-intl-tel-input-id="0">
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
                                                            name="passport_expiry" id="passport_expiry" required
                                                            placeholder="Enter Passport Expiry" autocomplete="off" data-intl-tel-input-id="0">
                                                        <div class="text-danger passport_expiryValidation">
                                                        </div>
                                                    </div>
                                                </div> --}}

                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="passport_expiry" class="form-label">Passport
                                                                Expiry Date<span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="date"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="passport_expiry"
                                                                    value="{{$meta['passport_expiry'] ?? ''}}"
                                                                id="passport_expiry" required
                                                                placeholder="Enter Passport Issue Date" autocomplete="off"
                                                                data-intl-tel-input-id="0">
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
                                                            @php
                                                                $fund_source = $meta['fund_source'] ?? '';

                                                                $array = ['Employment Income', 'Sale of Investment or Properties', 'Business Income', 'Savings', 'Other'];
                                                                $is_fund_source = in_array($fund_source, $array) ? 1 : 0;
                                                                $is_other_selected = $fund_source != 1 ? 'selected' : '';

                                                            @endphp
                                                            <select class="form-control select2" id="fund_source_select" name="fund_source_select" required>
                                                                <option></option>
                                                                @foreach ($array as $item)

                                                                    <option @if($fund_source == $item) selected @endif
                                                                    @if($item == 'Other') {{$is_other_selected}} @endif
                                                                    value="{{ $item }}">{{ $item }}</option>

                                                                @endforeach

                                                            </select>

                                                            <input type="text"
                                                                class="form-control
                                                                 @if($is_fund_source) d-none  @endif
                                                                mt-1 inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="fund_source" id="fund_source"
                                                                 value="{{$fund_source ?? ''}}"
                                                                placeholder="Enter Fund Source" autocomplete="off">

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
                                                                name="occupation" id="occupation"
                                                                  value="{{$meta['occupation'] ?? ''}}"
                                                                required placeholder="Enter Occupation" autocomplete="off"
                                                                data-intl-tel-input-id="0">
                                                            <div class="text-danger occupationValidation"> </div>

                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="employer_name" class="form-label">Employer /
                                                                Establishment Name<span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="employer_name" id="employer_name"
                                                                  value="{{$meta['employer_name'] ?? ''}}"
                                                                required placeholder="Enter Employer Name"
                                                                autocomplete="off" data-intl-tel-input-id="0">
                                                            <div class="text-danger annual_incomeValidation">
                                                            </div>


                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="annual_income" class="form-label">Annual Income
                                                                <span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="number"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="annual_income" id="annual_income"
                                                                 value="{{$meta['annual_income'] ?? ''}}"
                                                                required placeholder="Enter Annual Income"
                                                                autocomplete="off" data-intl-tel-input-id="0">
                                                            <div class="text-danger annual_incomeValidation">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="years_in_role" class="form-label">Years In Present
                                                                Role <span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="number"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="years_in_role" id="years_in_role"
                                                                 value="{{$meta['years_in_role'] ?? ''}}"
                                                                required placeholder="Enter Years In Role"
                                                                autocomplete="off" data-intl-tel-input-id="0">
                                                            <div class="text-danger years_in_roleValidation">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="establishment_address" class="form-label">Employer
                                                                / Establishment
                                                                Address<span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                                name="establishment_address"
                                                                  value="{{$meta['establishment_address'] ?? ''}}"
                                                                id="establishment_address" required
                                                                placeholder="Enter Establishment Address"
                                                                autocomplete="off" data-intl-tel-input-id="0">
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
                                                            id="payment_method" name="payment_method" required>

                                                            <option value="Cash">Cash</option>
                                                            <option value="Mortgage">Mortgage</option>

                                                        </select>
                                                    </div>
                                                </div> --}}
                                                    <div class="row col-md-6 mt-3 individualForm  companyForm">
                                                        <div class="col-md-4">
                                                            <label for="politically_exposed_person"
                                                                class="form-label">Current/former
                                                                Politically Exposed Person (PEP)? <span
                                                                    class="label-rental-prop label-primary-sale label-secondary-sale  label-reset-validations "></span></label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select
                                                                class="form-control select2  rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode"
                                                                id="politically_exposed_person"
                                                                name="politically_exposed_person" required>
                                                                <option value="YES">Yes</option>
                                                                <option value="No">No</option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row col-md-6 mt-3 d-none">
                                                        <input type="hidden" name="is_signature" id="is_signature" value="0">
                                                        <label for="politically_exposed_person"
                                                                class="form-label">Sign Here <button type="button"
                                                                onclick="clearSignature()"
                                                                class="btn btn-light btn-sm float-end" id="clear">Clear</button></label>
                                                        <div id="signatureCanvas"
                                                        style="border:1px solid #e19f7d87; border-radius: .25rem;"
                                                        ></div>
                                                        <span>* Please match your signature with your passport singnature.</span>
                                                    </div>
                                                </div>



                                                {{-- Secondary Leads Data --}}
                                                <div class="row" id="secondary_leads">

                                                </div>



                                                <div class="row col-md-12 mt-3 mb-3">
                                                    <div class="col-md-12 ">
                                                        <button class="btn btn-primary lead-disable-on-view-mode"
                                                            type="submit" id="updateLeadBtn">Submit</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>




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
                                            <h4>Details Updated Successfully.</h4>

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

    <script src="https://cdn.jsdelivr.net/npm/lemonadejs/dist/lemonade.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@lemonadejs/signature/dist/index.min.js"></script>

<script>
    // setDateMaxToday('lead-dob');
    // setDateMaxToday('passport_issue_date');


    // lead-mobile
    function showToastify(
    text = "",
    type = "success",
    duration = 3000,
    gravity = "bottom"
) {
    let style = {};
    if (type == "error") {
        style = {
            background: "linear-gradient(to right, #df2808, #df5c45)",
        };
    }
    Toastify({
        text,
        close: true,
        duration,
        gravity,
        style,
    }).showToast();
}

    const component = Signature(document.getElementById("signatureCanvas"), {
    width: 500,
    height: 200,
    value: [],
    onchange: (o) => {

        //  console.log(JSON.stringify(o.value));
         if(o.value.length > 0){
            $("#is_signature").val('1');
         }else{
            $("#is_signature").val('0');
         }
    }
});

function clearSignature() {
    component.value = [];  // Reset the signature value to an empty array
   // component.refresh();   // Refresh the component to apply changes
   $("#is_signature").val('0');
}



    $(document).ready(function() {
        $("#fund_source_select").select2({
            placeholder: "Select Fund Source",
            allowClear: true,
        });


        $("#fund_source_select").change(function() {
            let value = $(this).val();
            if (value == 'Other') {
                $("#fund_source").removeClass('d-none');
               // $("#fund_source").prop('disabled', false);
            } else {
                $("#fund_source").addClass('d-none');
                //$("#fund_source").prop('disabled', true);
            }
        });
    });






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



</script>
    <script>
        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })
        $(document).ready(function() {
            $("#lead-nationality_id").select2({
                                            placeholder: "Select Nationality",
                                            allowClear: true,
                                        });
            $("#passport_issue_country").select2({
                                            placeholder: "Select Issue Country",
                                            allowClear: true,
                                        });
            $("#place_of_birth").select2({
                                            placeholder: "Country of birth",
                                            allowClear: true,
                                        });


        });
        $(document).on('submit', '#mainForm', function(event) {
            $("#updateLeadBtn").prop('disabled', true);
            event.preventDefault();

            // function save() {
            var formData = new FormData($("#mainForm")[0]);
            let signature = component.getImage();
            formData.append('signature', signature);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                url: "{{ route('collab.deals.update.lead.external.user') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    $('#screen_loader').addClass('d-none');
                    if (result.status == 'success') {
                        showToastify(result.message, "success")
                        $("#updateLeadBtn").prop('disabled', true);
                        $('#leadDetails').addClass('d-none');
                        $('#successMessage').removeClass('d-none');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);

                    }else{
                        showToastify(result.message, "error")
                        $("#updateLeadBtn").prop('disabled', false);
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
