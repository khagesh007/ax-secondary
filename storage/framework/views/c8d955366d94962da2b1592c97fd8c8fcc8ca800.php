<div class=" text-center d-none mt-3" id="invite_sent_success_alert">
    <div class="alert alert-warning alert-border-left alert-dismissible fade show" role="alert">
        <i class="ri-notification-line me-1 align-middle fs-16"></i><strong>You have invited <span id="alert_agent_name">
            </span>
            to fill the lead form !</strong>
        - Please wait for your colleague to complete the lead form
        
    </div>
</div>


<div class="text-end">
    <div id="btn-edit-lead-mode" class=" btn  btn-outline-secondary d-none  w-20 edit-action">
        <i class="mdi mdi-pencil"></i> Edit
    </div>
</div>

<div class="col mt-3" id="lead-selector">


    <div class="row">
        <?php if($isAlreadyInvited == false): ?>
            <div class="col-md-9 primary-lead-selector">
                <div class="row ">
                    <div class="col-md-12 ">
                        <label for="address" class="form-label">Primary <span
                                class="tenant-buyer-title">Buyer</span></label>
                    </div>
                    <div class="col-md-10">
                        <select name="lead_id" id="lead_id"
                            class="form-control inputs select2 lead-disable-on-view-mode">
                            <option value="">Select <span class="tenant-buyer-title">Buyer</span></option>
                        </select>
                        <div class="invalid-feedback">
                            Please select primary <span class="tenant-buyer-title">Buyer</span> or wait for your
                            colleague
                            to fill the form and attach lead
                        </div>
                    </div>
                </div>
            </div>


            <?php if(!$fromInvitePage): ?>
                <div class="col-md-3 mt-4 text-end">

                    <button class="btn btn-primary  btn-sm mt-2 d-none lead-disable-on-view-mode text-white"
                        id="addNewLeadButton" type="button">Add New
                        <span class="tenant-buyer-title">Buyer</span></button>
                    <?php /*
                    <button class="btn btn-secondary btn-sm mt-2 text-white lead-disable-on-view-mode"
                        id="inviteAgentBtn" type="button">Invite
                        Agent</button>

 */
                    ?>
                </div>
            <?php endif; ?>
        <?php elseif($isAlreadyInvited == true && $isDealInviteLeadCreated == true): ?>
            <div class="alert alert-success alert-border-left alert-dismissible fade show" role="alert">
                <i class="ri-notification-line me-1 align-middle fs-16"></i><strong>Your colleague has completed lead
                    form you can move to next step <span id="alert_agent_name"> </span> !</strong>
                - Thank you for your patience
                
            </div>
        <?php else: ?>
            <div class="alert alert-warning alert-border-left alert-dismissible fade show" role="alert">
                <i class="ri-notification-line me-1 align-middle fs-16"></i><strong>You have invited <span
                        id="alert_agent_name"> </span>
                    to fill the lead form !</strong>
                - Please wait for your colleague to complete the lead form
                
            </div>
        <?php endif; ?>

    </div>


</div>







<div class="col-md-12 mt-3 d-none" id="showSelectLead">

    <div class="card " id="">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <div class="mr-auto p-2">
                        <h5><span class="tenant-buyer-title">Buyer</span> Details</h5>
                    </div>
                </div>

            </div>
        </div>
        <div class="card-body">
            <div class="row mt-4">
                <div class="col-md-6">
                    <table class="table table-bordered">

                        <tbody>
                            <tr>
                                <td><b>First Name</b></td>
                                <td><span id="show-lead-first_name"></span></td>
                            </tr>
                            <tr>
                                <td><b>Email </b></td>
                                <td><span id="show-lead-email"></span></td>
                            </tr>
                            <tr>
                                <td><b>Source </b></td>
                                <td><span id="show-lead-source_id"></span></td>
                            </tr>


                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">

                        <tbody>
                            <tr>
                                <td><b>Last Name</b></td>
                                <td><span id="show-lead-last_name"></span></td>
                            </tr>
                            <tr>
                                <td><b>Mobile</b></td>
                                <td><span id="show-lead-mobile"></span></td>
                            </tr>
                            <tr>
                                <td><b>Medium</b></td>
                                <td><span id="show-lead-lead_medium_id"></span></td>
                            </tr>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 text-center">
            <div class="p-2">
                <button class="btn btn-primary btn-sm text-white" type="button" id="attachlead">Confirm
                    <span class="tenant-buyer-title">Buyer</span></button>
                <button class="btn btn-secondary btn-sm" type="button" id="cancelSelectedLead">Cancel
                </button>
            </div>
        </div>
    </div>


    <div class="row mt-4 d-none" id="">
        <div class="col-md-5">
            <table class="table table-bordered">

                <tbody>
                    <tr>
                        <td><b>First Name</b></td>
                        <td><span id="show-lead-first_name"></span></td>
                    </tr>
                    <tr>
                        <td><b>Email </b></td>
                        <td><span id="show-lead-email"></span></td>
                    </tr>

                </tbody>
            </table>
        </div>
        <div class="col-md-5">
            <table class="table table-bordered">

                <tbody>
                    <tr>
                        <td><b>Last Name</b></td>
                        <td><span id="show-lead-last_name"></span></td>
                    </tr>
                    <tr>
                        <td><b>Mobile</b></td>
                        <td><span id="show-lead-mobile"></span></td>
                    </tr>

                </tbody>
            </table>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm" type="button" id="attachlead">Attach <span
                    class="tenant-buyer-title">Buyer</span></button>
        </div>
    </div>
</div>
<div class="col-md-12 mb-2" id="attachedLeads">

</div>

