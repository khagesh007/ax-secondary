<?php
if(!empty($deal)){
     $agent_id = $deal->agent_id;
        $property_id = $deal->property_id;
}else{
    $agent_id = isset($_GET['agent_id']) ? $_GET['agent_id'] : 0;
    if($agent_id == 0 && auth()->user()->hasRole('Agent')){
        $agent_id = auth()->id();
    }
    $property_id = isset($_GET['property_id']) ? $_GET['property_id'] : 0;
    }
?>
<div class="row mt-3 ">
    <div class="text-end">
        <div id="btn-edit-property-mode" class=" btn mb-4 <?php echo e($edit_btn); ?> d-none w-20 btn-outline-secondary edit-action">
            <i class="mdi mdi-pencil"></i> Edit
        </div>
    </div>
</div>

<div class="row " >


    <div class="col-6 property-transaction_type <?php if(!empty($lead)): ?> <?php if($deal->deal_type == 'Rental'): ?> d-none  <?php endif; ?>  <?php endif; ?>">
        <div class="row mt-2">
            <div class="col-4">


            </div>
            <div class="col-8">

            </div>
        </div>
    </div>
    <div class="col-6 property-transaction_type">
       <div class="row d-none">
           <div class="col-4 mt-2">

           </div>
           <div class="col-8 mt-2">

           </div>
       </div>
    </div>
</div>

