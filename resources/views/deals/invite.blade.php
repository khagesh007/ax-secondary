@extends('layouts.master-without-nav')
@section('title')
    Deals Information
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
    <link href="{{ URL::asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet">

@endsection

@section('content')
    @include('admin.partials.loader')

    <div class="auth-page-wrapper pt-2">
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

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-2">
                            <img src="{{ asset('assets/images/logo/ax-short-logo.png') }}" alt="">
                        </div>

                    </div>
                </div>
                <!-- end row -->

                <div class="row  justify-content-center formDiv">
                    <div class="col-md-12">
                        <div class="card mt-4">

                            <form action="{{ route('collab.deals.storeInformation') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="text" value="{{ $invite_code }}" name="invite_code">
                                <div class="card-body p-4">
                                    <div class="col-xxl-12">
                                        <h3 class="mb-3 text-center">Lead Details</h3>

                                        <div class="" id="leadDetails">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h5>Owner Details</h5>
                                                </div>

                                                <div class="col-md-12 mt-3">
                                                    <label for="lead_id" class="form-label">Lead<span
                                                            class="required">*</span></label>
                                                    <select class="form-control select2 inputs" id="lead_id"
                                                        name="lead_id">
                                                        <option></option>
                                                        @foreach ($leads ?? [] as $lead)
                                                            <option value="{{ $lead->id }}"
                                                                data-first_name="{{ $lead->first_name }}"
                                                                data-last_name="{{ $lead->last_name }}"
                                                                data-email="{{ $lead->email }}"
                                                                data-source_id="{{ $lead->source_id }}"
                                                                data-lead_medium_idx="{{ $lead->lead_medium_idx }}"
                                                                data-mobile="{{ $lead->mobile }}">
                                                                {{ $lead->first_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-12 text-center mt-3">
                                                    <label for="">OR</label>
                                                </div>

                                                <div class="col-md-12 text-center">
                                                    <div class="btn-group" role="group"
                                                        aria-label="Basic radio toggle button group">
                                                        <input type="radio" class="changeForm btn-check" name="btnradio"
                                                            id="btnradio1" autocomplete="off" checked=""
                                                            value="individual">
                                                        <label class="btn btn-sm btn-outline-secondary"
                                                            for="btnradio1">Individual</label>

                                                        <input type="radio" class="changeForm btn-check" name="btnradio"
                                                            id="btnradio2" autocomplete="off" value="company">
                                                        <label class="btn btn-sm btn-outline-secondary"
                                                            for="btnradio2">Company</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="formAppend">
                                            </div>
                                            <div class="row mt-5">
                                                <div class="col-12">
                                                    <h5>Related Document</h5>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="dropzone">
                                                    <div class="fallback">
                                                        <input name="file" type="file" multiple="multiple">
                                                    </div>
                                                    <div class="dz-message needsclick">
                                                        <div class="mb-3">
                                                            <i class="display-4 text-muted ri-upload-cloud-2-fill"></i>
                                                        </div>

                                                        <h4>Drop files here or click to upload.</h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="description" class="form-label">Passport</label>
                                                    <input type="file" class="form-control" name="passport">
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="description" class="form-label">Visa</label>
                                                    <input type="file" class="form-control" name="visa">
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="description" class="form-label">Emirates
                                                        ID</label>
                                                    <input type="file" class="form-control" name="emirates_id">
                                                </div>
                                            </div>
                                            @if ($entity_type == 'COMPANY')
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <label for="description" class="form-label">Trade
                                                            License</label>
                                                        <input type="file" class="form-control" name="trade_license">
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="description" class="form-label">Note <span
                                                            class="required">*</span></label>
                                                    <textarea class="form-control inputs" required id="formSizeDefault" name="message" placeholder="Note for the Lead"
                                                        rows="4"></textarea>
                                                    <div class="invalid-feedback">
                                                        Please write Note for the Lead.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="p-2 mt-1">
                                        <div class="row text-center" id="duplicationMessage">
                                            <div class="col-md-12">
                                                <span class="text-danger" id="showDuplicationMessage"></span>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">Submit Form</button>
                                        </div>


                                    </div>
                                </div>
                            </form>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->



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
                <div class="row mt-3">
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




    <div id="" class="d-none">
        <div class="row mt-5" id="individualForm">
            <div class="col-md-6 mt-3">
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="name" class="form-label">First name
                            <span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs" id="first_name" name="first_name"
                            value="" required placeholder="John">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="name" class="form-label">Last
                            name</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs" id="last_name" name="last_name"
                            value="" placeholder="Doe">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="aliases" class="form-label">Aliases
                            <span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="aliases" id="aliases" required
                            placeholder="Enter aliases" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger aliasesValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="address" class="form-label">Residential
                            Address<span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="address" id="address" required
                            placeholder="Enter address" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger addressValidation">
                        </div>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="data_of_birth" class="form-label">Data
                            of birth
                            <span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="date" class="form-control inputs " name="data_of_birth" id="data_of_birth"
                            required placeholder="Enter data_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger data_of_birthValidation">
                        </div>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="place_of_birth" class="form-label">Place of birth
                            <span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="place_of_birth" id="place_of_birth"
                            required placeholder="Enter place_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger place_of_birthValidation">
                        </div>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="gender" class="form-label">Gender</label>

                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2 inputs" id="gender" name="gender" required>
                            <option></option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>

                        </select>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="nationality" class="form-label">Nationality</label>

                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2 inputs" id="nationality" name="nationality" required>
                            <option></option>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source->id }}">
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ------- --}}
            </div>
            <div class="col-md-6 mt-3">
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="mobile" class="form-label">Mobile
                            <span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="tel" class="form-control inputs checkDuplicate" name="mobile" id="mobile"
                            required placeholder="Enter mobile" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger mobileValidation">
                        </div>
                        <small>example:+971585858176</small>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="whatsapp" class="form-label">Whatsapp
                            <span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="tel" class="form-control inputs" name="whatsapp" id="whatsapp" required
                            placeholder="Enter whatsapp" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger mobileValidation">
                        </div>
                        <small>example:+971585858176</small>

                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="phone" class="form-label">Phone
                            <span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="tel" class="form-control inputs" name="phone" id="phone" required
                            placeholder="Enter phone" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger mobileValidation">
                        </div>
                        <small>example:+971585858176</small>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="email" class="form-label">Email
                            <span class="required">*</label>
                    </div>
                    <div class="col-md-9">
                        <input type="email" class="form-control inputs" name="email" id="email" required
                            placeholder="Enter email" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger emailValidation">
                        </div>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="passport" class="form-label">Passport
                            Number
                            <span class="required">*</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs" name="passport" id="passport" required
                            placeholder="Enter passport" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger passportValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="passport_issue_date" class="form-label">Passport Issue Date <span
                                class="required">*</label>
                    </div>
                    <div class="col-md-9">
                        <input type="date" class="form-control inputs" name="passport_issue_date"
                            id="passport_issue_date" required placeholder="Enter passport_issue_date" autocomplete="off"
                            data-intl-tel-input-id="0">
                        <div class="text-danger passport_issue_dateValidation">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="passport_expiry_date" class="form-label">Passport Expiry <span
                                class="required">*</label>
                    </div>
                    <div class="col-md-9">
                        <input type="date" class="form-control inputs" name="passport_expiry_date"
                            id="passport_expiry_date" required placeholder="Enter passport_expiry_date"
                            autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger passport_expiry_dateValidation">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="passport_issue_country" class="form-label">Passport issuing
                            country</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2 inputs" id="passport_issue_country"
                            name="passport_issue_country" required>
                            <option></option>
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
                    <div class="col-md-3">
                        <label for="source_id" class="form-label">Source</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2 inputs" id="source_id" name="source_id" required>
                            <option></option>
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
                    <div class="col-md-3">
                        <label for="lead_medium_id" class="form-label">Medium</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2 inputs" id="lead_medium_id" name="lead_medium_id" required>
                            <option></option>
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
                    <div class="col-md-3">
                        <label for="fund_source" class="form-label">Fund
                            Source</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2 inputs" id="fund_source" name="fund_source" required>
                            <option></option>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source->id }}">
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="occupation" class="form-label">Occupation</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2 inputs" id="occupation" name="occupation" required>
                            <option></option>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source->id }}">
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="employer_name" class="form-label">Employer / Establishment
                            Name</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2 inputs" id="employer_name" name="employer_name" required>
                            <option></option>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source->id }}">
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="annual_income" class="form-label">Annual Income</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2 inputs" id="annual_income" name="annual_income" required>
                            <option></option>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source->id }}">
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="politically_exposed_person" class="form-label">Are you current/former
                            Politically Exposed Person (PEP)? *</label>
                    </div>
                    <div class="col-md-6">
                        <select class="form-control select2 inputs" id="politically_exposed_person"
                            name="politically_exposed_person" required>
                            <option></option>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source->id }}">
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row mt-3">

                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="years_in_role" class="form-label">Years In Present
                            Role</label>
                    </div>
                    <div class="col-md-9">
                        <input type="number" class="form-control inputs" name="years_in_role" id="years_in_role"
                            required placeholder="Enter years_in_role" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger years_in_roleValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="establishment_address" class="form-label">Employer / Establishment
                            Address</label>
                    </div>
                    <div class="col-md-9">
                        <input type="number" class="form-control inputs" name="establishment_address"
                            id="establishment_address" required placeholder="Enter establishment_address"
                            autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger establishment_addressValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control select2 inputs" id="payment_method" name="payment_method" required>
                            <option></option>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source->id }}">
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="" class="d-none">
        <div class="row mt-5" id="companyForm">
            <div class="col-md-12 mt-3">
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="name" class="form-label">Full Legal Name
                            <span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs" id="first_name" name="first_name"
                            value="" required placeholder="John">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="name" class="form-label">Former Name (If any)</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs" id="last_name" name="last_name"
                            value="" placeholder="Doe">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="aliases" class="form-label">Trading Name (If any)
                            <span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="aliases" id="aliases" required
                            placeholder="Enter aliases" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger aliasesValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="address" class="form-label">Trade License Number<span
                                class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="address" id="address" required
                            placeholder="Enter address" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger addressValidation">
                        </div>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="data_of_birth" class="form-label">Country of incorporation/registration<span
                                class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="date" class="form-control inputs " name="data_of_birth" id="data_of_birth"
                            required placeholder="Enter data_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger data_of_birthValidation">
                        </div>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="place_of_birth" class="form-label">Date of incorporation/registration
                            <span class="required">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="place_of_birth" id="place_of_birth"
                            required placeholder="Enter place_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger place_of_birthValidation">
                        </div>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="gender" class="form-label">Registered Address</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="place_of_birth" id="place_of_birth"
                            required placeholder="Enter place_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger place_of_birthValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="gender" class="form-label">Address of Principal Place of Business (if different from Registered Address)</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="place_of_birth" id="place_of_birth"
                            required placeholder="Enter place_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger place_of_birthValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="gender" class="form-label">Telephone number</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="place_of_birth" id="place_of_birth"
                            required placeholder="Enter place_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger place_of_birthValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="gender" class="form-label">Email address</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="place_of_birth" id="place_of_birth"
                            required placeholder="Enter place_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger place_of_birthValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="gender" class="form-label">Trade license issue date</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="place_of_birth" id="place_of_birth"
                            required placeholder="Enter place_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger place_of_birthValidation">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="gender" class="form-label">Trade license expiry date</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="place_of_birth" id="place_of_birth"
                            required placeholder="Enter place_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger place_of_birthValidation">
                        </div>
                    </div>
                </div>


                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="gender" class="form-label">Trade license issuing authority</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control inputs " name="place_of_birth" id="place_of_birth"
                            required placeholder="Enter place_of_birth" autocomplete="off" data-intl-tel-input-id="0">
                        <div class="text-danger place_of_birthValidation">
                        </div>
                    </div>
                </div>





                {{-- ------- --}}
            </div>

        </div>
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
        $('#lead_id').select2({
            placeholder: 'Select Lead',
            allowClear: true
        });
        $('#source_id').select2({
            placeholder: 'Select Source',
            allowClear: true
        });
        $('#lead_medium_id').select2({
            placeholder: 'Select Medium',
            allowClear: true
        });
        $('#nationality').select2({
            placeholder: 'Select Nationality',
            allowClear: true
        });
        $('#passport_issue_country').select2({
            placeholder: 'Select Passport issuing country',
            allowClear: true
        });
        $('#gender').select2({
            placeholder: 'Select Gender',
            allowClear: true
        });

        $(document).ready(function() {
            $('#individualForm').appendTo('#formAppend');



        })

        $(document).on('change', '.changeForm', function() {
            let value = $(this).val();
            $('#formAppend').html('');
            if (value == 'individual') {
                $('#individualForm').appendTo('#formAppend');
            } else {
                $('#companyForm').appendTo('#formAppend');
            }
            alert(value)
        })



        $(document).on('change', '#lead_id', function() {
            let first_name = $(this).find(':selected').data('first_name');
            let last_name = $(this).find(':selected').data('last_name');
            let email = $(this).find(':selected').data('email');
            let mobile = $(this).find(':selected').data('mobile');
            let source_id = $(this).find(':selected').data('source_id');
            let lead_medium_id = $(this).find(':selected').data('lead_medium_id');
            $('#first_name').val(first_name);
            $('#last_name').val(last_name);
            $('#email').val(email);
            $('#mobile').val(mobile);
            $('#source_id').val(source_id).trigger('change');
            $('#lead_medium_id').val(lead_medium_id).trigger('change');
        })


        $(document).on('change', '#mobile', function() {
            // lead_id is empty
            if ($('#lead_id').val() == '') {
                let mobile = $(this).val();
                $.ajax({
                    url: "{{ route('ajax.checkDuplicateLead') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        mobile: mobile,
                        'agent_id': '837'
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#duplicationMessage').removeClass('d-none');
                            $('#showDuplicationMessage').html(response.message);
                            $('#saveBtn').attr('disabled', true);
                        } else {
                            $('#duplicationMessage').addClass('d-none');
                            $('#showDuplicationMessage').html('');
                            $('#saveBtn').attr('disabled', false);
                        }
                    }
                })
            }


        })
    </script>

@endsection