<?php echo $__env->make('admin.deals_revamped.partials.lead._primary', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startPush('page-js'); ?>
    <script>
        let show_lead_id = null;
        let show_first_name = null;
        let show_last_name = null;
        let show_email = null;
        let show_mobile = null;
        var form_already_submited = false;
        var lead_step = false;
        // $('#lead_id').select2({
        //     placeholder: 'Select Lead',
        //     allowClear: true,
        // });

        $(document).ready(function() {

            setDateMaxToday('lead-dob');
            setDateMaxToday('metaData-passport_issue_date');
            setDateMaxToday('metaData-trade_license_issue_date');
        });
        // For View And Edit Mode (Lead)
        var lead_mode = "edit";

        $('#btn-edit-lead-mode').click(function() {
            if (lead_mode == "edit") {
                lead_mode = "view";
                $('#btn-edit-lead-mode').html('<i class="mdi mdi-pencil"></i> Edit');
                leadFieldsDisabled(true);
            } else {
                lead_mode = "edit";
                $('#btn-edit-lead-mode').html('<i class="mdi mdi-eye"></i> View');
                leadFieldsDisabled(false);
            }
        })

        function leadFieldsDisabled(disbaled) {

            $(".lead-disable-on-view-mode").prop('disabled', disbaled);



            if (lead_mode == "edit") {
                let leadType = $("input[name='metaData-leadtype']:checked").val();

                if (leadType == 'company') {

                    $('#leadtype1').prop('checked', false).trigger(
                        "change");
                    $('#leadtype2').prop('checked', true).trigger(
                        "change");

                    $('.individualForm').addClass('d-none');
                    $('.individualForm input').prop('disabled', true);
                    $('.individualForm select').prop('disabled', true);

                    $('.companyForm').removeClass('d-none');
                    $('.companyForm input').prop('disabled', false);
                    $('.individualForm select').prop('disabled', false);


                } else {
                    $('#leadtype1').prop('checked', true).trigger(
                        "change");
                    $('#leadtype2').prop('checked', false).trigger(
                        "change");

                    $('.companyForm').addClass('d-none');
                    $('.companyForm input').prop('disabled', true);
                    $('.companyForm select').prop('disabled', true);


                    $('.individualForm').removeClass('d-none');
                    $('.individualForm input').prop('disabled', false);
                    $('.individualForm select').prop('disabled', false);
                }

            }
            // $("#lead-mobile").prop("disbaled",true);
            // $("#lead-phone").prop("disbaled",true);
            // $("#lead-whatsapp").prop("disbaled",true);






        }

        function initLeadSelect2() {
            let placeholder = "Select Buyer";
            if (deal.value.type == "Rental") {
                placeholder = "Select Tenant";
            }
            $("#lead_id").select2({
                debug: true,
                placeholder: placeholder,
                allowClear: true,
                closeOnSelect: true,
                templateResult: formatLeadSelect2,
                templateSelection: formatLeadSelect2,
                ajax: {
                    url: route('ajax.leads.select2'),
                    dataType: 'json',
                    type: 'GET',
                    data: function(params) {
                        return {
                            search: params.term,
                            agent_id: agent_id,
                        }
                    },
                    processResults: function(result) {

                        var results = $.map(result.data, function(item) {
                            let text =
                                `${item.first_name ? item.first_name : ''} ${item.last_name ? item.last_name : ''} ${item.mobile ? ' - ' + item.mobile : ''}  ${item.email ? ' - ' + item.email : ''} ${item?.lead_source ? '('+item?.lead_source?.name+')' : ''}`;
                            return {
                                text: `${text}`,
                                id: item.id,
                                mobile: item.mobile,
                                email: item.email,
                                image: item.image,
                            };
                        });

                        return {
                            results: results
                        };

                    }
                }
            });
        }
        // For View And Edit Mode (Lead)
        <?php
        echo 'let countries = ' . json_encode($countries);
        ?>


        function getLeads() {
            let agent_id = $('#agent_id').val();
            initLeadSelect2();
            return '';

            $.ajax({
                url: "<?php echo e(route('ajax.leads.select2')); ?>",
                type: "post",
                data: {
                    agent_id: agent_id,
                    _token: "<?php echo e(csrf_token()); ?>"
                },
                dataType: "json",
                success: function(response) {
                    let title_text = "Buyer";
                    if (deal.value.type == "Rental") {
                        title_text = "Tenant";
                    }
                    let options = '';
                    options += '<option value="">Select ' + title_text + '</option>';
                    $.each(response.data, function(index, item) {
                        options +=
                            `<option value="${item.id}">${item.first_name ?  item.first_name : ''} ${item.last_name ?  item.last_name : ''} ${item.mobile ?  ' - '+item.mobile : ''}  ${item.email ?  ' - '+item.email : ''} (${item?.lead_source ? item?.lead_source?.name : ''})</option>`;
                    });
                    $('#lead_id').html(options);
                    // $('#lead_id').select2({
                    //     placeholder: 'Select ' + title_text,
                    //     allowClear: true,
                    //     required: true,
                    // });


                },
                error: function() {
                    console.log("AJAX request was a failure");
                }
            });
        }
        // $("#agent_to_invite").select2({
        //     placeholder: 'Select Agent',
        //     dropdownParent: $("#agentInviteModal")
        // });


        $(document).on('change', '#lead_id', function() {
            let lead_id = $(this).val();
            $('#lead_id').attr('required', true);
            showLead(lead_id);

            $('.individualForm input , .individualForm select').val('');
            $('.companyForm input , .companyForm select').val('');

        });




        function checkStepCompletionLead(data) {
            if (data.lead_meta == null || data.lead_meta == undefined || data.lead_meta.length == 0) {
                return false;
            }

            let type = (data.lead_meta.find(x => x.meta_key == 'type')?.meta_value) ?? 'INDIVIDUAL';
            let place_of_birth = (data.lead_meta.find(x => x.meta_key == 'place_of_birth')?.meta_value) ?? '';
            let passport_issue_country = (data.lead_meta.find(x => x.meta_key == 'passport_issue_country')?.meta_value) ??
                '';
            // let name_on_passport = (data.lead_meta.find(x => x.meta_key == 'name_on_passport')?.meta_value) ?? '';
            let passport_number = (data.lead_meta.find(x => x.meta_key == 'passport_number')?.meta_value) ?? '';
            let passport_issue_date = (data.lead_meta.find(x => x.meta_key == 'passport_issue_date')?.meta_value) ?? '';
            let passport_expiry = (data.lead_meta.find(x => x.meta_key == 'passport_expiry')?.meta_value) ?? '';
            let fund_source = (data.lead_meta.find(x => x.meta_key == 'fund_source')?.meta_value) ?? '';
            let occupation = (data.lead_meta.find(x => x.meta_key == 'occupation')?.meta_value) ?? '';
            let employer_name = (data.lead_meta.find(x => x.meta_key == 'employer_name')?.meta_value) ?? '';
            let annual_income = (data.lead_meta.find(x => x.meta_key == 'annual_income')?.meta_value) ?? '';
            let years_in_role = (data.lead_meta.find(x => x.meta_key == 'years_in_role')?.meta_value) ?? '';
            let establishment_address = (data.lead_meta.find(x => x.meta_key == 'establishment_address')?.meta_value) ?? '';
            let politically_exposed_person = (data.lead_meta.find(x => x.meta_key == 'politically_exposed_person')
                ?.meta_value) ?? '';


            lead_step = !(
                isNullOrEmpty(data.first_name) ||
                isNullOrEmpty(data.mobile) ||
                isNullOrEmpty(data.phone) ||
                isNullOrEmpty(data.whatsapp) ||
                isNullOrEmpty(data.email) ||
                isNullOrEmpty(data.gender) ||
                isNullOrEmpty(data.nationality_id) ||
                isNullOrEmpty(data.address) ||
                isNullOrEmpty(data.source_id) ||
                isNullOrEmpty(data.lead_medium_id) ||
                isNullOrEmpty(place_of_birth) ||
                isNullOrEmpty(passport_issue_country) ||
                // isNullOrEmpty(name_on_passport) ||
                isNullOrEmpty(passport_number) ||
                isNullOrEmpty(passport_issue_date) ||
                isNullOrEmpty(passport_expiry) ||
                isNullOrEmpty(fund_source) ||
                isNullOrEmpty(occupation) ||
                isNullOrEmpty(employer_name) ||
                isNullOrEmpty(annual_income) ||
                isNullOrEmpty(years_in_role) ||
                isNullOrEmpty(establishment_address) ||
                isNullOrEmpty(politically_exposed_person)
            );

            if (type == "INDIVIDUAL") {
                lead_step = !(
                    isNullOrEmpty(data.last_name) ||
                    isNullOrEmpty(data.dob)
                );
            }

            if (type == "COMPANY") {

                let company_first_name = (data.lead_meta.find(x => x.meta_key == 'company_first_name')?.meta_value) ?? '';
                let company_trading_name = (data.lead_meta.find(x => x.meta_key == 'company_trading_name')?.meta_value) ??
                    '';
                let company_principal_address = (data.lead_meta.find(x => x.meta_key == 'company_principal_address')
                    ?.meta_value) ?? '';

                lead_step = !(
                    isNullOrEmpty(company_first_name) ||
                    isNullOrEmpty(company_principal_address) ||
                    isNullOrEmpty(company_trading_name)
                );
            }

            //  console.log("LEAD STEP 2 STATUS: "+lead_step);
        }

        function getLead(id, fronEditLeadButton = false) {
            form_already_submited = false;

            $('#lead_id').val(id);
            $('#lead_id').attr('required', false);
            $.ajax({
                url: "<?php echo e(route('admin.work.deals.getLead.api')); ?>",
                type: "get",
                data: {
                    id: id
                },
                success: function(result) {
                    checkStepCompletionLead(result.data);
                    if (result.status == '1') {
                        Object.keys(result.data).map((index) => {
                            if (index == 'dob' && result.data[index] != null && result.data[index] !=
                                '') {
                                $(`#lead-${index}`).val(result.data[index]).trigger("change");
                            } else if (index == 'first_name') {
                                if (result.data[index] == "new" || result.data[index] == "New" || result
                                    .data[index] == "NEW") {
                                    $('#lead-first_name').val('').trigger("change");
                                } else {
                                    $(`input[name='lead-${index}']`).val(result.data[index]).trigger(
                                        "change")
                                }
                            } else {
                                $(`#lead-${index}`).val(result.data[index]).trigger("change")
                            }

                            if ((index === 'source_id' || index === 'lead_medium_id') && result.data[
                                    index] > 0) {
                                $(`select[name='lead-${index}']`).attr('disabled', true);
                                try {
                                    let source_id_temp = $(`select[name='lead-${index}']`);
                                    let selectedOption = source_id_temp.find("option:selected");
                                    let selectedOptionText = selectedOption.text().trim();
                                    if (selectedOptionText == "Unspecified") {
                                        source_id_temp.val('').trigger("change");
                                        $(`select[name='lead-${index}']`).attr('disabled', false);

                                    }
                                } catch (error) {

                                }


                            } else if (index === 'source_id' || index === 'lead_medium_id') {
                                $(`select[name='lead-${index}']`).attr('disabled', false);
                            }

                            if ('lead_meta' == index) {

                                Object.keys(result.data[index]).map((metaIndex) => {
                                    if (result.data[index][metaIndex]['meta_key'] ==
                                        'leadtype') {
                                        if (result.data[index][metaIndex]['meta_value'] ==
                                            'COMPANY') {

                                            $('#leadtype1').prop('checked', false).trigger(
                                                "change");
                                            $('#leadtype2').prop('checked', true).trigger(
                                                "change");

                                            $('.individualForm').addClass('d-none');
                                            $('.individualForm input').prop('disabled', true);

                                            $('.companyForm').removeClass('d-none');
                                            $('.companyForm input').prop('disabled', false);

                                        } else {
                                            $('#leadtype1').prop('checked', true).trigger(
                                                "change");
                                            $('#leadtype2').prop('checked', false).trigger(
                                                "change");

                                            $('.companyForm').addClass('d-none');
                                            $('.companyForm input').prop('disabled', true);


                                            $('.individualForm').removeClass('d-none');
                                            $('.individualForm input').prop('disabled', false);
                                        }
                                    } else if (result.data[index][metaIndex]['meta_key'] ==
                                        'lead_mode') {
                                        // For View And Edit Mode (Lead)
                                        if (!fronEditLeadButton) {
                                            // setTimeout(() => {
                                            lead_mode = "view";
                                            $("#btn-edit-lead-mode").removeClass("d-none");
                                            $("#btn-edit-lead-mode").html(
                                                '<i class="mdi mdi-pencil"></i> Edit');
                                            leadFieldsDisabled(true);
                                            // }, 500);
                                        }

                                        // For View And Edit Mode (Lead)

                                    } else {



                                        $(`#metaData-${result.data[index][metaIndex]['meta_key']}`)
                                            .val(result.data[index][metaIndex]['meta_value'])
                                            .trigger("change")
                                    }
                                })
                            } else {

                                $('#leadtype1').prop('checked', true).trigger("change");
                                $('#leadtype2').prop('checked', false).trigger("change");

                                $('.companyForm').addClass('d-none');
                                $('.companyForm input').prop('disabled', true);


                                $('.individualForm').removeClass('d-none');
                                $('.individualForm input').prop('disabled', false);
                            }

                            if ($('#lead-phone').val() == null || $('#lead-phone').val() == '') {
                                $('#lead-phone').attr('disabled', false)
                            } else {
                                $('#lead-phone').attr('disabled', true)
                            }
                            if ($('#lead-whatsapp').val() == null || $('#lead-whatsapp').val() == '') {
                                $('#lead-whatsapp').attr('disabled', false)
                            } else {
                                $('#lead-whatsapp').attr('disabled', true)
                            }
                            if ($('#lead-mobile').val() == null || $('#lead-mobile').val() == '') {
                                $('#lead-mobile').attr('disabled', false)
                            } else {
                                $('#lead-mobile').attr('disabled', true)
                            }
                        })
                    }
                }
            });
        }



        function getSecondaryLead(id) {
            $.ajax({
                url: "<?php echo e(route('admin.work.deals.getLead.api')); ?>",
                type: "get",
                data: {
                    id: id
                },
                success: function(result) {
                    if (result.status == '1') {
                        Object.keys(result.data).map((index) => {

                            if (index == 'first_name' || index == 'last_name' || index == 'email' ||
                                index == 'mobile') {
                                $(`input[name='secondary_${index}[]']`).val(result.data[index]).trigger(
                                    "change")
                            }


                            if (index == 'nationality_id') {
                                $(`select[name='secondary_${index}[]']`).val(result.data[index])
                                    .trigger("change")
                            }


                        })
                    }
                }
            });
        }


        function deleteLead(id, is_not_secondary) {

            $.ajax({
                url: "<?php echo e(route('deleteLead')); ?>",
                type: "POST",
                data: {
                    lead_id: id,
                    deal_id: deal_id
                },
                success: function(result) {

                    $('#removeRow-' + id).remove();
                    $('#lead-selector').removeClass('d-none');

                    if (is_not_secondary) {
                        $('#lead_id').val('').trigger('change');
                        $('#editLeadForm').addClass('d-none');
                    } else {
                        $('#lead_id').attr('required', false);

                    }

                }
            });
        }

        function getDealLeadds(id) {

            // <?php
            // echo 'var fromInvitePage = ' . json_encode($fromInvitePage) . ';';
            //
            ?>

            // if (fromInvitePage) {
            //     $('a[href="#step-1"]').addClass('disabled');
            //     $('a[href="#step-2"]').addClass('disabled');
            // }


            $.ajax({
                url: "<?php echo e(route('admin.work.deals.getDealLeads')); ?>",
                type: "get",
                data: {
                    deal_id: deal_id
                },
                success: function(result) {

                    $('#lead-deal_id').val(deal_id);
                    data = result.data;
                    dealLeads.value = data;
                    let html = '';

                    let title_text = "Buyer";
                    if (deal.value.type == "Rental") {
                        title_text = "Tenant";
                    }
                    let hideButtons = 'd-none';
                    if(deal.value.status == 'DRAFT' || deal.value.status == 'REJECTED'){
                        hideButtons = '';
                    }

                    $('#secondary_leads').html('');

                    if (result.status == '1') {
                        $.each(data, function(index, item) {

                            if (index == 0) {
                                let isLeadInvited = false;
                                let isLeadSubmitted = false;
                                let leadInviteMsg = '';

                                <?php
                                echo 'var sources = ' . json_encode($sources) . ";\n";
                                echo 'var mediums = ' . json_encode($mediums) . ";\n";
                                echo 'var isAlreadyInvited = ' . json_encode($isAlreadyInvited) . ";\n";
                                echo 'var isDealInviteLeadCreated = ' . json_encode($isDealInviteLeadCreated) . ";\n";
                                echo 'var lead_invited = ' . json_encode($lead_invited) . ";\n";
                                ?>

                                if (lead_invited != null) {
                                    if (lead_invited.lead_id == item.id) {
                                        isLeadInvited = true;
                                    }
                                    if (lead_invited.lead_id == item.id && lead_invited
                                        .form_submitted) {
                                        console.log('Inside success condition')
                                        isLeadSubmitted = true;
                                        leadInviteMsg = `<div class="alert alert-success alert-border-left alert-dismissible fade show" role="alert">
                                                    <i class="ri-notification-off-line me-3 align-middle fs-16"></i><strong>Success</strong>
                                                    KYC form filled and submitted by lead.
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>`;
                                    } else {
                                        console.log('Inside pending condition')
                                        leadInviteMsg = `<div class="alert alert-warning alert-border-left alert-dismissible fade show" role="alert">
                                                    <i class="ri-notification-off-line me-3 align-middle fs-16"></i><strong>Pending</strong>
                                                    - Lead is invited to fill rest of the details & KYC form.
                                                     <br><small>Invited By: ${lead_invited?.invited_by?.full_name} at ${moment(lead_invited?.created_at).format('DD/MM/YYYY HH:mm')}</small>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>`;
                                    }
                                }

                                if (isAlreadyInvited == false) {
                                    $('#inviteAgentBtn').addClass('d-none');
                                }


                                if (isDealInviteLeadCreated) {
                                    isValidSubmission = true;
                                }

                                $('#lead_id').val(item.id);

                                $('#editLeadForm').addClass('d-none');

                                let source = sources.find(x => x.id === item.source_id);
                                let medium = mediums.find(x => x.id === item.lead_medium_id);

                                let metaData = {};

                                item.lead_meta?.map(m => {
                                    if (m.meta_value != null) {
                                        metaData[m.meta_key] = m.meta_value;
                                    }
                                });

                                getLead(item.id)

                                const nationalityName = getNationalityName(item.nationality_id);


                                const isCompany = (metaData.type || "INDIVIDUAL") == "COMPANY";

                                html += `<?php if($isAlreadyInvited == false || ($isAlreadyInvited == true && $isDealInviteLeadCreated == true)): ?>
                                <div class="card mt-3" id="removeRow-${item.id}">
                                        <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mr-auto p-2"><h5>Attached ` + title_text + ` (` + (index ==
                                        0 ? 'Primary' : 'Secondary ' + index) +
                                    `)</h5></div>
                                            </div>

                                            <?php if($isAlreadyInvited == false): ?>
                                            <div class="col-md-6 text-end">
                                                <div class="p-2">
                                                    <button class="btn btn-outline-primary ${hideButtons} btn-sm lead-disable-on-view-mode ` +
                                    (index == 0 ?
                                        'editAttachedLead' :
                                        'editSecondaryAttachedLead') +
                                    `" type="button" data-id="${item.id}">Edit ` + title_text +
                                    `</button>
                                                        <button class="btn btn-danger btn-sm deleteAttachedLead lead-disable-on-view-mode ${hideButtons}" type="button" data-id="${item.id}"  data-deal_id="${deal_id}">Remove ` +
                                    title_text + `</button>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                        </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mt-4">
                                                <div class="col-md-6">
                                                <table class="table table-bordered table-border-custom">

                                                    <tbody>
                                                        <tr>
                                                            <td class="tableThDark"><b>Full Name</b></td>
                                                            <td><span id="show-selected-lead-first_name">${item.first_name} ${item.last_name || ''}</span></td>
                                                        </tr>
                                                        <?php if($isAlreadyInvited == false && $isDealInviteLeadCreated == false): ?>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : ''}">
                                                            <td class="tableThDark"><b>UAE National </b></td>
                                                            <td><span id="show-selected-lead-uae_national">${metaData?.uae_national == 1 ? "Yes" : "No"}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? '' : 'lead-details-hidden d-none'}">
                                                            <td class="tableThDark"><b>Country of incorporation/registration </b></td>
                                                            <td><span id="show-selected-lead-uae_national">${metaData?.company_coi || ''}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="tableThDark"><b>Buyer Type </b></td>
                                                            <td><span id="show-selected-lead-uae_national">${metaData?.type || ''}</span></td>
                                                        </tr>


                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'd-none' : 'lead-details-hidden d-none'}">
                                                            <td class="tableThDark"><b>Trade license issue date </b></td>
                                                            <td><span id="show-selected-lead-agent">${metaData?.trade_license_issue_date || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'd-none' : 'lead-details-hidden d-none'}">
                                                            <td class="tableThDark"><b>Trade license expiry date </b></td>
                                                            <td><span id="show-selected-lead-agent">${metaData?.trade_license_expiry_date || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td class="tableThDark"><b>Residential Address </b></td>
                                                            <td><span id="show-selected-lead-address">${item?.address || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td class="tableThDark"><b>Date of Birth </b></td>
                                                            <td><span id="show-selected-lead-dob">${item?.dob || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td class="tableThDark"><b>Place of Birth </b></td>
                                                            <td><span id="show-selected-lead-dob">${item?.pob || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td class="tableThDark"><b>Lead Created Date </b></td>
                                                            <td><span id="show-selected-lead-dob">${moment(item?.created_at).format('DD/MM/YYYY')}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td class="tableThDark"><b>Source </b></td>
                                                            <td><span id="show-selected-lead-source_id">${source?.name || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Occupation </b></td>
                                                            <td><span id="show-selected-lead-source_id">${metaData?.occupation || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Years In Present Role </b></td>
                                                            <td><span id="show-selected-lead-source_id">${metaData?.year_of_experience || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Employer / Establishment Name </b></td>
                                                            <td><span id="show-selected-lead-source_id">${metaData?.employer_name || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Employer / Establishment Address </b></td>
                                                            <td><span id="show-selected-lead-source_id">${metaData?.employer_address || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td class="tableThDark"><b>Medium</b></td>
                                                            <td><span id="show-selected-lead-lead_medium_id">${medium?.name || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Politically Exposed Person (PEP)?</b></td>
                                                            <td><span id="show-selected-lead-lead_medium_id">${metaData?.political || ''}</span></td>
                                                        </tr>
                                                        <?php endif; ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-bordered table-border-custom">

                                                    <tbody>

                                                        <?php if($isAlreadyInvited == false && $isDealInviteLeadCreated == false): ?>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Company Former Name</b></td>
                                                            <td><span id="show-selected-lead-mobile">${metaData.company_former_name || ''}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="tableThDark"><b>Mobile</b></td>
                                                            <td><span id="show-selected-lead-mobile">${item.mobile || ''}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="tableThDark"><b>Email </b></td>
                                                            <td><span id="show-selected-lead-email">${item.email || ''}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="tableThDark"><b>Responsible Agent </b></td>
                                                            <td><span id="show-selected-lead-agent">${item?.agent?.name || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Nationality </b></td>
                                                            <td><span id="show-selected-lead-email">${nationalityName || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Alias Name </b></td>
                                                            <td><span id="show-selected-lead-email">${metaData.aliases || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'd-none' : 'lead-details-hidden d-none'}">
                                                            <td class="tableThDark"><b>Trading Name </b></td>
                                                            <td><span id="show-selected-lead-email">${metaData.company_trading_name || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'd-none' : 'lead-details-hidden d-none'}">
                                                            <td class="tableThDark"><b>Date of incorporation/registration </b></td>
                                                            <td><span id="show-selected-lead-email">${metaData.company_coi_date || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td class="tableThDark"><b>Telephone Number</b></td>
                                                            <td><span id="show-selected-lead-lead_medium_id">${item?.phone || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td class="tableThDark"><b>Passport Number</b></td>
                                                            <td><span id="show-selected-lead-lead_medium_id">${metaData?.passport_number || ''}</span></td>
                                                        </tr>

                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td class="tableThDark"><b>Passport Issue Date</b></td>
                                                            <td><span id="show-selected-lead-lead_medium_id">${metaData?.passport_issue_date || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td class="tableThDark"><b>Passport Expiry Date</b></td>
                                                            <td><span id="show-selected-lead-lead_medium_id">${metaData?.passport_expiry || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Fund Source</b></td>
                                                            <td><span id="show-selected-lead-lead_medium_id">${metaData?.fund_source || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Annual Income</b></td>
                                                            <td><span id="show-selected-lead-lead_medium_id">${metaData?.annual_income || ''}</span></td>
                                                        </tr>
                                                        <tr class="show-selected-lead-more-details ${isCompany ? 'lead-details-hidden d-none' : 'd-none'}">
                                                            <td class="tableThDark"><b>Payment Method</b></td>
                                                            <td><span id="show-selected-lead-lead_medium_id">${metaData?.payment_method || ''}</span></td>
                                                        </tr>


                                                        <?php endif; ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div id="secondary_leads_details_box"></div>
                                        <div>
                                            <span id="leadInviteSec"></span>
                                            ${ isLeadInvited ?
                                            leadInviteMsg :
                                            `<button type="button" id="leadInviteBtn" class="btn btn-outline-primary ${title_text == 'Tenant' ? 'd-none' : ''} btn-sm lead-disable-on-view-mode ${hideButtons}" onclick="inviteLeadForForm('${item.id}')">Invite ` + title_text + ` to fill rest of the details & KYC form</button>`}
                                            <span class="cursor-pointer" style="float: right; color: green; font-size: 20px;" onclick="toggleLeadDetails()"><i data-v-f17cf47e="" class="ri-aspect-ratio-line"></i></span>
                                        </div>
                                        </div>
                                    </div>  <?php endif; ?>`

                                $('#attachedLeads').html(html);
                                $('#attachedLeads').removeClass('d-none');
                            } else {
                                let html =
                                    `<?php if($isAlreadyInvited == false || ($isAlreadyInvited == true && $isDealInviteLeadCreated == true)): ?>
                                     <div class="row" style="padding-right: 32px" id="secondary_child_` +
                                    index +
                                    `">
                                                                    <hr class="mt-5">
                                                                    <input type="hidden" name="secondary_lead_id[]" value="` +
                                    item.id + `">

                                     <div class="col-md-6 mt-2">
                                        <h5>
                                            Secondary ` + title_text + ` <span id="leadCounter"></span>
                                        </h5>
                                    </div>
                                    <div class="col-md-6 mt-2 ">
                                        <button class="btn btn-primary float-end btn-sm deleteAttachedLead" type="button" data-secondary-index="${index}" data-id="${item.id}"  data-deal_id="${deal_id}">X</button>
                                    </div>
                        <div class="col-md-6 mt-2">
                                        <table class="table table-bordered table-border-custom table-form">
                                    <tbody>
                                <tr>
                                    <td width="30%" class="tableThDark tdAdjust">
                                         <label for="name" class="form-label">First Name <span
                                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                          <input type="text"
                                                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                    name="secondary_first_name[]"
                                                    required
                                                    value="` + item.first_name + `" placeholder="John">

                                    <div class="text-danger aliasesValidation">
                                    </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="30%" class="tableThDark tdAdjust">
                                         <label for="name" class="form-label">Email <span
                                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                          <input type="email"
                                                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode "
                                                    name="secondary_email[]"
                                                    required
                                                    value="` + item.email + `" placeholder="John@axcap.ae">


                                    <div class="text-danger aliasesValidation">
                                    </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="30%" class="tableThDark tdAdjust">
                                         <label for="name" class="form-label">Mobile <span
                                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                         <input type="text"
                                                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                    name="secondary_mobile[]"
                                                    required
                                                    value="` + item.mobile + `" placeholder="0501234567">



                                    <div class="text-danger aliasesValidation">
                                    </div>
                                    </td>
                                </tr>

                        </tbody>
                        </table>
                                        </div>



                                    <div class="col-md-6">

                                         <table class="table table-bordered table-border-custom table-form">
                                    <tbody>
                                <tr>
                                    <td width="30%" class="tableThDark tdAdjust">
                                         <label for="name" class="form-label">Last Name <span
                                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                          <input type="text"
                                                    class="deal-form-control inputs rental-prop primary-sale secondary-sale reset-validations lead-disable-on-view-mode"
                                                    name="secondary_last_name[]"
                                                    required
                                                    value="` + item.last_name + `" placeholder="Doe">

                                    <div class="text-danger aliasesValidation">
                                    </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="30%" class="tableThDark tdAdjust">
                                         <label for="name" class="form-label">Nationality <span
                                                        class="label-rental-prop label-primary-sale label-secondary-sale label-reset-validations"></span></label>

                                    </td>
                                    <td width="70%" colspan="3" class="p-0">
                                         <select
                                                        class="form-control  select2 rental-prop primary-sale secondary-sale reset-validations inputs lead-disable-on-view-mode nationality_id_${index}"
                                                        name="secondary_nationality_id[]" required>
                                                        <?php $__currentLoopData = $countries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($country->id); ?>" >
                                                                <?php echo e($country->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                </select>

                                    <div class="text-danger aliasesValidation">
                                    </div>
                                    </td>
                                </tr>
                                </tbody>
                                </table>



                                    </div>

                                </div>


                                <?php endif; ?>`
                                $('#secondary_leads').append(html);

                                $(".nationality_id_" + index).val(item.nationality_id).trigger(
                                    "change");


                                let html2 = ` <div class="card-body">
                                            <div class="row mt-4">
                                                <div class="col-md-6">
                                                <table class="table table-bordered">

                                                    <tbody>
                                                        <tr class="show-selected-lead-more-details d-none">
                                                            <td width="40%"><b>First Name</b></td>
                                                            <td width="60%"><span id="show-selected-lead-first_name">${item.first_name}</span></td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-bordered">

                                                        <tbody>
                                                            <tr class="show-selected-lead-more-details d-none">
                                                                <td width="40%"><b>Last Name</b></td>
                                                                <td width="60%"><span id="show-selected-lead-last_name">${item.last_name || ''}</span></td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                        </div>`;

                                $('#secondary_leads_details_box').append(html2);

                                $(document).ready(function() {
                                    $(".nationality_id_" + index).select2();
                                });
                            }
                        });
                        handleDealStatus(deal.value)
                    }
                    $(document).ready(function() {
                        // Disable required for all inputs and selects initially
                        $('#editLeadForm :input, #editLeadForm select').prop('required', false);
                        $('#agent_to_invite').prop('required', false);
                        // agentInviteModalLabel


                        if (deal.value.type == "Secondary Sale") {
                            // Show/hide elements based on the condition
                            checkvalidations('secondary-sale')
                            $('.label-primary-sale, .label-rental-prop').addClass('d-none');
                            $('.secondary-sale').attr('required', true);
                            $('.label-secondary-sale').removeClass('d-none');
                            $('.tenant-buyer-title').text('Buyer');
                        } else if (deal.value.type == "Primary Resale") {
                            // Show/hide elements based on the condition
                            checkvalidations('primary-sale')
                            $('.label-secondary-sale, .label-rental-prop').addClass('d-none');
                            $('.primary-sale').attr('required', true);
                            $('.label-primary-sale').removeClass('d-none');
                            $('.tenant-buyer-title').text('Buyer');
                        } else if (deal.value.type == "Rental") {
                            // Show/hide elements based on the condition
                            checkvalidations('rental-prop')
                            $('.label-secondary-sale, .label-primary-sale').addClass('d-none');
                            $('.rental-prop').attr('required', true);
                            $('.label-rental-prop').removeClass('d-none');
                            $('.tenant-buyer-title').text('Tenant');
                        }
                    });

                    $('#lead_id').attr('required', true);
                    $('#addNewLeadButton').removeClass('d-none');

                }
            });
        }



        $('#attachlead').click(function() {
            let lead_id = $('#lead_id').val();
            lead_mode = "edit";
            selectlead(lead_id);
        })

        $('#cancelSelectedLead').click(function() {
            $('#lead_id').val('').trigger('change');
            $('#showSelectLead').addClass('d-none');
        })





        function selectlead(id) {

            $.ajax({
                url: "<?php echo e(route('admin.work.deals.addDealLead')); ?>",
                type: "POST",
                data: {
                    lead_id: id,
                    deal_id: deal_id,
                    lead_type: 'Primary'

                },
                success: function(result) {


                    if (result.status == '1') {
                        $('#lead-selector,#showSelectLead').addClass('d-none');
                        $('#editLeadForm').removeClass('d-none');
                        getLead(id, true);

                        let title_text = "Buyer";
                        if (deal.value.type == "Rental") {
                            title_text = "Tenant";
                        }


                        showToastify(result.msg, "success")
                        $('#attachedLeads').append(`
                        <div class="" id="removeRow-${id}">
        <div class="">
           <div class="row">
               <div class="col-md-6">
                <div class="mr-auto p-2"><h5>Primary ` + title_text +
                            `</h5></div>
               </div>
               <div class="col-md-6 text-end">
                <div class="p-2">
                        <button class="btn btn-primary btn-sm deleteAttachedLead" type="button" data-id="${id}"  data-deal_id="${deal_id}">Remove ` +
                            title_text + `</button>

                </div>
               </div>
           </div>
        </div>

    </div>`)
                    } else {
                        showToastify(result.msg, "error");
                    }
                }
            });
        }


        $(document).on('click', '#addNewLeadButton', function() {


            $("#showSelectLead").addClass('d-none');

            let lead_deal_id_temp = $('#lead-deal_id').val();
            $('#lead_id').attr('required', false);

            $('#editLeadForm').removeClass('d-none');

            $('#leadtype1').prop('checked', true).trigger("change");
            $('#leadtype2').prop('checked', false).trigger("change");

            $('.companyForm').addClass('d-none');
            $('.companyForm input').prop('disabled', true);

            $('.individualForm').removeClass('d-none');
            $('.individualForm input').prop('disabled', false);


            $('#editLeadForm select').attr('disabled', false);

            $('#editLeadForm input , #editLeadForm select').not('#leadtype1 , #leadtype2', '#lead-deal_id',
                '#lead-id').val('');

            $("#lead-deal_id").val(lead_deal_id_temp);
            $("#lead-selector").addClass('d-none');

        });



        // $(document).on('click', '#addNewLeadButton', function() {
        //     $('#editLeadForm').removeClass('d-none');


        //     $('#leadtype1').prop('checked', true).trigger("change");
        //     $('#leadtype2').prop('checked', false).trigger("change");

        //     $('.companyForm').addClass('d-none');
        //     $('.companyForm input').prop('disabled', true);

        //     $('.individualForm').removeClass('d-none');
        //     $('.individualForm input').prop('disabled', false);

        //     $('#lead_id').attr('required', false);

        // });

        $(document).on('click', '#inviteAgentBtn', function() {

            $('#agentInviteModal').modal('show');
        });


        $(document).on('change', '#agent_to_invite', function() {
            let agent_id = $(this).val();
            if (agent_id != '') {
                $('#agentInviteDetails').removeClass('d-none');
            } else {
                $('#agentInviteDetails').addClass('d-none');
            }

            <?php echo 'var agents = ' . json_encode($agents) . ";\n"; ?>

            let agent = agents.find(x => x.id == agent_id);
            $('#show-agent-name').text(agent?.name)
            $('#show-agent-mobile').text(agent?.mobile)
            $('#show-agent-mobile').attr('href', 'tel:' + agent?.mobile)
            $('#show-agent-email').text(agent?.email)
            // mailto:primary@axcapital.ae
            $('#show-agent-email').attr('href', 'mailto:' + agent?.email)
            $('#show-agent-image').attr('src', agent?.thumbnail)

        });


        $(document).on('click', '#inviteAgentFromDetailsPage', function() {
            let agent_id = $('#agent_to_invite').val();


            $.ajax({
                url: "<?php echo e(route('admin.work.deals.generateInviteLink')); ?>",
                type: "POST",
                data: {
                    agent_id: agent_id,
                    deal_id: deal_id,
                    _token: "<?php echo e(csrf_token()); ?>"
                },
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.message, "success")
                        // agentInviteModal
                        $('#agentInviteModal').modal('hide');
                        <?php echo 'var agents = ' . json_encode($agents) . ";\n"; ?>

                        let agent = agents.find(x => x.id == agent_id);
                        $('#alert_agent_name').text(agent?.first_name + ' ' + agent?.last_name);
                        $('#invite_sent_success_alert').removeClass('d-none');

                        $('#lead-selector').addClass('d-none');
                        $('#showSelectLead').addClass('d-none');
                    } else {
                        showToastify(result.message, "error");
                    }
                }
            });
        });





        $(document).on('click', '.editAttachedLead', function() {
            let id = $(this).data('id');
            lead_mode = "edit";
            $("#metaData-lead_mode").val(lead_mode);
            $('#editLeadForm').removeClass('d-none');
            $('#attachedLeads').addClass('d-none');
            getLead(id, true);
        })

        // Close Lead Form Button
        $(document).on('click', '#editLeadFormCloseBtn', function() {
            $('#editLeadForm').addClass('d-none');
            $('#attachedLeads').removeClass('d-none');
            $('#lead-selector').removeClass('d-none');
            getDealLeadds(deal_id);
        });
        // Close Lead Form Button


        $(document).on('click', '.editSecondaryAttachedLead', function() {
            let id = $(this).data('id');
            getSecondaryLead(id);
        })

        $(document).on('click', '.deleteAttachedLead', function() {

            let id = $(this).data('id');
            let secondary_child_index = $(this).data('secondary-index');

            if (secondary_child_index) {
                $('#secondary_child_' + secondary_child_index).remove();
            }

            if (secondary_child_index == undefined) {
                $('#inviteAgentBtn').removeClass('d-none');
                $('#lead_id').attr('required', true);
                deleteLead(id, true);
            } else {
                deleteLead(id, false);
            }

        })

        function showLead(id) {
            if (id == '' || id == null || id == undefined) {
                return false;
            }

            $.ajax({
                url: "<?php echo e(route('admin.work.leads.get')); ?>",
                type: "get",
                data: {
                    id: id
                },
                success: function(result) {
                    if (result.status == '1') {
                        show_lead_id = result.data.id;
                        show_first_name = result.data.first_name;
                        show_last_name = result.data.last_name;
                        show_email = result.data.email;
                        show_mobile = result.data.mobile;

                        $('#show-lead-first_name').text(result.data.first_name)
                        $('#show-lead-last_name').text(result.data.last_name)
                        $('#show-lead-mobile').text(result.data.mobile)
                        $('#show-lead-email').text(result.data.email)


                        <?php
                        echo 'var sources = ' . json_encode($sources) . ";\n";
                        echo 'var mediums = ' . json_encode($mediums) . ";\n";
                        ?>

                        let source = sources.find(x => x.id === result.data.source_id);
                        let medium = mediums.find(x => x.id === result.data.lead_medium_id);

                        $('#show-lead-source_id').text(source?.name)
                        $('#show-lead-lead_medium_id').text(medium?.name)

                        $('#showSelectLead').removeClass('d-none');
                    }
                }
            });
        }

        $(document).on("submit", ".dealLeadForm", function(event) {

            // if(!(mobileInput.isValidNumber())){
            //     showToastify("Please enter a valid mobile number", "error");
            //     $("#lead-mobile").attr('disabled', false);
            //     $("#lead-mobile").focus();
            //     return false;
            // }


            // if(!(mobileInput3.isValidNumber())){
            //     showToastify("Please enter a valid whatsapp number", "error");
            //     return false;
            // }


            // if(!(mobileInput2.isValidNumber())){
            //     showToastify("Please enter a valid phone number", "error");
            //     return false;
            // }

            event.preventDefault();


            let id = $("#lead-id").val();
            $("#lead-deal_id").val(deal_id);

            let formData = new FormData($(this)[0]);

            let modifiedData = {};
            modifiedData.lead_meta = {};

            // Iterate through each pair in the original FormData
            formData.forEach((value, key) => {

                let modifiedKey = key.replace(/^metaData-/, '').replace(/-/g, '');

                // Check if the key has "meta-" prefix
                if (key.startsWith('metaData-')) {
                    modifiedData[modifiedKey] = value;
                } else {
                    // Check if the key already exists in modifiedData
                    if (modifiedData.hasOwnProperty(modifiedKey)) {
                        // If it exists and is not an array, convert it to an array
                        if (!Array.isArray(modifiedData[modifiedKey])) {
                            modifiedData[modifiedKey] = [modifiedData[modifiedKey]];
                        }
                        // Add the new value to the array
                        modifiedData[modifiedKey].push(value);
                    } else {
                        // If the key doesn't exist, add it to modifiedData
                        modifiedData[modifiedKey.replace(/^lead/, '')] = value;
                    }
                }
            });

            modifiedData['agent_id'] = agent_id;
            modifiedData['updated_from'] = 'deal';
           // modifiedData['lead_mode'] = lead_mode;



            // For View And Edit Mode (Lead)
            if (lead_mode == 'edit') {
                // For View And Edit Mode (Lead)

                if (!form_already_submited) {
                    $.ajax({
                        url: "<?php echo e(route('admin.work.deals.updateLead')); ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: modifiedData,
                        cache: false,
                        success: function(result) {
                            if (result.status == 1) {
                                lead_step = true;
                                form_already_submited = true;
                                showToastify(result.msg, "success")
                                isValidSubmission = true;
                                // $("#smartwizard").smartWizard("next");
                                if (nextStep) {
                                    $('#smartwizard').smartWizard("goToStep", nextStep);
                                }
                                isValidSubmission = false;

                            }else if (result.status == 4) {

                            } else {
                                form_already_submited = false;

                                showToastify(result.msg, "error");
                            }
                        },
                        error: function(jqXhr, json, errorThrown) {
                            form_already_submited = false;

                            if (jqXhr.responseJSON.errors != undefined) {
                                $.each(jqXhr.responseJSON.errors, function(key, value) {
                                    showToastify(value, "error");
                                });
                            } else {
                                console.log(jqXhr.responseJSON.message);
                            }
                        },
                    });
                }

                // For View And Edit Mode (Lead)


            } else {
                isValidSubmission = true;
                // $("#smartwizard").smartWizard("next");
                if (nextStep) {
                    $('#smartwizard').smartWizard("goToStep", nextStep);
                }
                isValidSubmission = false;
            }
            // For View And Edit Mode (Lead)
        })

        $('.select2').select2({
            placeholder: 'Select',
            allowClear: true,
        });

        function toggleLeadDetails() {
            if ($(".show-selected-lead-more-details").not(".lead-details-hidden").hasClass("d-none")) {
                $(".show-selected-lead-more-details").not(".lead-details-hidden").removeClass("d-none")
            } else {
                $(".show-selected-lead-more-details").not(".lead-details-hidden").addClass("d-none")
            }
        }

        function getNationalityName(id) {
            if (id == null) {
                return '';
            }
            if (countries != null) {
                let countryData = countries.find((country) => country.id == id);
                if (countryData) {
                    return countryData.nationality;
                } else {
                    return '';
                }
            } else {
                return '';
            }
        }
        const inviteLeadForForm = (lead_id) => {
            let user_title = "buyer";
            if (deal.value.type == "Rental") {
                user_title = "tenant";
            }
            swal({
                    title: "Are you sure?",
                    text: `Do you really want to invite this ${user_title} to fill rest of the details & KYC form?`,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: "<?php echo e(route('admin.deals_revamped.lead.invite.form')); ?>",
                            type: "POST",
                            data: {
                                lead_id: lead_id,
                                deal_id: deal_id,
                                _token: "<?php echo e(csrf_token()); ?>"
                            },
                            success: function(result) {
                                if (result.status == '1') {
                                    showToastify(result.msg, "success")
                                    $('#inviteAgentBtn').addClass('d-none');
                                    $('#lead_id').attr('required', false);
                                    // $('#invite_sent_success_alert').removeClass('d-none');
                                    $("#leadInviteBtn").addClass('d-none');
                                    $('#leadInviteSec').html(`<div class="alert alert-warning alert-border-left alert-dismissible fade show" role="alert">
                                                    <i class="ri-notification-off-line me-3 align-middle fs-16"></i><strong>Pending</strong>
                                                    - Lead is invited to fill rest of the details & KYC form.
                                                    <br><small>Invited By: ${result.data.invited_by.full_name} at ${moment(result.data.created_at).format('DD/MM/YYYY HH:mm')}</small>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>`);
                                } else {
                                    showToastify(result.msg, "error");
                                }
                            }
                        });
                    }
                });
        }
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/deals_revamped/partials/_lead.blade.php ENDPATH**/ ?>