@extends('admin.layouts.master')
@section('title')
    Deals
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!-- CSS -->

    <link href="{{ asset('dist/css/smart_wizard_all.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('smart-wizard/css/demo.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .btn-ax-primary-outline {
            border-color: #c48667;
            color: #c48667;
        }

        .btn-outline-secondary {
            border-color: #c48667 !important;
            color: #c48667 !important;
        }

        .btn-check:active+.btn-outline-secondary,
        .btn-check:checked+.btn-outline-secondary,
        .btn-outline-secondary.active,
        .btn-outline-secondary.dropdown-toggle.show,
        .btn-outline-secondary:active {
            background-color: #c48667 !important;
            border-color: #c48667 !important;
            color: #fff !important;
        }

        .btn-outline-secondary:hover {
            background-color: #c48667 !important;
            border-color: #c48667 !important;
            color: #fff !important;
        }

        .floating-button {
            position: fixed;
            right: 15px;
            /* Adjust the right distance as needed */
            bottom: 50vh;
            /* Adjust the bottom distance as needed */
            transform: translateX(50%);
            /* Center the button horizontally */
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 25px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .modal-content {
            border: 5px solid #eba888a1 !important;
            border-radius: 5px !important;
        }

        #cover-spin {
            position: fixed;
            width: 100%;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: none;
        }

        @-webkit-keyframes spin {
            from {
                -webkit-transform: rotate(0deg);
            }

            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #cover-spin::after {
            content: '';
            display: block;
            position: absolute;
            left: 48%;
            top: 40%;
            width: 40px;
            height: 40px;
            border-style: solid;
            border-color: black;
            border-top-color: transparent;
            border-width: 4px;
            border-radius: 50%;
            -webkit-animation: spin .8s linear infinite;
            animation: spin .8s linear infinite;
        }

        .table-border-custom {
            border: 1px solid #e2aa8d8c !important;
        }

        .tableThDark {
            background: #e19f7d21 !important;
        }

        .form-control:disabled,
        .form-control[readonly] {}

        .form-control {
            border: 1px solid #e19f7d87 !important;
        }

        .select2-selection {
            border: 1px solid #e19f7d87 !important;
        }

        .tdInput .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            background: white;
        }

        .tdInput span.select2-selection {
            border: 0px !important;
        }


        #financialTable1 td {
            vertical-align: middle !important;
        }

        #financialTable1 td.title {
            padding: 0px 0px 0px 9px;
        }

        .deal-input-group-text {
            position: absolute;
            align-items: center;
            color: var(--vz-body-color);
            display: flex;
            font-size: .8125rem;
            font-weight: 400;
            line-height: 1.5;
            padding: .5rem .9rem;
            text-align: center;
            white-space: nowrap;
        }

        .deal-form-control {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-clip: padding-box;
            background-color: var(--vz-input-bg);
            border: 0px solid var(--vz-input-border);
            border-radius: .25rem;
            color: var(--vz-body-color);
            display: block;
            font-size: .8125rem;
            font-weight: 400;
            line-height: 1.5;
            padding: .5rem .9rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            width: 100%;
        }

        .deal-form-control:focus {
            outline: none;
            /* Removes the default focus outline */
            box-shadow: none;
            /* Removes the box-shadow */
            border-color: transparent;
            /* Makes the border transparent */
        }

        .deal-input-border {
            border: 1px solid #e4e1df57 !important;
        }

        .table-spacing {
            margin-left: 3px;
        }

        .blur-tr {
            background: #dad3d352 !important;
            color: #8080808a !important;
        }

        .tdAdjust {
            padding: 0px 0px 0px 5px !important;
            vertical-align: middle;
        }

        .height-45 {
            height: 45px;
        }
    </style>
