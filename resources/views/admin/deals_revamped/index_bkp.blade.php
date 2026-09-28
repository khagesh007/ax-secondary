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
@endsection
@section('content')
    <form class="row needs-validation" id="mainForm" novalidate>
        <div class="col-lg-12">
            <div class="card">

            </div>
        </div>

    </form>

    <div class="row">
        <div class="col-xxl-12">
            <div class="card" id="contactList">
                <div class="card-body">



                    <!-- SmartWizard html -->
                    <div id="smartwizard" dir="" class="sw sw-theme-arrows sw-justified">
                        <ul class="nav nav-progress">
                            <li class="nav-item">
                                <a class="nav-link default active" href="#step-1">
                                    <div class="num">1</div>
                                    Property Information
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link default" href="#step-2">
                                    <span class="num">2</span>
                                    Contact Information
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link default" href="#step-3">
                                    <span class="num">3</span>
                                    Lead Information
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

                        <div class="tab-content" style="height: auto !important;">
                            <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1"
                                style="display: block;">
                                <form id="form-1" class="row row-cols-1 ms-5 me-5 needs-validation dealPropertyForm" novalidate="">
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
                                <form id="form-3" class="row row-cols-1 ms-5 me-5 needs-validation dealLeadForm" novalidate="" enctype="multipart/form-data">
                                    @include('admin.deals_revamped.partials._lead')
                                </form>
                            </div>
                            <div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4"
                                style="display: none;">
                                <form id="form-4" class="row row-cols-1 ms-5 me-5 needs-validation" novalidate="" enctype="multipart/form-data">
                                    <input type="hidden" id="attachments-deal_id" name="deal_id" required>
                                    @include('admin.deals_revamped.partials._attachments')
                                </form>
                            </div>
                            <div id="step-5" class="tab-pane" role="tabpanel" aria-labelledby="step-5"
                                style="display: none;">

                                <form id="form-5" class="row row-cols-1 ms-5 me-5 dealCommissionForm" novalidate="">
                                    @include('admin.deals_revamped.partials._financial')
                                </form>



                            </div>
                        </div>
                        <div class="sw-toolbar-elm toolbar toolbar-bottom" role="toolbar"><button
                                class="btn sw-btn-prev sw-btn disabled" type="button">Previous</button><button
                                class="btn sw-btn-next sw-btn" type="button">Next</button>
                            <button class="btn btn-danger" id="btnCancel" onclick="onCancel()">Cancel</button>
                        </div>

                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    {{-- Smart Wizard html  --}}
                </div>
            </div>
        </div>
    </div>



    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Order Placed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Congratulations! Your order is placed.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="closeModal()">Ok, close and reset</button>
                </div>
            </div>
        </div>
    </div>
    @include('admin.deals_revamped.partials._deal_notes')
    @yield('modals')
    @stack('modals')
@endsection

@section('script')
    <!-- JavaScript -->
    <script src="{{ asset('dist/js/jquery.smartWizard.min.js') }}"></script>
    <script src="{{ asset('smart-wizard/js/demo.js') }}"></script>
    <script src="{{ asset('assets/libs/cleave.js/cleave.js.min.js') }}"></script>

    <script type="text/javascript">
        //here the code
        var deal_id = @if(isset($_GET['deal_id'])) "{{$_GET['deal_id']}}" @else  null @endif;
        var lead_id = null;
        var agent_id = null;
        var property_id = null;
        var contact_id = null;
        const deal = {};

        let currentStep = 1;

        // check if request has deal_id
        if (window.location.search.includes('deal_id')) {
            deal_id = window.location.search.split('deal_id=')[1];
            getDeal(deal_id);

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


        function onConfirm() {
            let form = document.getElementById('form-5');
            if (form) {
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    $('#smartwizard').smartWizard("setState", [4], 'error');
                    $("#smartwizard").smartWizard('fixHeight');
                    return false;
                }

                $('#smartwizard').smartWizard("unsetState", [3], 'error');
                myModal.show();
            }
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
        }


        $(function() {
            // Leave step event is used for validating the forms
            $("#smartwizard").on("leaveStep", function(e, anchorObject, currentStepIdx, nextStepIdx,
                stepDirection) {
                // Validate only on forward movement
                if (stepDirection == 'forward') {
                    let form = document.getElementById('form-' + (currentStepIdx + 1));
                    if (form) {
                        if (!form.checkValidity()) {
                            form.classList.add('was-validated');
                            $('#smartwizard').smartWizard("setState", [currentStepIdx], 'error');
                            $("#smartwizard").smartWizard('fixHeight');
                            return false;
                        }else{
                            console.log("coming in this else");
                            console.log("#form-" + (currentStepIdx + 1));
                            $("#form-" + (currentStepIdx + 1)).submit();
                        }
                        $('#smartwizard').smartWizard("unsetState", [currentStepIdx], 'error');
                    }
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
                if(currentStep == 4){
                    setupDealAttchments();
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
                    extraHtml: `<button class="btn btn-success" id="btnFinish" disabled onclick="onConfirm()">Complete Deal</button>
                          <button class="btn btn-danger" id="btnCancel" onclick="onCancel()">Cancel</button>`
                },
                anchor: {
                    enableNavigation: true, // Enable/Disable anchor navigation
                    enableNavigationAlways: false, // Activates all anchors clickable always
                    enableDoneState: true, // Add done state on visited steps
                    markPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                    unDoneOnBackNavigation: true, // While navigate back, done state will be cleared
                    enableDoneStateNavigation: true // Enable/Disable the done state navigation
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

        function getDeal(id) {
            console.log("AJAX deal request");
            $.ajax({
                url: "{{ route('admin.work.deals.get') }}",
                type: "get",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    if(response.status == '1') {
                        deal.value = response.data;
                        agent_id = response.data.agent_id;
                        property_id = response.data.property_id;
                        contact_id = response.data.contact_id;
                        lead_id = response.data.lead_id;
                        $('#agent_id').val(response.data.agent_id);
                        $('#agent_id').trigger('change');
                        // wait for 3 seconds
                        setTimeout(() => {
                            $('#property_id').val(response.data.property_id);
                            $('#property_id').trigger('change');
                        }, 1000);
                        getContactInformation(contact_id);
                        getLeads();
                        // console.log("deal: ", deal);
                        // var watchDeal = new DealWatcher();
                        // watchDeal.updateDealData(deal)
                    }else{

                    }


                },
                error: function() {
                    console.log("AJAX request was a failure");
                }
            });
        }
        checkvalidations('rental-prop')
        function checkvalidations(className) {
            $('.reset-validations').attr('required', false);
            $('.' + className).attr('required', true);
            $('.label-reset-validations').html('');
            $('.label-' + className).html('*');
        }

        function setupDealAttchments(){
            $("#attachments-deal_id").val(deal_id);

            if(contact_id != null && contact_id != undefined && contact_id != ""){
                getOwner(contact_id);
            }

            if(deal_id != null && deal_id != undefined && deal_id != ""){
                getDealLeads(deal_id);
                getDealMedia(deal_id);
            }
        }

        function setupDealNotes(){
            if(deal_id != null && deal_id != undefined && deal_id != ""){
                getDealNotes(deal_id, currentStep);
            }
        }

    </script>
    @stack('page-js')
@endsection
