<div class="modal fade" id="dealCreateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title text-uppercase" id="exampleModalLabel">Initialize Deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dealInitForm" class="checkout-tab">
                <!--end modal-body-->
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label for="agent" class="form-label">Select Agent</label>
                            <select class="form-control select2" id="deal_agent_id" name="agent_id">
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <div>
                                <label for="lead" class="form-label">Lead</label>
                                <select class="form-control eventInputs" name="lead_ids[]" id="deal_lead_id" multiple required>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div>
                                <label for="property" class="form-label">Property</label>
                                <select class="form-control eventInputs" name="property_id" id="deal_property_id"
                                        required>
                                </select>
                            </div>
                        </div>
                        <!--end col-->

                        <div class="col-lg-12 d-none" id="property_details_container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fs-14 text-primary mb-0">Property Details</h5>
                            </div>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Reference #</h6>
                                    </div>
                                    <span class="text-muted" id="property_reference_no"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Unit #</h6>
                                    </div>
                                    <span class="text-muted" id="property_unit_no"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Category</h6>
                                    </div>
                                    <span class="text-muted" id="property_category"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Location</h6>
                                    </div>
                                    <span class="text-muted" id="property_location"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Price</h6>
                                    </div>
                                    <span class="text-muted" id="property_price"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-12 d-none" id="contact_details_container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fs-14 text-primary mb-0">Contact Details</h5>
                            </div>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Name</h6>
                                    </div>
                                    <span class="text-muted" id="contact_name"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Mobile</h6>
                                    </div>
                                    <span class="text-muted" id="contact_mobile"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Email</h6>
                                    </div>
                                    <span class="text-muted" id="contact_email"></span>
                                </li>

                            </ul>

                        </div>

                        <div class="col-6 d-none">

                            <label>Deal Price</label>

                                <input type="text" class="form-control" name="deal_price" id="deal_price">

                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <div class="d-flex align-items-start gap-3 mt-3">
                                <button type="submit" id="initSubmitBtn" class="btn btn-primary btn-label right ms-auto"
                                        data-nexttab="property-details-tab"><i
                                        class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Start Deal
                                </button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->


                <!--end modal-body-->
            </form>
        </div>
    </div>
</div>
<?php $__env->startPush('partial-scripts'); ?>
    <script src="<?php echo e(URL::asset('/assets/js/pages/crypto-kyc.init.js')); ?>"></script>
    <script type="text/javascript">
        $("#deal_agent_id").getAgentSelect2('#dealCreateModal',true,true,'Select Agent');
        // Property Select 2
        
        
        
        
        
        
        
        
        
        
        

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        $("#deal_agent_id").change(function (){
            $("#deal_property_id, #deal_lead_id").val("");
            $("#property_details_container, #contact_details_container").addClass("d-none");
            $("#deal_property_id").propertySelect2('#dealCreateModal',true,true,$(this).val(),'Select Property');
           // $("#deal_lead_id").getUserLeadSelect2('#dealCreateModal',true,true,'Select Lead',$(this).val(),true);
        });

        // Property Select2 ends here

        // Lead Select2 here
        $("#deal_lead_id").select2({
            placeholder: "Select Lead",
            allowClear: true,
            tags: true,
            closeOnSelect: false,
            dropdownParent: $('#dealCreateModal'),
            templateResult: formatLeadSelect2,
            templateSelection: formatLeadSelect2,
            ajax: {

                url: "<?php echo e(route('ajax.leads.select2')); ?>",
                dataType: 'json',
                type: 'GET',
                data: function (params) {
                    return {
                        search: params.term,
                        agent_id: $("#deal_agent_id").val(),
                    }
                },
                processResults({data}) {

                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: `${item.first_name || ""} ${item.last_name || ""}`,
                                id: item.id,
                                first_name: item.first_name,
                                last_name: item.last_name,
                                full_name: item.first_name + ' ' + item.last_name,
                                mobile: item.mobile,
                                email: item.email,
                            }
                        })
                    }
                }
            }
        });


        // Lead select2 ends here

        // On change of property showing contact details

        $('#deal_property_id').on('select2:select', function (e) {
            var data = e.params.data;
            let property = $(this).select2('data')[0] ?? e.params.data;

            if (property) {
                $("#property_reference_no").text(property.ref_no);
                $("#property_category").text(property.category);
                $("#property_unit_no").text(property.unit_no);
                $("#property_location").text(property.location);
                $("#contact_name").text(property.owner_name);
                $("#contact_email").text(property.owner_email);
                $("#contact_mobile").text(property.owner_mobile);
                $("#form_agent_id").val(property.agent_id);

                $("#property_details_container").removeClass("d-none");
                $("#contact_details_container").removeClass("d-none");
            } else {
                $("#property_details_container").addClass("d-none");
            }
        });
        $('#deal_property_id').on('select2:clear', function (e) {
            $("#property_details_container, #contact_details_container").addClass("d-none");
        });
        $('#deal_property_id').on('select2:unselect', function (e) {
            $("#property_details_container, #contact_details_container").addClass("d-none");
        });

        $(document).on('submit', '#dealInitForm', function (event) {
            $("#initSubmitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "<?php echo e(route('admin.work.deals.initStore')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#initSubmitBtn").prop('disabled', false); //enable submit button
                        $('#dealInitForm').trigger('reset'); //
                    let dealCreatePage = '<?php echo e(route('admin.work.deals.create',['id'=>':id'])); ?>';
                    //javascript write a code to redirect to deal create page
                        location.href = dealCreatePage.replace(':id',result.data.id);


                    } else if (result.status == '2') {
                        $("#initSubmitBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#initSubmitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $("#initSubmitBtn").prop('disabled', false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")

                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "success")
                    }
                },
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/deals/include/createModal.blade.php ENDPATH**/ ?>