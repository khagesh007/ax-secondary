

<div class="text-end">
    <div id="btn-edit-contact-mode" class=" btn <?php echo e($edit_btn); ?>  btn-outline-secondary  d-none w-20 edit-action">
        <i class="mdi mdi-pencil"></i> Edit
    </div>
</div>


<div class="row mt-3">
    <input type="hidden" id="contact-id" name="contact-id">
    <div class="col-12 d-flex justify-content-center">
        <div class="row">
            <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check contact-disable-on-view-mode" checked name="meta-type"
                    id="meta-type-1" value="INDIVIDUAL" required>
                <label class="btn btn-outline-secondary mb-0" for="meta-type-1">Individual</label>

                <input type="radio" class="btn-check contact-disable-on-view-mode" name="meta-type" id="meta-type-2"
                    value="COMPANY" required>
                <label class="btn btn-outline-secondary mb-0" for="meta-type-2"
                    style="border-color: #c48667;
    color: #c48667;">Company</label>
            </div>
        </div>
    </div>
</div>
<div class="row d-none" id="company-dependant">
    <div class="col-12">
        <h5 class="mt-2">Company Information</h5>
    </div>
    <div class="col-6">
        <table class="table table-bordered table-border-custom">

            <tbody>
                <tr>
                    <td width="30%" class="tableThDark tdAdjust">Company Name *</td>
                    <td width="70%" colspan="3"
                    class="p-0"
                    >
                        <input type="text" class="deal-form-control inputs company-dependant-fields contact-disable-on-view-mode"
                        name="contact-company" id="contact-company" placeholder="Enter company name">
                    <div class="invalid-feedback">
                        Please Enter Company Name
                    </div>

                    </td>
                </tr>
                <tr id="contact-tradeExpiryDiv1">
                    <td width="30%" class="tableThDark tdAdjust">Trade License Expiry *</td>
                    <td width="70%" colspan="3"
                    class="p-0"
                    >
                        <input type="date"
                        class="deal-form-control inputs company-dependant-fields company_not_mandatory_field contact-disable-on-view-mode"
                        id="meta-trade_license_expiry" name="meta-trade_license_expiry"
                        placeholder="Enter trade license expiry">

                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    <div class="col-6">
        <table class="table table-bordered table-border-custom">

            <tbody>
                <tr>
                    <td width="30%" class="tableThDark tdAdjust">Trade Name *</td>
                    <td width="70%" colspan="3"
                    class="p-0"
                    >
                        <input type="text" class="deal-form-control inputs company-dependant-fields contact-disable-on-view-mode"
                        name="meta-trade_license_name" id="meta-trade_license_name" placeholder="Enter trade name">
                    <div class="invalid-feedback">
                        Please Enter Trade License.
                    </div>

                    </td>
                </tr>
                <tr id="contact-tradeNoDiv1">
                    <td width="30%" class="tableThDark tdAdjust">Trade License No *</td>
                    <td width="70%" colspan="3"
                    class="p-0"
                    >
                        <input type="text"
                        class="deal-form-control inputs company-dependant-fields company_not_mandatory_field contact-disable-on-view-mode"
                        id="meta-trade_license_no" name="meta-trade_license_no" placeholder="Enter trade license no">

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row ">
    <div class="col-12">
         <h5 class="mt-2" id="contact-information-title">Personal Information</h5>
    </div>
    <input type="text" hidden id="meta-contact_mode" name="meta-contact_mode" value="view">
    <div class="col-6">
    <table class="table table-bordered table-border-custom">

        <tbody>
            <tr>
                <td width="30%" class="tableThDark tdAdjust">First Name *</td>
                <td width="70%" colspan="3"
                class="p-0"
                >
                    <input type="text" class="deal-form-control inputs contact-disable-on-view-mode" name="contact-first_name"
                    id="contact-first_name" placeholder="Enter first name" required>
                <div class="invalid-feedback">
                    Please enter first name.
                </div>

                </td>
            </tr>
            <tr>
                <td width="30%" class="tableThDark tdAdjust">Last Name *</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <input type="text" class="deal-form-control inputs contact-disable-on-view-mode" name="contact-last_name"
                id="contact-last_name" placeholder="Enter last name" required>
            <div class="invalid-feedback">
                Please enter last name.
            </div>
                </td>
            </tr>
            <tr id="contact-mobileDiv1" class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Mobile *</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <input type="tel" class="deal-form-control inputs contact-disable-on-view-mode" name="contact-mobile"
                    id="contact-mobile" placeholder="Enter mobile number" required>

                <div class="text-danger contactMobileValidation">

                </div>
                </td>
            </tr>
            <tr id="contact-whatsappDiv1" class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Whatsapp *</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <input type="text" class="deal-form-control inputs contact-disable-on-view-mode"
                    name="contact-whatsapp" id="contact-whatsapp" placeholder="Enter whatsapp number" required>
                <div class="text-danger contactWhatsappValidation">

                </div>
                </td>
            </tr>
            <tr class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Email *</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <input type="email" class="deal-form-control inputs contact-disable-on-view-mode  mb-1"
                    name="contact-email" id="contact-email" placeholder="Enter email address" required>
                <div class="invalid-feedback">
                    Please enter a valid email address.
                </div>
                </td>
            </tr>
            <tr class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Gender *</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <select class="form-control select2 inputs contact-disable-on-view-mode" name="contact-gender"
                    id="contact-gender" required>
                    <option value="" selected disabled>Select gender
                    </option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <div class="invalid-feedback">
                    Please choose gender.
                </div>
                </td>
            </tr>

            <tr class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Phone </td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <input type="text" class="deal-form-control inputs contact-disable-on-view-mode checkValidity"
                       name="contact-phone" id="contact-phone" placeholder="Enter phone number">
                <div class="text-danger contactPhoneValidation">

                </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="col-6">
    <table class="table table-bordered table-border-custom">

        <tbody>
            <tr class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Passport Number *</td>
                <td width="70%" colspan="3"
                class="p-0"
                >
                    <input type="text" class="deal-form-control inputs contact-disable-on-view-mode"
                       name="contact-passport" id="contact-passport" placeholder="Enter passport" required>
                <div class="invalid-feedback">
                    Please enter a valid passport number.
                </div>

                </td>
            </tr>
            <tr class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Passport Issue Date</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <input type="date" class="deal-form-control inputs contact-disable-on-view-mode"
                id="meta-passport_issue_date" name="meta-passport_issue_date"
                placeholder="Enter passport issue date">
         <div class="invalid-feedback">
             Please enter passport issue date.
         </div>
                </td>
            </tr>
            <tr class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Passport Expiry</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <input type="date" class="deal-form-control inputs contact-disable-on-view-mode"
                       id="contact-passport_expiry" name="contact-passport_expiry"
                       placeholder="Enter passport expiry date">

                <div class="invalid-feedback">
                    Please enter passport expiry date.
                </div>
                </td>
            </tr>
            <tr class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Residential Address</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <input type="text" class="deal-form-control inputs contact-disable-on-view-mode" name="contact-address"
                       id="contact-address" placeholder="Enter address">
                <div class="invalid-feedback">
                    Please enter address.
                </div>
                </td>
            </tr>
            <tr class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Date Of Birth</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <input type="date" class="deal-form-control inputs contact-disable-on-view-mode" id="contact-dob"
                name="contact-dob" placeholder="Enter date of birth">
            <div class="invalid-feedback">
                Please enter date of birth.
            </div>
                </td>
            </tr>
            <tr class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Place Of Birth</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                        <select class="form-control select2 inputs contact-disable-on-view-mode" name="meta-place_of_birth"
                        id="meta-place_of_birth">
                        <option value="" selected disabled>Select country
                        </option>
                        <?php $__currentLoopData = $countries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($country->name); ?>">
                                <?php echo e($country->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="invalid-feedback">
                        Please enter place of birth.
                    </div>
                </td>
            </tr>
            <tr class="externalAgentHide">
                <td width="30%" class="tableThDark tdAdjust">Nationality *</td>
                <td width="70%" colspan="3"
                class="tdInput p-0"
                style="max-width:200px;"
                >
                <select class="form-control select2 inputs contact-disable-on-view-mode" name="contact-nationality_id"
                id="contact-nationality_id" required>
                <option value="" selected disabled>Select nationality
                </option>
                <?php $__currentLoopData = $countries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($country->id); ?>">
                        <?php echo e($country->nationality); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="invalid-feedback">
                Please choose nationality.
            </div>
                </td>
            </tr>
            <tr class="externalAgentHide ifCompany d-none">
                <td width="30%" class="tableThDark tdAdjust">Relationship *</td>
                <td width="70%" class="tdInput p-0">
                    <input type="text" class="deal-form-control inputs company-dependant-fields contact-disable-on-view-mode" name="meta-relationship"
                       id="meta-relationship" placeholder="Enter relation with company" required>
                    <div class="invalid-feedback">
                        Please enter relation with company.
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</div>




<?php $__env->startPush('page-js'); ?>
    <script>
        var contact_step = false;
        // Set date max today
        $(document).ready(function() {
            setDateMaxToday('contact-dob');
            setDateMaxToday('meta-passport_issue_date');
        });

        // For View And Edit Mode (Contact)
        var contact_mode = "edit";

        $('#btn-edit-contact-mode').click(function() {
            if (contact_mode == "edit") {
                contact_mode = "view";
                $('#btn-edit-contact-mode').html('<i class="mdi mdi-pencil"></i> Edit');
                contactFieldsDisabled(true);
            } else {
                contact_mode = "edit";
                $('#btn-edit-contact-mode').html('<i class="mdi mdi-eye"></i> View');
                contactFieldsDisabled(false);
            }
        })

        function contactFieldsDisabled(disbaled) {


            $(".contact-disable-on-view-mode").prop('disabled', disbaled);



        }

        // For View And Edit Mode (Contact)


        // contact-mobile
        let contactMobileInput = initPhoneValidator('contact-mobile')
        var contactMobileInputElement = document.querySelector("#contact-mobile");
        var contactMobileIti = window.intlTelInput(contactMobileInputElement, {
            initialCountry: "ae",
        });
        contactMobileIti.getNumber()

        contactMobileInputElement.addEventListener("countrychange", function() {
            var selectedCountryData = contactMobileIti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            var countryFlag = selectedCountryData.iso2;


            if (contactMobileIti.isValidNumber()) {
                $(".contactMobileValidation").html("");
                $('input[name="contact-mobile"]').val(contactMobileIti.getNumber());
            } else {
                $(".contactMobileValidation").html("Please enter a valid mobile number with country code");
            }
        });

        $(document).on('change', '#contact-mobile', function() {

            if (contactMobileInput.isValidNumber() == null) {
                return false;
            }
            if (contactMobileInput.isValidNumber()) {
                $('input[name="contact-mobile"]').val(contactMobileIti.getNumber());
                $(".contactMobileValidation").html("");
            } else {
                $(".contactMobileValidation").html("Please enter a valid mobile number with country code");
            }
        })

        // contact-whatsapp
        let contactWhatsappInput = initPhoneValidator('contact-whatsapp')
        var contactWhatsappInputElement = document.querySelector("#contact-whatsapp");
        var contactWhatsappIti = window.intlTelInput(contactWhatsappInputElement, {
            initialCountry: "ae",
        });

        contactWhatsappInputElement.addEventListener("countrychange", function() {
            var selectedCountryData = contactWhatsappIti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            var countryFlag = selectedCountryData.iso2;


            if (contactWhatsappIti.isValidNumber()) {
                $(".contactWhatsappValidation").html("");
                $('input[name="contact-whatsapp"]').val(contactWhatsappIti.getNumber());
            } else {
                $(".contactWhatsappValidation").html("Please enter a valid Whatsapp number with country code");
            }
        });

        $(document).on('change', '#contact-whatsapp', function() {



            if (contactWhatsappIti.getNumber() == "") {
                $(".contactWhatsappValidation").html("");
                return false;
            }
            if (contactWhatsappInput.isValidNumber()) {
                $('input[name="contact-whatsapp"]').val(contactWhatsappIti.getNumber());
                $(".contactWhatsappValidation").html("");
            } else {
                $(".contactWhatsappValidation").html("Please enter a valid Whatsapp number with country code");
            }
        })

        // contact-phone
        let contactPhoneInput = initPhoneValidator('contact-phone')
        var contactPhoneInputElement = document.querySelector("#contact-phone");
        // var contactPhoneIti = window.intlTelInput(contactPhoneInputElement, {
        //     initialCountry: "ae",
        // });

        contactPhoneInputElement.addEventListener("countrychange", function() {
            var selectedCountryData = contactPhoneInput.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            var countryFlag = selectedCountryData.iso2;

            if (contactPhoneInput.isValidNumber()) {
                $('input[name="contact-phone"]').val(contactPhoneInput.getNumber());
                $(".contactPhoneValidation").html("");
            } else {
                $(".contactPhoneValidation").html("Please enter a valid Phone number with country code");
            }
        });

        $(document).on('change', '#contact-phone', function() {

            if (contactWhatsappIti.getNumber() == "") {
                $(".contactPhoneValidation").html("");
                return false;
            }

            if (contactPhoneInput.isValidNumber()) {
                $('input[name="contact-phone"]').val(contactPhoneInput.getNumber());
                $(".contactPhoneValidation").html("");
            } else {
                $(".contactPhoneValidation").html("Please enter a valid Phone number with country code");
            }
        })



        function checkStepCompletion(data) {
            if(data.contact_meta == null || data.contact_meta == undefined || data.contact_meta.length == 0){
                return false;
            }
          //  let type = data.contact_meta.find(x => x.meta_key == 'type').meta_value ?? 'INDIVIDUAL';
            let type = (data.contact_meta.find(x => x.meta_key == 'type')?.meta_value) ?? 'INDIVIDUAL';

            let place_of_birth = (data.contact_meta.find(x => x.meta_key === 'pob')?.meta_value) ?? '';

            contact_step = !(
                isNullOrEmpty(data.first_name) ||
                isNullOrEmpty(data.last_name) ||
                isNullOrEmpty(data.mobile) ||
                isNullOrEmpty(data.email) ||
                isNullOrEmpty(data.gender) ||
                isNullOrEmpty(data.nationality_id) ||
                isNullOrEmpty(data.address) ||
                isNullOrEmpty(data.dob) ||
                isNullOrEmpty(place_of_birth) ||
                isNullOrEmpty(data.passport)
                );

            if (type === "COMPANY") {
                let trade_license_name = data.contact_meta.find(x => x.meta_key == 'trade_license_name').meta_value;
                contact_step = !(
                    isNullOrEmpty(data.company) ||
                    isNullOrEmpty(trade_license_name)
                );
            }

            console.log("CONTACT COMPANY STEP 2 STATUS: "+contact_step);
        }



        // Get the contact information
        function getContactInformation(id) {
            // Jquery ajax get request to fetch contact information from get-owner route
            if (id) {
                $.ajax({
                    url: "<?php echo e(route('admin.work.deals.get.contact')); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        checkStepCompletion(result.data)


                        $(".inputs").val('').trigger("change");
                        Object.keys(result.data).map((index) => {

                            if (index == 'dob' && result.data[index] != null && result.data[index] !=
                                '') {

                                let dob = result.data[index].split("-");
                                let new_dob = dob[2] + "-" + dob[1] + "-" + dob[0];
                                $(`#contact-${index}`).val(new_dob).trigger("change");

                            } else {
                                $(`#contact-${index}`).val(result.data[index]).trigger("change");
                            }
                            if (index == "contact_meta") {
                                let contactMeta = result.data[index];
                                Object.keys(contactMeta).map((index) => {
                                    let key = "meta_key";
                                    let val = "meta_value";

                                    // For View And Edit Mode (Contact)
                                    if (contactMeta[index][key] == "contact_mode") {
                                        contact_mode = "view";
                                        $("#btn-edit-contact-mode").removeClass("d-none");
                                        $("#btn-edit-contact-mode").html(
                                            '<i class="mdi mdi-pencil"></i> Edit');
                                        contactFieldsDisabled(true);
                                    }
                                    // For View And Edit Mode (Contact)

                                    $(`#meta-${contactMeta[index][key]}`).val(contactMeta[index]
                                        [val]).trigger("change");

                                    // Checkboxes
                                    let checkboxGroups = {
                                        "type": "meta-type",
                                        "uae_national": "meta-uae_national",
                                        "uae_resident": "meta-uae_resident"
                                        // Add more checkbox groups as needed
                                    };

                                    Object.keys(checkboxGroups).forEach((groupKey) => {
                                        if (contactMeta[index][key] === groupKey) {
                                            $(`input[name='${checkboxGroups[groupKey]}'][value='${contactMeta[index][val]}']`)
                                                .prop("checked", true).trigger(
                                                    "change");
                                        }
                                    });
                                });

                            }
                        });
                    },
                    error: function(result) {
                        showToastify(result.message, "error");
                    }
                });
            }

        }




        $(document).on("change", "input[name='meta-type']", function(event) {
            event.preventDefault();
            let type = $(this).val();
            if (type == "COMPANY") {
                $("#company-dependant").removeClass("d-none");
                $(".ifCompany").removeClass("d-none");

                // Make company-dependant-fields required
                $(".company-dependant-fields").attr("required", true);
                $('.company_not_mandatory_field').attr('required', false);
            $("#contact-information-title").text("Representative Person's Information")
            } else {
                $(".ifCompany").addClass("d-none");

                $("#company-dependant").addClass("d-none");
                $(".company-dependant-fields").attr("required", false);
                $("#contact-information-title").text('Personal Information')
            }
        });
        $(document).on("change", "input[name='meta-uae_national']", function(event) {
            event.preventDefault();
            let uaeNational = $(this).val();
            if (uaeNational == "NO") {
                $(".uae_national_dependant").removeClass("d-none");
                // Make company-dependant-fields required
                $(".uae_national_dependant_fields").attr("required", true);

            } else {
                $(".uae_national_dependant").addClass("d-none");
                $(".uae_national_dependant_fields").attr("required", false);
                $("input[name='meta-uae_resident']").prop('checked', false);
                $("input[name='meta-uae_resident']").attr('required', false);
            }
        });



        // Submit the form
        $(document).on("submit", ".dealContactForm", function(event) {
            console.log('submitting contact form');
            event.preventDefault();
            isValidSubmission = false;
            let id = $("#contact-id").val();
            let formData = new FormData($(this)[0]);
            let modifiedData = {};
            modifiedData.contact_meta = {};
            // Iterate through each pair in the original FormData
            formData.forEach((value, key) => {
                // Remove hyphens and "contact-" prefix from the key
                let modifiedKey = key.replace(/^meta-/, '').replace(/-/g, '');

                // Check if the key has "meta-" prefix
                if (key.startsWith('meta-')) {
                    // Add the key without "meta-" prefix to the contact_meta object
                    modifiedData.contact_meta[modifiedKey] = value;
                } else {
                    // Add the key without "contact-" prefix to the modifiedData object
                    modifiedData[modifiedKey.replace(/^contact/, '')] = value;
                }
            });

            // For View And Edit Mode (Contact)
            if (contact_mode == "edit") {
                // For View And Edit Mode (Contact)
                $.ajax({
                    url: "<?php echo e(route('admin.work.deals.updateContact')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: modifiedData,
                    cache: false,
                    success: function(result) {

                        if (result.status == 1) {
                            contact_step = true;
                            showToastify(result.message, "success");
                            getContactInformation(id);

                        } else {
                            showToastify(result.message, "error");
                        }

                        getLeads();
                        getDealLeadds(deal_id);
                        isValidSubmission = true;
                        //$("#smartwizard").smartWizard("next");
                        if (nextStep) {
                            $('#smartwizard').smartWizard("goToStep", nextStep);
                        }
                        //
                        isValidSubmission = false;
                        //$('#screen_loader').addClass('d-none');

                    },
                    error: function(jqXhr, json, errorThrown) {

                        if (jqXhr.responseJSON.errors) {
                            $.each(jqXhr.responseJSON.errors, function(key, value) {
                                showToastify(value, "error");
                            });
                        } else {
                            console.log(jqXhr.responseJSON.message);
                        }

                        isValidSubmission = false;
                    },
                });

                //  For View And Edit Mode (Contact)
            } else {
                isValidSubmission = true;
                if (nextStep) {
                    $('#smartwizard').smartWizard("goToStep", nextStep);
                }
                isValidSubmission = false;
            }
            // For View And Edit Mode (Contact)



        });

        // flatpickr('#meta-trade_license_expiry', {
        //     "maxDate": new Date(),
        //     allowInput: true,
        //     enableTime: false,
        //     dateFormat: datePickerFormat()
        // });

        // flatpickr('#contact-dob', {
        //     "maxDate": new Date(),
        //     allowInput: true,
        //     enableTime: false,
        //     dateFormat: datePickerFormat()
        // });

        // flatpickr('#meta-passport_issue_date', {
        //     "maxDate": new Date(),
        //     allowInput: true,
        //     enableTime: false,
        //     dateFormat: datePickerFormat()
        // });

        // flatpickr('#contact-passport_expiry', {
        //     "maxDate": new Date(),
        //     allowInput: true,
        //     enableTime: false,
        //     dateFormat: datePickerFormat()
        // });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/deals_revamped/partials/_contact.blade.php ENDPATH**/ ?>