<div class="row <?php if(!empty($deal)): ?> d-none <?php endif; ?>" id="property-selection-area">
    <div class="col-6">
    <table class="table table-bordered table-border-custom">

        <tbody>
            <tr>
                <td width="30%" class="tableThDark tdAdjust">
                    <input type="hidden" id="deal_id" name="deal_id" value="<?php echo e(request()->deal_id); ?>">
                    
                    <input type="text" hidden id="meta-property_mode" name="meta-property_mode" value="view">
                    <label for="first-name" class="form-label">Select Agent *</label>
                </td>
                <td width="70%" colspan="3"
                 class="tdInput p-0"
                >
                    <?php if(auth()->user()->hasRole('Agent')): ?>
                <input type="hidden" name="agent_id" id="agent_id" value="<?php echo e(auth()->id()); ?>">
                <input type="hidden" id="agent_name" value="<?php echo e(auth()->user()->full_name); ?>">
               <span class="p-2" ><?php echo e(auth()->user()->full_name); ?></span>
                <?php else: ?>

                <select name="" id="agent_id" class="form-control select2" required>
                    <option value=""></option>
                    <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(auth()->user()->hasRole('Agent')): ?>
                            <option value="<?php echo e($agant->id); ?>" <?php if($agant->id == auth()->id()): ?> selected <?php endif; ?>><?php echo e($agant->full_name); ?></option>
                        <?php else: ?>
                        <option

                        value="<?php echo e($agant->id); ?>"><?php echo e($agant->full_name); ?></option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                    <?php endif; ?>

                <div class="invalid-feedback">
                    Please Select an Agent
                </div>


                </td>
            </tr>
            <tr>
                <td width="30%" class="tableThDark tdAdjust"><b>Select Property *</b></td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >

                    <select name="" id="property_id" class="form-control" required >
                        <option value="">Property</option>
                    </select>


                    <div class="invalid-feedback">
                        Please Select a Property
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="col-6">
    <table class="table table-bordered table-border-custom">

        <tbody>
            <tr>
                <td width="30%" class="tableThDark tdAdjust">

                    <label for="deal_type_selector" class="form-label">Deal Type *</label>
                </td>
                <td width="70%" colspan="3"
                class="tdInput p-0">

                    <select name="deal_type" id="deal_type_selector" class="select2" required>
                        <option value="">Select Deal Type</option>
                        <option <?php if(!empty($lead)): ?> <?php if($deal->deal_type == 'Rental'): ?> selected  <?php endif; ?>  <?php endif; ?> value="Rental">Rental</option>
                        <option <?php if(!empty($lead)): ?> <?php if($deal->deal_type == 'Offplan Resale'): ?> selected  <?php endif; ?>  <?php endif; ?> value="Offplan Resale">Offplan Resale</option>
                        <option <?php if(!empty($lead)): ?> <?php if($deal->deal_type == 'Secondary Sale'): ?> selected  <?php endif; ?>  <?php endif; ?> value="Secondary Sale">Secondary Sale</option>
                    </select>


                    <div class="invalid-feedback">
                        Please Select a deal type
                    </div>

            </td>
            </tr>
            <tr class="<?php if(!empty($lead)): ?> <?php if($deal->deal_type == 'Rental'): ?> d-none  <?php endif; ?>  <?php endif; ?> property-transaction_type">
                <td width="30%" class="tableThDark tdAdjust"> <label class=""> Transaction Type*
                    <span class="label-sales-prog-prop"></span>
                </label></td>
                <td width="70%" colspan="3"  class="tdInput p-0">
                    <select id="property-transaction_type" name="transaction_type" class="form-control select2" onchange="getSalesProgression('property-transaction_type','property-sales_progression')">
                        <option value="">Select Transaction Type</option>
                        <?php $__currentLoopData = \App\Models\Deal::TRANSACTION_TYPE_SELECT; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option <?php if($deal !=null): ?>
                                        <?php if($deal?->progression?->transaction_type == $key): ?>
                                            selected
                                    <?php endif; ?>

                            <?php endif; ?> value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>
                    <div class="invalid-feedback">Please Select Transaction Type</div>
                </td>
            </tr>
            <tr class="property-transaction_type">
                <td width="30%" class="tableThDark tdAdjust"> <label>Sales Progression
                    <span class="label-sales-prog-prop"></span>
                </label>

                <button type="button"
                        data-bs-container="body"
                        data-bs-placement="right"
                        data-bs-toggle="tooltip"
                        data-bs-html="true"
                        title="
                                 <b>Auto selected Sales Progression Person may be a</b>
                                 <br>- Person with lowest sells
                                 <br>- Person who hasn't sold recently"

                        style="border: 0px solid;border-radius: 13px;"
                >
                    ?
                </button>

                </td>
                <td width="70%" colspan="3"  class="tdInput p-0">
                    <select id="property-sales_progression" name="conveyancer_id" class="form-control sales-prog-prop select2">


                    </select>
                    <div class="invalid-feedback">Please Select Sales Progression Agent</div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</div>
    <div class="row">

    <div class="col-md-12">
        <div class="row mt-4 d-none" id="PropertySelection">
            <div class="col-md-6">
                <table class="table table-bordered table-border-custom">

                    <tbody>
                        <tr>
                            <td width="30%" class="tableThDark"><b>Reference #</b></td>
                            <td width="70%" colspan="3"><span id="ref_no" class="property_ref_no"></span></td>
                        </tr>
                        <tr>
                            <td class="tableThDark"><b>Agent</b></td>
                            <td colspan="3"><span id="agent"></span></td>
                        </tr>




                        <tr>
                            <td class="tableThDark"><b>Unit No </b></td>
                            <td colspan="3"><span id="unit_no" class="property_unit_no"></span></td>
                        </tr>
                        <tr>
                            <td class="tableThDark"><b>Price (AED)</b></td>
                            <td>
                                 <span id="price" class="listing_price"></span>
                            </td>
                            <td width="15%" class="tableThDark">Per Sqft</td>
                            <td> <span class="price_sqft"></span></td>
                        </tr>



                        <tr>
                            <td class="tableThDark"><b>Location</b></td>
                            <td colspan="3"><span id="location" class="property_location"></span></td>
                        </tr>

                        <tr>
                            <td class="tableThDark"><b>Type </b></td>
                            <td colspan="3"><span id="property_type" class="property_type"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered table-border-custom">

                    <tbody>

                        <tr>
                            <td class="tableThDark"><b>Publish Status</b></td>
                            <td><span id="publish_status" class="property_status"></span></td>
                        </tr>
                        <tr>
                            <td class="tableThDark"><b>Category</b></td>
                            <td><span id="property_category" class="property_category"></span></td>
                        </tr>

                        <tr>
                            <td width="30%" class="tableThDark"><b>Bed</b></td>
                            <td width="70%"><span id="beds" class="property_bed"></span></td>
                        </tr>
                        <tr>
                            <td class="tableThDark"><b>Bath</b></td>
                            <td><span id="baths" class="property_bath"></span></td>
                        </tr>


                        <tr>
                            <td class="tableThDark"><b>Build Up Area</b></td>
                            <td><span id="built_up_area" class="property_bua"></span></td>
                        </tr>
                        <tr>
                            <td class="tableThDark"><b>Completion Status</b></td>
                            <td><span id="completion_status" class="property_completion_status"></span></td>
                        </tr>


                    </tbody>
                </table>
            </div>




            
        </div>
    </div>