@endsection
@section('content')

    @if (!empty($deal))
        @if ($deal->status == 'APPROVED' && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning alert-dismissible alert-label-icon rounded-label shadow fade show"
                        role="alert">
                        <i class="ri-airplay-line label-icon"></i><strong>Hi {{ auth()?->user()?->name }}</strong> - This
                        deal is <b>Approved</b> and is sent to account department. you can mark this deal <b>Completed</b>.
                        Please confirm first with account department before marking this deal completed.

                        <a href="javascript:void(0)" onclick="showAdminCompleteModal('{{ $deal->id }}')"
                            class="btn rounded-pill btn-sm btn-info waves-effect waves-light">Mark Completed</a>
                    </div>
                </div>
            </div>
            @include('admin.deals_revamped.partials.modal.dealComplete')
        @endif
        @if (!empty($admin_deal_approval))
            @if (
                $deal->status == 'PENDING_APPROVAL' &&
                    $admin_deal_approval->approval_status == 'Pending' &&
                    (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')))
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning alert-dismissible alert-label-icon rounded-label shadow fade show"
                            role="alert">
                            <i class="ri-airplay-line label-icon"></i><strong>Hi {{ auth()?->user()?->name }}</strong> -
                            This deal is pending approval from <b>Admin</b> please check the details and take action.
                            <a href="javascript:void(0)" onclick="showAdminApprovalStatusModal('{{ $deal->id }}')"
                                class="btn rounded-pill btn-sm btn-info waves-effect waves-light">Approve</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#adminRejectModal"
                                class="btn rounded-pill btn-sm btn-danger waves-effect waves-light">Reject</a>


                        </div>
                    </div>
                </div>
                @include('admin.deals_revamped.partials.modal.adminApprovalModal')

                @include('admin.deals_revamped.partials.modal.adminRejectModal')
            @endif
            <?php
            /* //This was used when there was multiple users approval needed
@if($deal->status == 'Rejected' &&  (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')))
    <div class="row" >
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible alert-label-icon rounded-label shadow fade show"
                 role="alert">
                <i class="ri-airplay-line label-icon"></i><strong>Hi {{ auth()?->user()?->name }}</strong> - This deal is pending approval from some users. You can check the details
                <a href="javascript:void(0)"  onclick="showAdminApprovalStatusModal('{{$deal->id}}')" class="btn rounded-pill btn-sm btn-info waves-effect waves-light">Here</a>
                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#adminRejectModal" class="btn rounded-pill btn-sm btn-danger waves-effect waves-light">Reject</a>


            </div>
        </div>
    </div>
    @include('admin.deals_revamped.partials.modal.adminApprovalModal')
@endif
*/
            ?>
        @endif
        @if (!empty($deal_approval))
            @if ($deal_approval->approval_status == 'Pending')
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning alert-dismissible alert-label-icon rounded-label shadow fade show"
                            role="alert">
                            <i class="ri-airplay-line label-icon"></i><strong>Hi {{ auth()?->user()?->name }}</strong> -
                            This deal is pending approval please check the details and take action.
                            <a href="javascript:void(0)"
                                onclick="showApprovalModal('{{ $deal->id }}','{{ $deal_approval->id }}')"
                                class="btn rounded-pill btn-sm btn-info waves-effect waves-light">Approve</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#adminRejectModal"
                                class="btn rounded-pill btn-sm btn-danger waves-effect waves-light">Reject</a>


                        </div>
                    </div>
                </div>
                @include('admin.deals_revamped.partials.modal.approvalModal')
            @endif
        @endif
    @endif

    <form class="row needs-validation" id="mainForm" novalidate>
        <div class="col-lg-12">
            <div class="card">
                <div id="cover-spin"></div>
            </div>
        </div>

    </form>

    <div class="modal fade" id="agentInviteModal" tabindex="-1" aria-labelledby="agentInviteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered overflow-auto">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agentInviteModalLabel">Invite Agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="modalClose()"></button>
                </div>


                <div class="modal-body p-4 ">
                    <div class="col-md-12 agent-selector mb-3">
                        <div class="row ">
                            <div class="col-md-12">
                                <select name="agent_to_invite" id="agent_to_invite" class="form-control" required>
                                    <option value=""></option>
                                    @foreach ($agents_for_invite as $agant)
                                        <option value="{{ $agant->id }}">{{ $agant->full_name }}</option>
                                    @endforeach
                                </select>

                                <div class="invalid-feedback">
                                    Please Select an Agent
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card d-none" id="agentInviteDetails">



                        <div class="card-body text-center mt-4 ">
                            <div class="position-relative d-inline-block">
                                <a data-lightbox="user-avatar_4" href="#">

                                    <img id="show-agent-image" src="" alt=""
                                        class="avatar-10rem rounded-circle" data-lightbox="#">
                                </a>
                                <span class="contact-active position-absolute rounded-circle bg-success"><span
                                        class="visually-hidden"></span>
                                </span>
                            </div>
                            <h5 class="mt-4 mb-1"><span id="show-agent-name"> </span></h5>

                            {{-- <ul class="list-inline mb-0">
                                <li class="list-inline-item avatar-xs">
                                    <a href="tel:+971521718769"
                                        class="avatar-title bg-soft-success text-success fs-15 rounded">
                                        <i class="ri-phone-line"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item avatar-xs">
                                    <a href="mailto:primary@axcapital.ae"
                                        class="avatar-title bg-soft-danger text-danger fs-15 rounded">
                                        <i class="ri-mail-line"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item avatar-xs">
                                    <a href="https://wa.me/+971521718769"
                                        class="avatar-title bg-soft-warning text-warning fs-15 rounded" target="_blank">
                                        <i class="ri-whatsapp-line"></i>
                                    </a>
                                </li>
                            </ul> --}}
                        </div>
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase fw-semibold mb-3">Personal Information</h6>

                            <div class="table-responsive table-card">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium" scope="row">Email ID</td>
                                            <td><a href="mailto:primary@axcapital.ae" id="show-agent-email"></a></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium" scope="row">Mobile No</td>
                                            <td><a href="tel:+971521718769" id="show-agent-mobile"></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="text-center mb-3">
                            <button class="btn btn-primary  text-white" type="button"
                                id="inviteAgentFromDetailsPage">Invite Agent
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-12">
            <div class="" id="contactList">
                <div class="">



                    <!-- SmartWizard html -->
                    <div id="smartwizard" dir="" class="sw sw-theme-arrows sw-justified">
                        <ul class="nav nav-progress">
                            <li class="nav-item">
                                <a class="nav-link default active" href="#step-1">
                                    <div class="num">1</div>
                                    Property Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link default" href="#step-2">
                                    <span class="num">2</span>
                                    <span class="nav-deal_contact_type"> Contact</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link default" href="#step-3">
                                    <span class="num">3</span>
                                    <span class="nav-deal_lead_type"> Lead </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link default" href="#step-4">
                                    <span class="num">4</span>
                                    Attachments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link default" href="#step-5">
                                    <span class="num">5</span>
                                    Finance
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content card mt-3" style="min-height: 430px;
height: auto !important;">
                            <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1"
                                style="display: block;">
                                <form id="form-1" class="row row-cols-1 ms-5 me-5 needs-validation dealPropertyForm"
                                    novalidate="">
                                    @include('admin.deals_revamped.partials._property')
                                </form>
                            </div>
                            <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2"
                                style="display: none;">
                                <form id="form-2" class="row row-cols-1 ms-5 me-5 needs-validation dealContactForm"
                                    novalidate="">
                                    @include('admin.deals_revamped.partials._contact')
                                </form>
                            </div>
                            <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3"
                                style="display: none;">
                                <form id="form-3" class="row row-cols-1 ms-5 me-5 needs-validation dealLeadForm"
                                    novalidate="" enctype="multipart/form-data">
                                    @include('admin.deals_revamped.partials._lead')
                                </form>
                            </div>
                            <div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4"
                                style="display: none;">
                                <form id="form-4" class="row row-cols-1 ms-5 me-5 needs-validation" novalidate=""
                                    enctype="multipart/form-data">
                                    @include('admin.deals_revamped.partials._attachments')
                                </form>
                            </div>
                            <div id="step-5" class="tab-pane" role="tabpanel" aria-labelledby="step-5"
                                style="display: none;">

                                <form id="form-5" class="row row-cols-1 needs-validation dealCommissionForm"
                                    novalidate="">
                                    @include('admin.deals_revamped.partials._financial')
                                    {{-- @include('admin.deals_revamped.partials._financial_jun24') --}}
                                </form>

                            </div>
                        </div>
                        {{-- <div class="sw-toolbar-elm toolbar toolbar-bottom" role="toolbar"><button
                                class="btn sw-btn-prev sw-btn disabled" type="button">Previous</button><button
                                class="btn sw-btn-next sw-btn" type="button">Next</button>
                            <button class="btn btn-danger" id="btnCancel" onclick="onCancel()">Cancel</button>
                        </div> --}}

                        <span class="fw-light p-1">Status: <span class="fw-bold" id="deal-status-text">Draft</span>
                            <span id="deal-status-message" style="font-style: italic;"></span>
                        </span>
                        {{-- <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 20%" aria-valuenow="20"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div> --}}
                    </div>

                    {{-- Smart Wizard html  --}}
                </div>
            </div>
        </div>
    </div>


    <button class="btn btn-info btn-sm floating-button" onclick="getDealActivity()" type="button"
        data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i
            class=" ri-message-3-line"></i> </button>


    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Deal Final Action!!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">

                            <!-- put a animated success.json animated image file here -->
                            <div class="alert alert-warning" role="alert">
                                Are you sure you want to submit this deal for approval from admin. Once submitted you will
                                not be able to edit this deal until admin approves it. You can edit the deal only if admin
                                rejects it.
                            </div>


                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="closeModal()">Close</button>
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close"
                                onclick="updateDealStatus('PENDING_APPROVAL')">Submit For Approval</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="confirmModalLabel">Some data is missing!!



                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">

                            <!-- put a animated success.json animated image file here -->
                            <div class="" role="alert" id="errorList">

                            </div>


                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                                aria-label="Close">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.deals_revamped.partials._deal_notes')
    @yield('modals')
    @stack('modals')
    @include('admin.deals_revamped.partials.financial.splitModal')
    @include('admin.deals_revamped.partials.financial.deductibleModal')
    @include('admin.deals_revamped.partials.financial.splitCommissionEdit')
    @include('admin.deals_revamped.partials.financial.editSingleUserCommission')
    @include('admin.deals_revamped.partials.widgets.timeline')

@endsection

@section('script')
    <!-- JavaScript -->
    <script src="{{ asset('dist/js/jquery.smartWizard.min.js') }}"></script>
    <script src="{{ asset('smart-wizard/js/demo.js') }}"></script>
    <script src="{{ asset('assets/libs/cleave.js/cleave.js.min.js') }}"></script>
    <script src="{{ asset('assets/libs/intl-tel-input/js/intlTelInput.min.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script type="text/javascript">
        //init agent_to_invite select2

        var lead_document_notes_fetched = [];

        $(document).ready(function() {
            $('#agent_to_invite').select2({
                placeholder: "Select Agent",
                allowClear: true,
                dropdownParent: $('#agentInviteModal')
            });
            $(".sw-btn").addClass('action-edit');
        });



        //here the code
        var deal_id =
            @if (isset($_GET['deal_id']))
                "{{ $_GET['deal_id'] }}"
            @else
                null
            @endif ;
        var lead_id = null;
        var agent_id = null;
        var is_external_agent = false;
        var property_id = null;
        var contact_id = null;
        var isValidSubmission = false;
        var nextStep = 0;
        const dealLeads = [];
        const deal = {
            value: {
                status: 'DRAFT'
            }

        };
        const dealSalesProgression = {};
        let deal_convencer_id =
            @if (!empty($deal_sales_progression))
                "{{ $deal_sales_progression?->conveyancer_id }}"
            @else
                null
            @endif ;
        const natianlity_list = @json($countries);
        let currentStep = 1;

        let stepNames = ["Property Information", "Contact Information", "Lead Information", "Attachments", "Finance"];

        // check if request has deal_id
        if (window.location.search.includes('deal_id')) {
            deal_id = window.location.search.split('deal_id=')[1];
            getDeal(deal_id);

        }
        const handleDealStatus = (deal_value) => {
            if (deal_value.status == 'PENDING_APPROVAL') {
                $("#btnCancel").addClass('d-none');
                @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))
                    $(".edit-action").removeClass('d-none');
                @else
                    $(".edit-action").remove();
                @endif
                $("#btnFinish").addClass('d-none');
            } else if (deal_value.status == 'COMPLETED' || deal_value.status == 'APPROVED') {
                $(".edit-action").addClass('d-none');
                $("#btnFinish").addClass('d-none');
                $("#btnCancel").addClass('d-none');
            } else {

            }
        }
        const myModal = new bootstrap.Modal(document.getElementById('confirmModal'));

        function onCancel() {
            // Reset wizard
            $('#smartwizard').smartWizard("reset");
            // Reset form
            document.getElementById("form-1").reset();
            document.getElementById("form-2").reset();
            document.getElementById("form-3").reset();
            document.getElementById("form-4").reset();
            document.getElementById("form-5").reset();
        }

        $(document).on('click', '.closeErrorModal', function() {
            setTimeout(() => {
                $('#errorModal').modal('hide');
            }, 500);

        });

        function onConfirm() {

            $("#confirmBtnLabel").addClass('d-none');
            $("#confirmBtnLoader").removeClass('d-none');

            $.ajax({
                url: "{{ route('admin.work.deals.data.check') }}",
                type: "get",
                data: {
                    deal_id: deal_id
                },
                success: function(result) {
                    $("#errorList").html('');
                    if (result.status === 'error') {
                        let stepMap = {
                            'Property': 1,
                            'Seller': 2,
                            'Owner': 2,
                            'Lead': 3,
                            'Buyer': 3,
                            'Tenant': 3,
                            'Attachments': 4,
                            'Finance': 5
                        }

                        $.each(result.message, function(key, valueObj) {
                            // console.log(key + "/" + valueObj );

                            $("#errorList").append('<p class="fw-semibold">' + key +
                                `<a href="#step-${stepMap[key]}" class="closeErrorModal"><i class="ri-eye-fill"></i></a> </p>`
                            );
                            let itemHtml = '';
                            $.each(valueObj, function(key, value) {
                                itemHtml += `<p class="">${value}</p>`;
                            });
                            $("#errorList").append(itemHtml);


                        });
                        $('#errorModal').modal('show');
                        for (let i = 0; i < result.message.length; i++) {
                            //showToastify(result.message[i],'error')

                            let module = result.message[i];
                            // if(module == 'lead'){
                            //
                            // }
                            for (j = 0; j < module.length; j++) {
                                console.log(j)
                                console.log(module[j])

                            }
                        }
                        let pending_module = result.pending_module;
                        if (pending_module.length > 0) {
                            if (pending_module.includes('lead')) {
                                $('#smartwizard').smartWizard("setState", [2], 'error');
                            }
                            if (pending_module.includes('Finance')) {
                                $('#smartwizard').smartWizard("setState", [4], 'error');
                            }
                            if (pending_module.includes('attachments')) {
                                $('#smartwizard').smartWizard("setState", [3], 'error');
                            }
                        }


                        $("#confirmBtnLabel").removeClass('d-none');
                        $("#confirmBtnLoader").addClass('d-none');

                    } else {
                        console.log('no error');
                        let form = document.getElementById('form-5');
                        if (form) {
                            if (!form.checkValidity()) {
                                form.classList.add('was-validated');
                                $('#smartwizard').smartWizard("setState", [4], 'error');
                                $("#smartwizard").smartWizard('fixHeight');
                                return false;
                            }

                            //   $('#smartwizard').smartWizard("unsetState", [3], 'error');
                            myModal.show();
                        }
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });


            // if(lead_step == false){
            //      $('#smartwizard').smartWizard("setState", [2], 'error');
            //      return false;
            //  }


        }

        function closeModal() {
            // Reset wizard
            $('#smartwizard').smartWizard("reset");

            // Reset form
            document.getElementById("form-1").reset();
            document.getElementById("form-2").reset();
            document.getElementById("form-3").reset();
            document.getElementById("form-4").reset();
            document.getElementById("form-5").reset();

            myModal.hide();
            let indexRoute = "{{ route('admin.work.deals.index') }}";
            //redirect to index page

        }


        $(function() {

            // Leave step event is used for validating the forms
            $("#smartwizard").on("leaveStep", function(e, anchorObject, currentStepIdx, nextStepIdx,
                stepDirection) {
                console.log("leaveStep", currentStepIdx, nextStepIdx, stepDirection, isValidSubmission);

                nextStep = nextStepIdx;
                // Validate only on forward movement
                if (stepDirection == 'forward' && isValidSubmission == false) {
                    let form = document.getElementById('form-' + (currentStepIdx + 1));
                    //get form data
                    let form_data = new FormData(form);

                    // for (var pair of form_data.entries()) {
                    //     //this will log the form inputs to the console
                    // //    console.log(pair[0] + ', ' + pair[1]);
                    // }

                    if (form) {
                        if (!form.checkValidity()) {
                            console.log('form not valid');
                            form.classList.add('was-validated');
                            if (currentStepIdx == 0) {
                                if (property_mode == 'edit') {
                                    $('#smartwizard').smartWizard("setState", [currentStepIdx], 'error');
                                }
                            } else {
                                $('#smartwizard').smartWizard("setState", [currentStepIdx], 'error');
                            }
                            $("#smartwizard").smartWizard('fixHeight');
                            //  $("#form-" + (currentStepIdx + 1)).submit();
                            if (currentStepIdx == 1) {
                                if (contact_mode == 'edit') {
                                    $("#form-" + (currentStepIdx + 1)).submit();
                                }
                            }
                            if (currentStepIdx == 2) {
                                $("#form-" + (currentStepIdx + 1)).submit();
                            }
                            if (currentStepIdx == 3) {
                                if (attachmentsMode == 'edit') {
                                    $("#form-" + (currentStepIdx + 1)).submit();
                                }
                            }
                            return true;
                        } else {
                            console.log('form is valid');
                            $("#form-" + (currentStepIdx + 1)).submit();

                            // Get the current width value
                            var currentWidth = $(".progress-bar").attr("aria-valuenow");

                            var updatedWidth = parseFloat(currentWidth) + 20;

                            // Set the updated width to the .progress-bar element
                            $(".progress-bar").css("width", updatedWidth + "%");
                        }
                        $('#smartwizard').smartWizard("unsetState", [currentStepIdx], 'error');
                        return false;
                    }


                }
                        if (nextStepIdx == 4) {
                            getDeal(deal_id);
                            }

            });

            // Step show event
            $("#smartwizard").on("showStep", function(e, anchorObject, stepIndex, stepDirection, stepPosition) {
                $("#prev-btn").removeClass('disabled').prop('disabled', false);
                $("#next-btn").removeClass('disabled').prop('disabled', false);
                if (stepPosition === 'first') {
                    $("#prev-btn").addClass('disabled').prop('disabled', true);
                } else if (stepPosition === 'last') {
                    $("#next-btn").addClass('disabled').prop('disabled', true);
                } else {
                    $("#prev-btn").removeClass('disabled').prop('disabled', false);
                    $("#next-btn").removeClass('disabled').prop('disabled', false);
                }

                // Get step info from Smart Wizard
                let stepInfo = $('#smartwizard').smartWizard("getStepInfo");
                if (deal_id) {
                    setTimeout(() => {
                        $("#deal-notes-header-title").text(
                            `Deal #${deal_id} ${stepNames[stepInfo.currentStep]} Notes`)
                    }, 1000);
                }
                $("#sw-current-step").text(stepInfo.currentStep + 1);
                $("#sw-total-step").text(stepInfo.totalSteps);

                if (stepPosition == 'last') {
                    $("#btnFinish").prop('disabled', false);
                } else {
                    $("#btnFinish").prop('disabled', true);
                }

                // Focus first name
                if (stepIndex == 1) {
                    setTimeout(() => {
                        $('#first-name').focus();
                    }, 0);
                }
                currentStep = stepIndex + 1;

                setupDealNotes();
                if (currentStep == 3) {
                    isValidSubmission = false;
                    setTimeout(() => {
                        getLeads();
                        if (deal.value.id != null && deal.value.id != undefined && deal.value.id !=
                            "") {
                            getDealLeadds(deal.value.id);
                        }
                    }, 1000);
                } else if (currentStep == 4) {
                    setTimeout(() => {
                        isValidSubmission = false;
                        getDeal(deal_id);
                        setupDealAttchments();
                    }, 1000);
                } else if (currentStep == 5) {
                    // getDealCommissions(deal.value.id);
                }

            });

            // Smart Wizard
            $('#smartwizard').smartWizard({
                selected: 5,
                autoAdjustHeight: false,
                theme: 'arrows', // basic, arrows, square, round, dots
                transition: {
                    animation: 'fade'
                },
                toolbar: {
                    showNextButton: true, // show/hide a Next button
                    showPreviousButton: true, // show/hide a Previous button
                    position: 'bottom', // none/ top/ both bottom
                    extraHtml: `<button class="btn action-edit ${deal.value.status == 'Completed' ? 'd-none' : ''} " id="btnFinish" disabled onclick="onConfirm()"><span id="confirmBtnLabel">Submit Deal</span>
<span id="confirmBtnLoader" class="d-flex align-items-center d-none">
            <span class="flex-grow-1 me-2">
                Loading...
            </span>
            <span class="spinner-grow flex-shrink-0" role="status">
                <span class="visually-hidden">Loading...</span>
            </span>
        </span>

</button>



                          <button class="btn action-edit" id="btnCancel" onclick="onCancel()">Cancel</button>`
                },
                anchor: {
                    enableNavigation: true, // Enable/Disable anchor navigation
                    enableNavigationAlways: true, // Activates all anchors clickable always
                    enableKeyNavigation: false,
                    enableDoneState: true, // Add done state on visited steps
                    markPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                    unDoneOnBackNavigation: true, // While navigate back, done state will be cleared
                    enableDoneStateNavigation: true // Enable/Disable the done state navigation
                },
                keyboard: {
                    keyNavigation: false, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
                },
            });

            $("#state_selector").on("change", function() {
                $('#smartwizard').smartWizard("setState", [$('#step_to_style').val()], $(this).val(), !$(
                    '#is_reset').prop("checked"));
                return true;
            });

            $("#style_selector").on("change", function() {
                $('#smartwizard').smartWizard("setStyle", [$('#step_to_style').val()], $(this).val(), !$(
                    '#is_reset').prop("checked"));
                return true;
            });

        });

        // var DealWatcher = function(){
        //     this.dispatcher = $({});
        // }

        // dealWatcher.prototype = {
        //     deal: null,
        //     updateDealData: function(deal){
        //         this.deal = deal;
        //         this.dispatcher.trigger("dealDataUpdated");
        //     }
        // }
        const handleDealTypeActions = (deal_value) => {
            if (deal_value.type == 'Rental') {
                $(".salesprogression").addClass('d-none');
                $(".salesProgressionBlur").addClass('bulr-tr');

            } else {
                $(".salesprogression").removeClass('d-none');
            }
        }

        function setLeadTypeDataAndLabels(deal_value) {
            $(".label-deal_type").html(deal_value.type);
            if (deal_value.type == 'Rental') {
                $(".nav-deal_lead_type").html("Tenant");
                $(".nav-deal_contact_type").html("Landlord");
            } else {
                $(".nav-deal_contact_type").html("Seller");
                $(".nav-deal_lead_type").html("Buyer");
            }
        }

        function toggleDealFinancialEditDeleteButtons(hide = true) {
            if (hide) {
                $("#deal-dropdown-btn-group").addClass("d-none");
                $(".remove-commission-row-button").addClass("d-none");
                $(".delete-deductible-button").addClass("d-none");
                $(".edit-deductible-button").addClass("d-none");
                $(".remove-split-button").addClass("d-none");
                $(".edit-split-button").addClass("d-none");
                $(".delete-deal-commission").addClass("d-none");
                $(".edit-commission-button").addClass("d-none");
                $(".remove-split-commission").addClass("d-none");
                $(".edit-commission-button").addClass("d-none");
                $("#finanical-commission_saveBtn").addClass("d-none");
            } else {
                $("#deal-dropdown-btn-group").removeClass("d-none");
                $(".remove-commission-row-button").removeClass("d-none");
                $(".delete-deductible-button").removeClass("d-none");
                $(".edit-deductible-button").removeClass("d-none");
                $(".remove-split-button").removeClass("d-none");
                $(".edit-split-button").removeClass("d-none");
                $(".delete-deal-commission").removeClass("d-none");
                $(".edit-commission-button").removeClass("d-none");
                $(".remove-split-commission").removeClass("d-none");
                $(".edit-commission-button").removeClass("d-none");
                $("#finanical-commission_saveBtn").removeClass("d-none");
            }
        }

        function attachmentsFieldsDisabled(disabled) {
            $(".attachments-disable-on-view-mode").prop('disabled', disabled);
            if (disabled) {
                $('#btn-edit-attachments-mode').html('<i class="mdi mdi-pencil"></i> Edit');
                $(".attachments-hide-on-view-mode").addClass("d-none");
            } else {
                $(".attachments-hide-on-view-mode").removeClass("d-none");
                $('#btn-edit-attachments-mode').html('<i class="mdi mdi-eye"></i> View');
            }
        }

        function isNullOrEmpty(value) {

            return value === "" || value === null || value === undefined;
        }

        const getDealPropertyForExternalAgent = (deal_id) => {

            $.ajax({
                url: "{{ route('admin.deals_revamped.listing.deatils') }}",
                type: "get",
                data: {
                    deal_id: deal_id
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 1) {
                        let item = response.data;
                        let options =
                            `<option
                    data-price="${item.price?item.price.price: ''}"
                    data-completion_status="${item.completion_status?item.completion_status: ''}"
                    data-built_up_area="${item.built_up_area?item.built_up_area: ''}"
                    data-property_category="${item.property_category?item.property_category.title: ''}"
                    data-property_type="${item.for?item.for: ''}" data-baths="${item.bath?item.bath.name: ''}"
                    data-beds="${item.bed?item.bed.name: ''}" data-for="${item.for?item.for: ''}"
                    data-first_image="${item.first_image && item.first_image.path ? item.first_image.path : ''}"
                    data-ref_no="${item.ref_no}" data-unit_no="${item.unit_no}" data-publish_status="${item.publish_status || ''}"
                    data-publish_status="${item.publish_status}" data-location="${item.location}"
                    value="${item.id}">${item.ref_no} - ${item.for || ''} - ${item.unit_no || ''} - ${item.location}</option>`;
                        $('#property_id').html(options);

                        $('#property_id').val(item.id);
                        $('#property_id').trigger('change');
                    };

                    // $('#property-transaction_type').val(property.progression.transaction_type).trigger('change');
                    // $('#property-sales_progression').val(property.progression.sales_progression).trigger('change');

                },
                error: function() {
                    console.log("AJAX request was a failure");
                }
            });
        }

        function getDeal(id) {
            //console.log("AJAX deal request");
            $.ajax({
                url: "{{ route('admin.work.deals.get') }}",
                type: "get",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {

                    let deal_status = response?.data?.status ?? "DRAFT";
                    //remove _ with space and capitalize first letter
                    deal_status = deal_status.replace(/_/g, ' ').replace(/\w\S*/g, function(txt) {
                        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                    });
                    $('#deal-status-text').text(deal_status);
                    $('#deal-status-message').text(response?.data?.status_message ?? "");
                    if (response.status == '1') {
                        deal.value = response.data;
                        if (deal.value.external_agent_id != null) {
                            //  $("#btn-edit-property-mode").remove();
                        }
                        let document_notes = deal.value.document_notes;
                        let json_document_notes = JSON.parse(document_notes ?? "{}");
                        $("#contact_docuemnt_note").val(json_document_notes?.contact_docuemnt_note ?? "");
                        $("#other_docuemnt_note").val(json_document_notes?.other_docuemnt_note ?? "");
                        lead_document_notes_fetched = json_document_notes?.lead_document_notes ?? [];

                        setLeadTypeDataAndLabels(deal.value);
                        handleDealTypeActions(deal.value);
                        setStepName(deal.value);
                        agent_id = response.data.agent_id;
                        property_id = response.data.property_id;
                        contact_id = response.data.contact_id;
                        lead_id = response.data.lead_id;
                        if (deal.value.status == "PENDING_APPROVAL" || deal.value.status == "COMPLETED" || deal
                            .value.status == "APPROVED") {
                            $("#btnFinish").addClass('d-none');
                            $("#btnCancel").addClass('d-none');
                            $(".edit-action").remove();
                            $("#lead-selector").remove();
                            $(".editAttachedLead, .deleteAttachedLead").remove();
                            $(".hideBeyondEditable").remove();
                        }
                        if (deal.value.status == "REJECTED") {
                            $("#btnFinish").text('Resubmit Deal');
                        }
                        if (deal.value.external_agent_id == "{{ auth()->user()->id }}") {
                            $(".externalAgentHide").addClass('d-none');

                        }

                        if (deal.value.agent != null) {
                            $('#agent').text(deal.value.agent.name);
                        }

                        let property_built_up_area = response.data.deal_meta.find(x => x.meta_key ==
                            'property_bua')?.meta_value ?? 0;
                        let property_listing_price = response.data.deal_meta.find(x => x.meta_key ==
                            'listing_price')?.meta_value ?? 0;
                        $(".listing_price").html(numberFormat(property_listing_price));
                        let property_sqft_price = 0;
                        if (property_listing_price > 0) {
                            $(".property_listing_price").text(numberFormat(property_listing_price));
                            $("#listing_price").val(property_listing_price);


                        }

                        if (property_built_up_area > 0 && property_listing_price > 0) {
                            property_sqft_price = property_listing_price / property_built_up_area;
                            property_sqft_price = property_sqft_price != Infinity ? property_sqft_price : 0;
                            $(".price_sqft").text(numberFormat(property_sqft_price));
                        } else {
                            $(".price_sqft").text(0);
                        }
                        // For View And Edit Mode (Property)
                        response.data.deal_meta.forEach(element => {
                            if (element.meta_key == "property_mode") {
                                property_mode = element.meta_value;
                                $("#btn-edit-property-mode").removeClass('d-none');
                                $('#btn-edit-property-mode').html(
                                    '<i class="mdi mdi-pencil"></i> Edit');
                                $('#agent_id').prop('disabled', true);
                                $('#property_id').prop('disabled', true);
                                $('#property-transaction_type').prop('disabled', true);
                                $('#property-sales_progression').prop('disabled', true);
                            } else {
                                $("." + element.meta_key).text(element.meta_value);
                            }


                        });
                        // For View And Edit Mode (Property)

                        $('#agent_id').val(response.data.agent_id);
                        $('#agent_id').trigger('change');
                        $("deal_type").val(response.data.type);
                        // wait for 3 seconds
                        setTimeout(() => {
                            $('#property_id').val(response.data.property_id);
                            $('#property_id').trigger('change');
                            $('#property-transaction_type').val(response.data?.progression
                                ?.transaction_type).trigger('change');
                        }, 1000);
                        getContactInformation(contact_id);
                        //check if getLeads function is defined
                        if (typeof getLeads === "function") {
                            getLeads();
                        }

                        //Disable edit/delete buttons
                        toggleDealFinancialEditDeleteButtons(deal.value.status == "PENDING");
                        // if (deal.value.id != null && deal.value.id != undefined && deal.value.id !=
                        //     "") {
                        //     getDealLeadds(deal.value.id);
                        // }
                        // console.log("deal: ", deal);
                        // var watchDeal = new DealWatcher();
                        // watchDeal.updateDealData(deal)
                        response.data.deal_meta?.map(meta => {
                            if (meta.meta_key == "attachments_mode") {
                                attachmentsMode = "view";
                                attachmentsFieldsDisabled(true)
                            } else {
                                attachmentsFieldsDisabled(false)
                            }
                        })
                        if (deal.value.external_agent_id == globalUser.id) {
                            is_external_agent = true;
                            $("#btn-edit-contact-mode").remove();
                            $("#contactDocumentSec").remove();
                        }
                        if (deal.value.external_agent_id != null) {
                            setTimeout(() => {
                                getDealPropertyForExternalAgent(deal_id);
                                $("#PropertySelection").removeClass('d-none');
                                $('#PropertySelection').removeAttr('style');
                            }, 1000);


                        }
                        handleDealStatus(deal.value);
                    } else {

                    }


                },
                error: function() {
                    console.log("AJAX request was a failure");
                }
            });
        }


        function checkvalidations(className) {
            console.log(className);
            $('.reset-validations').attr('required', false);
            $('.' + className).attr('required', true);
            $('.label-reset-validations').html('');
            $('.label-' + className).html('*');
        }

        const updateDealStatus = (status) => {
            $('#cover-spin').show(0)
            let data = {
                deal_id: deal.value.id,
                status: status
            }
            axios.post(`/api/vue/v1/update-deal-status`, data).then((response) => {
                if (response.data.status == 1) {
                    $('#cover-spin').hide(0)
                    // refreshFinancialData();
                    showToastify('Deal status updated successfully', 'success')
                    setTimeout(() => {
                        location.reload();
                    }, 1000);

                } else {
                    $('#cover-spin').hide(0)
                    showToastify('Something went wrong', 'error')
                }
            });
        }

        function setupDealAttchments() {
            if (contact_id != null && contact_id != undefined && contact_id != "") {
                getOwner(contact_id);
            }

            if (deal_id != null && deal_id != undefined && deal_id != "") {
                getDealLeads(deal_id);
                getDealMedia(deal_id);
            }
        }

        function setupDealNotes() {
            if (deal_id != null && deal_id != undefined && deal_id != "") {
                getDealNotes(deal_id, currentStep);
            }
        }

        function setStepName(deal_value) {
            if (deal_value.type == 'Rental') {
                stepNames[1] = "Landlord Information";
                stepNames[2] = "Tenants Information";
            } else {
                stepNames[1] = "Seller Information";
                stepNames[2] = "Buyers Information";
            }
        }

        function setDateMaxToday(inputId) {
            let today = new Date().toISOString().split('T')[0];
            $(`#${inputId}`).attr('max', today);
        }


        $("#sidebarDeals").addClass('active');
        $("#dealSidebarBlock").removeClass('collapsed');
        $("#dealSidebarBlock").addClass('active');
    </script>
    @stack('page-js')
@endsection