</div>




<?php $__env->startPush('page-js'); ?>
    <script>



            var property_mode = "edit";

            $('#btn-edit-property-mode').click(function() {
                if (property_mode == "edit") {
                    $("#property-selection-area").addClass('d-none');
                    property_mode = "view";
                    propertyFieldsDisabled(true);
                    $('#btn-edit-property-mode').html('<i class="mdi mdi-pencil"></i> Edit');
                } else {
                    property_mode = "edit";
                    getDealProgression(deal_id)
                    $("#property-selection-area").removeClass('d-none');
                    propertyFieldsDisabled(false);
                    $('#btn-edit-property-mode').html('<i class="mdi mdi-eye"></i> View');
                }
            })

            function propertyFieldsDisabled(disbaled){
                $('#agent_id').prop('disabled', disbaled);
                $('#deal_type_selector').prop('disabled', disbaled);
                $('#property_id').prop('disabled', disbaled);
                $('#property-transaction_type').prop('disabled', disbaled);
                $('#property-sales_progression').prop('disabled', disbaled);
            }

        // For View And Edit Mode (Property)

        $(document).ready(function() {
            <?php
                echo 'var fromInvitePage = ' . json_encode($fromInvitePage) . ';';
            ?>

            if (fromInvitePage) {
                $('a[href="#step-1"]').addClass('disabled');
                $('a[href="#step-2"]').addClass('disabled');
            }
            $('#property_id').select2({
                placeholder: 'Select listing',
                allowClear: true,
                templateResult: formatProperty,
            });
            if(deal.value != null){
              let dealTypes = (deal.value.type != null && deal.value.type != undefined ) ? deal.value.type : '<?php echo e($deal?->type); ?>';
                $('#deal_type_selector').val(dealTypes).trigger('change');
            }

            $(".select2ex").select2({
                placeholder: 'Select agent',
                allowClear: true,
            });
        })


        <?php if(!auth()->user()->hasRole('Agent')): ?>
        $('#agent_id').select2({
            placeholder: 'Select Agent',
            allowClear: true,
        });
        <?php endif; ?>

        //trigger change on agent_id
        setTimeout(() => {
            <?php if($agent_id !=''): ?>
            $('#agent_id').val(<?php echo e($agent_id ?? " "); ?>).trigger('change');
            <?php else: ?>
            $('#agent_id').val(<?php echo e($agent_id ?? " "); ?>).trigger('change');
            <?php endif; ?>
        }, 100);
        const getDealProgression = (deal_id) => {
            $.ajax({
                url: "<?php echo e(route('get.deal.sales.progression')); ?>?deal_id=" + deal_id,
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    if (result.status == '1') {
                        let data = result.data || {};
                        dealSalesProgression.value = data;
                        if(data[0] !=null){
                        $("#property-transaction_type").val(data[0].transaction_type).trigger("change");
                        }
                        setTimeout(() => {
                            if(data.conveyancer_id != null) {
                                deal_convencer_id = data.conveyancer_id;
                            }
                           // $("#property-sales_progression").val(data.conveyancer_id).trigger("change");
                        }, 1000);


                        // $("#" + progression_user_field).val('').trigger('change');
                        // $("#" + progression_user_field).empty();
                        // $("#" + progression_user_field).append(html);
                    } else {
                      //  $("#" + progression_user_field).val('').trigger('change');
                    }
                }
            })
        }
            const getSalesProgression = (transactionTypeField,progression_user_field) => {
                let transaction_type = $("#"+transactionTypeField).val();

                if(transaction_type) {

                    $.ajax({
                        url: "<?php echo e(route('deal.get.sales.progressions')); ?>?transaction_type=" + transaction_type,
                        type: 'GET',
                        dataType: 'json',
                        success: function (result) {
                            if (result.status == '1') {
                                let html = '';
                                let users = result.data || {};

                                for (const key in users) {

                                    if (deal_convencer_id == users[key].id) {
                                        html += `<option value="${users[key].id}" selected>${users[key].full_name}</option>`;
                                    } else {
                                        html += `<option value="${users[key].id}">${users[key].full_name}</option>`;
                                    }
                                }

                                $("#" + progression_user_field).val('').trigger('change');
                                $("#" + progression_user_field).empty();
                                $("#" + progression_user_field).append(html).trigger('change');
                            } else {
                                $("#" + progression_user_field).val('').trigger('change');
                            }
                        }
                    });
                }else{
                    $("#" + progression_user_field).val('').trigger('change');
                }
            }
            function getDealProperties(){
                var agent_id = $("#agent_id").val();
                let fromDealType = "<?php echo e($deal?->type != null ? $deal->type : ''); ?>";

                let deal_type67 = (deal.value.type != null && deal.value.type != undefined && deal.value.type != '') ? deal.value.type : '<?php echo e($deal?->type); ?>';
                if(deal_type67 == null || deal_type67 == ''){
                     deal_type67 = $("#deal_type_selector").val();
                }

                if(property_mode == 'edit'){
                    deal_type67 = $("#deal_type_selector").val();

                }

                $.ajax({
                    url: "<?php echo e(route('ajax.deal.properties.select2')); ?>",
                    type: "POST",
                    data: {
                        agent_id: agent_id,
                        _token: "<?php echo e(csrf_token()); ?>",
                        property_id: <?php echo e($property_id ?? ""); ?>,
                        for:  deal_type67
                    },
                    success: function(response) {
                        let options = '';
                        options += '<option value="">Select Property</option>';
                        let property_found = false;
                        let selectedProperty = <?php echo e($property_id ?? ""); ?>;
                        if(selectedProperty == '' || selectedProperty == null){
                            selectedProperty = deal.value.property_id || '';

                        }

                        $.each(response.data, function(index, item) {
                            if(item.id == <?php echo e($property_id ?? ""); ?>){
                                property_found = true;
                            }
                            options +=
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
                        });
                        $('#property_id').html(options);
                        // $('#property_id').select2({
                        //     placeholder: 'Select listing',
                        //     allowClear: true,
                        //     templateResult: formatProperty,
                        // });


                        //if(property_mode == "edit"){
                        if(property_found == false){
                            $('#property_id').val('').trigger('change');
                            $('#PropertySelection').addClass('d-none');
                        }else{
                            $('#property_id').val(selectedProperty);
                            $('#PropertySelection').removeClass('d-none');
                        }
                    //}



                    }
                });
            }

         $(document).on('change', '#agent_id', function() {
             //#select_deal_type
           // $(document).on('select2:select','#agent_id',function (e) {
             getDealProperties();
        })

                $(document).on('select2:clear', '#deal_type_selector', function() {
                    $('#property_id').val('').trigger('change');
                    getDealProperties();
                })
            $(document).on('select2:select', '#deal_type_selector', function() {
                $('#property_id').val('').trigger('change');
                $('#property_id').empty();
                if($(this).val() == 'Rental'){
                $(".property-transaction_type").addClass('d-none');
                }else{
                $(".property-transaction_type").removeClass('d-none');
                }
                    getDealProperties();
                })

        $(document).on('select2:clear', '#property_id', function() {

            $('#PropertySelection').addClass('d-none');
        $("#property_id").prop("required", true);
        });

       $(document).on('change', '#property_id', function() {

           if($(this).val() == ''){

               $('#PropertySelection').addClass('d-none');
               $("#property_id").prop("required", true);
               return;
           }else {

               // $(document).on('select2:select','#property_id',function (e) {
               $('#PropertySelection').removeClass('d-none');
               $('#ref_no').text($(this).find(':selected').data('ref_no'));
               $('#unit_no').text($(this).find(':selected').data('unit_no'));
               $('#type').text($(this).find(':selected').data('type'));
               $('#location').text($(this).find(':selected').data('location'));
               $('#beds').text($(this).find(':selected').data('beds'));
               $('#baths').text($(this).find(':selected').data('baths'));
               $('#publish_status').text($(this).find(':selected').data('publish_status'));
               $('#property_type').text($(this).find(':selected').data('property_type'));
               $('#property_category').text($(this).find(':selected').data('property_category'));
               $('#built_up_area').text($(this).find(':selected').data('built_up_area'));
               let bua = $(this).find(':selected').data('built_up_area');
               let price = $(this).find(':selected').data('price');
               if(price > 0){
               $('#listing_price_label').text('AED '+numberFormat(price || 0));
               let per_sqft = price / bua;
               per_sqft = (isNaN(per_sqft) || per_sqft == null)? 0 : per_sqft;
                $('.price_sqft').text(numberFormat(per_sqft || 0));
               }
            if(property_mode == 'edit'){
                $('.listing_price').text(numberFormat(price || 0));
            }

               //$('.property-listing-price').html(numberFormat(price || 0));

               $('#completion_status').text($(this).find(':selected').data('completion_status'));
               let property_agent = $('#agent_id').find(':selected').text();
               property_agent = (property_agent != '' && property_agent != null) ? property_agent : $("#agent_name").val();
               $('#agent').text(property_agent);
               //$('#deal_type').text($(this).find(':selected').data('for'));


               var propertyType = $(this).find(':selected').data('property_type');
               var completionStatus = $(this).find(':selected').data('completion_status');

               if (propertyType === 'Residential for sale' || propertyType === 'Commercial for sale') {
                   if (completionStatus === 'Ready Primary' || completionStatus === 'Off-Plan Primary') {
                       $('#deal_type').text('Offplan Resale');
                       $('#property_type').text('Offplan Resale');
                   } else {
                       $("#select_deal_type").val("Rental").trigger("change");
                       $('#deal_type').text('Secondary Sale');
                       $('#property_type').text('Secondary Sale');
                   }

                   $("#property-transaction_type").val("").trigger("change");
                   $("#property-transaction_type").prop("required", true);
                   $("#property-sales_progression").prop("required", true);
                   $(".property-transaction_type").removeClass('d-none');
                     $("#property-transaction_type option[value='Rental']").prop("disabled", true);
               } else if (propertyType === 'Residential for rent' || propertyType === 'Commercial for rent') {
                   $('#deal_type').text('Rental');
                   $("#select_deal_type").val("Rental").trigger("change");
                   $('#property_type').text('Rental');
                   $("#property-transaction_type").val("").trigger("change");
                   $("#property-transaction_type").prop("required", false);
                   $("#property-sales_progression").prop("required", false);
                   $(".property-transaction_type").addClass('d-none');
                   $(".price_sqft").text('');
               }

               $('#PropertySelection').removeClass('d-none');
           }

        })



        $(document).on("submit", ".dealPropertyForm", function(event) {
            console.log('property form')
            event.preventDefault();
            isValidSubmission = false;
            // let deal_id = $('#deal_id').val();
            let agent_id = $('#agent_id').val();
            let property_id = $('#property_id').val();
            // For View And Edit Mode (Property)
            let property_mode_field_values = $('#meta-property_mode').val();
            let transaction_type = $('#property-transaction_type').val();
            let selectedConvencer = $('#property-sales_progression').val();

            deal_convencer_id = selectedConvencer;
            if (property_mode == "edit") {
            // For View And Edit Mode (Property)
                if (deal_id == '' || deal_id == null) {

                            $('#cover-spin').show(0)
                            $.ajax({
                                url: "<?php echo e(route('createDeal')); ?>",
                                type: "POST",
                                data: {
                                    agent_id: agent_id,
                                    property_id: property_id,
                                    deal_id: deal_id,
                                    // For View And Edit Mode (Property)
                                    property_mode: property_mode_field_values,
                                    // For View And Edit Mode (Property)
                                    transaction_type: transaction_type,
                                    conveyancer_id: selectedConvencer,
                                },
                                success: function (response) {
                                    $('#cover-spin').hide(0)
                                    if (response.status == '1') {
                                        deal_id = response.data.id;
                                        console.log('herer')
                                        getDeal(deal_id)
                                        contact_id = response.data.contact_id;
                                        showToastify(response.msg, "success")
                                        $('#deal_id').val(deal_id);
                                        // $('#saveDeal').text('Update Property');
                                        let url = new URL(window.location.href);
                                        url.searchParams.set('deal_id', deal_id);
                                        window.history.pushState({}, '', url);
                                        getContactInformation(contact_id);
                                        getLeads();
                                        isValidSubmission = true;
                                        // $('#smartwizard').smartWizard("goToStep", 2);
                                        if(nextStep){
                                            $('#smartwizard').smartWizard("goToStep", nextStep);
                                        }
                                        isValidSubmission = false;
                                    } else {
                                        isValidSubmission = false;
                                        showToastify("Error From the server", "error")
                                    }
                                },
                                error: function (result) {
                                    $('#cover-spin').hide(0)
                                    isValidSubmission = false;
                                    showToastify(result.message, "error");
                                }
                            });


                }else{
                    $.ajax({
                        url: "<?php echo e(route('createDeal')); ?>",
                        type: "POST",
                        data: {
                            agent_id: agent_id,
                            property_id: property_id,
                            deal_id: deal_id,
                            // For View And Edit Mode (Property)
                            property_mode: property_mode_field_values,
                            // For View And Edit Mode (Property)
                            transaction_type: transaction_type,
                        },
                        success: function (response) {
                            if (response.status == '1') {
                                deal_id = response.data.id;
                                getDeal(deal_id)
                                contact_id = response.data.contact_id;
                                showToastify(response.msg, "success")
                                $('#deal_id').val(deal_id);
                                // $('#saveDeal').text('Update Property');
                                let url = new URL(window.location.href);
                                url.searchParams.set('deal_id', deal_id);
                                window.history.pushState({}, '', url);
                                getContactInformation(contact_id);
                                getLeads();
                                isValidSubmission = true;
                                //$("#smartwizard").smartWizard("next");
                                if(nextStep){
                                    $('#smartwizard').smartWizard("goToStep", nextStep);
                                }
                                isValidSubmission = false;
                            } else {
                                isValidSubmission = false;
                                showToastify("Error From the server", "error")
                            }
                        },
                        error: function (result) {
                            isValidSubmission = false;
                            showToastify(result.message, "error");
                        }
                    });
                }
            // For View And Edit Mode (Property)
            } else {
                isValidSubmission = true;
                if(nextStep){
                  //  console.log("here");
                    $('#smartwizard').smartWizard("goToStep", nextStep);
                 }
                //$("#smartwizard").smartWizard("next");
                isValidSubmission = false;
            }
            // For View And Edit Mode (Property)




        })



        function formatProperty(e) {
            let f_unit_no = $(e.element).data('unit_no');
            let f_ref_no = $(e.element).data('ref_no');
            let f_location = $(e.element).data('location');
            let f_type = $(e.element).data('for');
            let f_beds = $(e.element).data('beds');
            let f_first_image = $(e.element).data('first_image') ? $(e.element).data('first_image') :
                'https://ui-avatars.com/api/?name=' + f_unit_no + '&color=7F9CF5&background=random';
            return e.id ? $('<span><img src="' + f_first_image + '" class="img-flag rounded-5" width="18" /> ' + f_ref_no +
                ' - ' + f_type + ' - ' + f_unit_no +
                "</span> <br> <span class='small ml-5 d-inline-block'> <i class='mdi mdi-bed-double'></i>" + f_beds +
                "</span> <span class='small ml-5 d-inline-block'> <i class='mdi mdi-google-maps'></i>" + f_location +
                "</span> ") : e.text;
        }

        <?php if(isset($deal) && !empty($deal)): ?>
            setTimeout(() => {
                getDealProgression('<?php echo e($deal->id); ?>');
            }, 2000);
        <?php endif; ?>
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/deals_revamped/partials/_property.blade.php ENDPATH**/ ?>