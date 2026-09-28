<style>
    .hide {
        display: none;
    }

    .primary-bg {
        background: #c48667 !important;
    }

    .las-col:hover .hide {
        display: block;
        color: red;
    }

    .finRow {
        border-style: dashed !important;
        border: 1px #f1f1f1;
        padding: 7px;
    }

    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.1s ease;
    }

    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }

    #commissionCard.card-border-primary .card-footer,
    .card-border-primary .card-header {
        border-color: #c48667;
    }

    #commissionCard.card-border-primary {
        border-color: #c48667 !important;
    }

    .btn-ghost-info {
        border-color: transparent;
        color: #299cdb !important;
    }

    input[type="checkbox"] {
        background-color: #ffffff;
        border: 1px solid #a7a1a1;
    }

    input[type="checkbox"]:checked {
        background-color: #d3a188;
    }

</style>
<div class="row">
    <div class="col-11"></div>
    <div class="col-1 edit-action">
        <?php if(!auth()->user()->hasRole('Agent')): ?>
        <div class="btn-group float-end mb-2 d-none" id="deal-dropdown-btn-group">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class=" ri-more-2-fill"></i>
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="javascript:void(0)" onclick="showDealSplitModal()">
                    <i class=" ri-pie-chart-line"></i>
                    New Deal Split</a>
                <a class="dropdown-item d-none" href="javascript:void(0)" onclick="showDeductibleModal()">
                    <i class=" ri-pie-chart-line"></i>
                    Add Fee</a>
                <a class="dropdown-item d-none" onclick="showCommissionCard()" href="javascript:void(0)">
                    <i class="bx bx-dollar"></i>
                    Commissions</a>
                <a class="dropdown-item d-none" onclick="refreshFinancialData()" href="javascript:void(0)">
                    <i class="mdi mdi-refresh"></i>
                    Refresh Calculation</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<form id="dealInitialCommissionForm">
    <div class="row mt-4" id="PropertySelection">
        <div class="col-md-6">
            <table class="table table-bordered table-border-custom" id="financialTable1">

                <tbody>
                    <tr>
                        <td width="30%" class="tableThDark tdAdjust"><b>Listing Price</b></td>
                        <td width="70%" colspan="3"
                        class="p-0"
                        >
                        <div class="input-group" style="
                            padding: 4.2px;">
                            <label class="table-spacing" id="listing_price_label"></label>

                            <input type="hidden" class="form-control cleave " autocomplete="off" name="listing_price"
                                id="listing_price" onkeyup="calculateCommission()" readonly>
                        </div></td>
                    </tr>
                    <tr>
                        <td class="tableThDark title tdAdjust"><b>Deposit Method Of Payment</b></td>
                        <td colspan="3" class="tdInput p-0"
                        class="p-0"
                        ><span>
                            <select class="select2 deal-input-border" name="mode_of_payment">
                                <option value="cash">Cash</option>
                                <option value="cheque">Cheque</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </span></td>
                    </tr>

                    <tr>
                        <td width="30%" class="tableThDark tdAdjust"

                        ><b>Deposit Amount (AED)</b></td>
                        <td width="70%"
                          class="p-0"
                        > <div class="input-group"
                        >
                            <span class="deal-input-group-text d-none" id="inputGroup-sizing-default">AED</span>
                            <input
                            type="text" class="deal-form-control cleave" autocomplete="off" name="deposit_amount"
                                id="finance-deposit_amount">
                        </div></td>
                    </tr>


                    <tr class="salesProgressionBlur">
                        <td class="tableThDark tdAdjust"><b>Sales/Transaction Type </b></td>
                        <td colspan="3" class="salesprogression deal-form-control tdInput p-0">
                            <select id="finance-transaction_type" name="transaction_type"
                            class="deal-form-control select2"
                            name="sales_type" required>
                            <option value="">Select Transaction Type</option>
                            <?php $__currentLoopData = \App\Models\Deal::TRANSACTION_TYPE_SELECT; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        </td>
                    </tr>




                    <tr class="salesProgressionBlur">
                        <td class="tableThDark tdAdjust"><b> <label>Sales Progression
                            <span class="label-sales-prog-prop"></span>
                        </label>

                        <button type="button" data-bs-container="body" data-bs-placement="right" data-bs-toggle="tooltip"
                            data-bs-html="true"
                            title="
                        <b>Auto selected Sales Progression Person may be a</b>
                        <br>- Person with lowest sells
                        <br>- Person who hasn't sold recently"
                            style="border: 0px solid;border-radius: 13px;position: absolute;">
                            ?
                        </button></b></td>
                        <td colspan="3" class="salesprogression tdInput p-0">
                            <select id="finance-sales_progression" name="conveyancer_id"
                            class="deal-form-control sales-prog-prop">


                            </select>
                            <div class="invalid-feedback">Please Select Sales Progression Agent</div>
                        </td>
                    </tr>

                    <tr class="salesProgressionBlur">
                        <td class="tableThDark tdAdjust"><b>Progression Fee </b></td>
                        <td colspan="3" class="salesprogression p-0">
                            <div class="input-group">

                                <input type="text" id="finance-progression_amount" onkeyup="calculateCommission()"
                                    class="deal-form-control cleave" autocomplete="off" name="progression_amount">
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered table-border-custom">

                <tbody>

                    <tr>
                        <td class="tableThDark tdAdjust"

                        > <label><b><span class="label-deal_type"></span> Price</b></label></td>
                        <td   class="p-0">
                            <div class="input-group">

                                <input type="text" class="deal-form-control cleave" autocomplete="off" name="deal_price"
                                    id="finance-deal_price" onkeyup="calculateCommission()">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="tableThDark tdAdjust"

                        ><b>Commission </b></td>
                        <td
                        class="p-0"
                        >
                            <input type="hidden" class="form-control cleave" autocomplete="off" name="net_commission"
                            id="net_commission" readonly>
                            <div class="input-group">
                                <span class="input-group-text cursor-pointer d-none" id="commission_cal_sign_label">%</span>
                                <input type="hidden" name="commission_cal_sign" id="commission_cal_sign" value='percentage'>
                                <input type="text" class="form-control deal-input-border" autocomplete="off"
                                    name="commission_percentage"
                                    placeholder="Percentage"
                                    maxlength="100"
                                    onkeyup="calculateCommissionByPercentageV2()"
                                    id="commission_percentage">
                                    <span
                                    style="    position: absolute;
                                left: 108px;
                                margin-top: 8px;
                                font-weight: 400;"
                                    >%</span>
                                <input type="text" onkeyup="calculateCommissionByNumbersV2()"
                                    placeholder="Commission Amount"
                                    class="form-control cleave deal-input-border"
                                    style="width: 130px;"
                                    autocomplete="off" name="commission_amount" id="finance-commission_amount"
                                    onkeyup="calculateCommission()">
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td class="tableThDark tdAdjust"

                        ><b>Commission Amount</b></td>
                        <td   class="p-0">
                            <div class="input-group"
                            style="padding: 8px;"
                            >

                                <span class="table-spacing netIncomeLabel"></span>
                            </div>
                        </td>
                    </tr>


                    <tr>
                        <?php
                        $vat = fromSettings('Deal', 'deal-vat','0');
                    ?>
                        <td class="tableThDark tdAdjust"><b>VAT</b></td>
                        <td   class="p-0">
                            <div class="input-group"   style="padding: 8px;">
                                <span class="table-spacing"> <?php echo e(number_format($vat,2)); ?></span>
                                 <input type="hidden" id="finance-vat"
                                     class="form-control cleave" autocomplete="off" name="vat" value="<?php echo e($vat); ?>">
                             </div>
                        </td>
                    </tr>





                </tbody>
            </table>
            <div class="input-group d-none">
                <b> AED &nbsp; <span class="gross_commission" id="gross_commission">0</span>
                    <br>
                    <input type="hidden" name="gross_commission" id="financial-gross_commission">
                </b>
            </div>
            <span class="fs-11 float-end">
                * All amounts are in AED
            </span>
        </div>





    </div>

    <div class="row p-3">

    <div class="col-12">
        <div class="float-right float-end">
            <button type="button" id="dealInitCommissionBtn" class="btn btn-primary

            ">Save</button>
        </div>
    </div>
    </div>
</form>

<div class="d-none" id="commissionDetailSection">
<div class="row mt-2">
    <div class="col-12  p-1 ml-2" style="border-radius:10px;
        margin-left: 10px;
    ">
        <h4>Commission Internal</h4>
    </div>

</div>

</div>
<div id="splitList">

</div>
<div class="card">
    <div class="card-body <?php if(auth()->user()->hasRole('Agent')): ?> d-none <?php endif; ?>" id="userCommissionList">

    </div>
</div>
<div class="card d-none" id="referralSection">
    <div class="card-header">
        <h4 class="card-title">Referral</h4>
    </div>
    <div class="card-body" id="dealReferralList">

    </div>
</div>
<?php $__env->startPush('page-js'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.2/axios.min.js"></script>

    <script>
        const splits = [];
        const userCommissions = [];
        var totalCompanyEarning = 0;
        ///handle permission flag
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('See Company Earning')): ?>
            let can_see_company_earning = true;
        <?php else: ?>
            let can_see_company_earning = false;
        <?php endif; ?>
        let show_progression_data = false;
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Sales Progression Data')): ?>
            show_progression_data = true;
        <?php else: ?>
            show_progression_data = false;
        <?php endif; ?>

        const getParentUserUrl = "<?php echo e(route('admin.deals_revamped.get.parent.users')); ?>";
        const getFinancialDataUrl = "<?php echo e(route('admin.deals_revamped.get.financial.data')); ?>";
        const deductibles = <?php echo json_encode($deductible_types, 15, 512) ?>;
        var salesProgressionSelected = false;
    </script>
    <script src="<?php echo e(asset('assets/deal/js/financial.js')); ?>"></script>
    <script>
        $("#finance-sales_progression").select2({
            placeholder: "Select Sales Progression",
            allowClear: true
        });


        // call the function insided the onload function
        $(document).ready(function() {
            let dealid = deal.value.id || deal_id;
            getDealSplits(dealid)
            getDealReferrals(dealid)
            $("#deal_split_agent_id").select2({
                placeholder: "Select Agent",
                dropdownParent: $('#dealSplitModal'),
                allowClear: true
            });
            $("#split_commission_from").select2({
                placeholder: "Commission From",
                dropdownParent: $('#dealSplitModal'),
                allowClear: true
            });
            $("#deal_split_referral_agent_id").select2({
                placeholder: "Select Agent",
                dropdownParent: $('#dealSplitModal'),
                allowClear: true
            });

            getFinancialData(dealid)
            $(document).on('change', '#split_enable_referral', function() {
            if ($(this).is(":checked")) {
               $(".referral_enabled").removeClass('d-none');
            } else {
                $(".referral_enabled").addClass('d-none');
                $("#deal_split_referral_agent_id").val('').trigger('change')
                $("#deal_split_referral_amount").val('').trigger('change')
            }
        })
        })

        setTimeout(() => {
            if (deal.value) {

                $("#finance-deal_price").val(numberFormat(deal.value.deal_price))
                $("#net_commission").val(numberFormat(deal.value.net_commission) || 0)
                $(".netIncomeLabel").html(numberFormat(deal.value.net_commission) || 0)
                let property_listing_price = 0;
                if(deal.value?.deal_meta !=null){
                 let property_listing_price = deal.value.deal_meta.find(x => x.meta_key ==
                'listing_price')?.meta_value || 0;
                }
                if(property_listing_price > 0){
                   $("#listing_price_label").text(numberFormat(property_listing_price))
                }
            if(deal.value.deal_price > 0 && deal.value.net_commission > 0){
                let deal_percentage = deal.value.net_commission / (deal.value.deal_price / 100) ;
                deal_percentage = isNaN(deal_percentage) ? 0 : deal_percentage;
                $("#commission_percentage").val(deal_percentage.toFixed(2))
            }
                $("#finance-deposit_amount").val(numberFormat(deal.value.initial_deposit) || 0)
                //$(".total_vat").html('')
                let gross_commission = isNaN(deal.value.gross_commission) ? 0 : deal.value.gross_commission;
                $(".gross_commission").html(numberFormat(gross_commission) || 0)
                $(".status_gross_commission").html(numberFormat(deal.value.gross_commission))
                $(".status_deal_price").html(numberFormat(deal.value.deal_price))
                $(".status_net_commission").html(numberFormat(deal.value.net_commission))
                let deal_meta = deal.value.deal_meta;
                var listing_price;
                if (deal_meta != undefined && deal_meta != null && deal_meta != '') {


                    var listingPriceItem = deal_meta.find(item => item.meta_key === "listing_price");
                    if (listingPriceItem) {
                        listing_price = listingPriceItem.meta_value;
                        //  $(".property-listing-price").html(numberFormat(listing_price))
                        // $("#finance-listing_price").val(numberFormat(listing_price))
                        $("#listing_price").val(listing_price)
                        if(listing_price > 0){
                        $("#listing_price_label").text(numberFormat(listing_price))
                        }
                        if(deal.value.net_commission == 0 || deal.value.net_commission == '0' || deal.value.net_commission == null){
                          $("#deal-dropdown-btn-group").addClass('d-none');
                          $("#commissionDetailSection").addClass('d-none');

                        }else{
                            $("#deal-dropdown-btn-group").removeClass('d-none');
                            $("#commissionDetailSection").removeClass('d-none');
                        }
                        // getDealDeductibles(deal_id);
                    } else {
                        console.log("Listing price not found in deal_meta");
                    }
                }
            }
        }, 2500);
        $(document).on('click', '#dealInitCommissionBtn', function(event) {
            $("#dealInitCommissionBtn").prop('disabled', true);
            //event.preventDefault();
            if($("#net_commission").val() == 0 || $("#net_commission").val() == '0' || $("#net_commission").val() == null){
                showToastify('Commission amount is required', "error")
                $("#dealInitCommissionBtn").prop('disabled', false);
                return '';
            }
            if($("#commission_percentage").val() == 0 || $("#commission_percentage").val() == '0' || $("#commission_percentage").val() == null){
                showToastify('Commission Percentage is required', "error")
                $("#dealInitCommissionBtn").prop('disabled', false);
                return '';
            }
            var formData = {
                deal_id: deal_id,
                deal_price: $("#finance-deal_price").val(),
                commission_percentage: $("#commission_percentage").val(),
                commission_amount: $("#finance-commission_amount").val(),
                seller_fee: $("input[name='seller_fee']").val(),
                buyer_fee: $("input[name='buyer_fee']").val(),
                sales_type: $("select[name='sales_type']").val(),

                net_commission: $("#net_commission").val(),
                gross_commission: $("#financial-gross_commission").val(),
                mode_of_payment: $("select[name='mode_of_payment']").val(),
                deposit_amount: $("#finance-deposit_amount").val(),
                transaction_type: $("#finance-transaction_type").val(),
                conveyancer_id: $("#finance-sales_progression").val(),
                progression_fee: $("#finance-progression_amount").val(),
                vat: $("#finance-vat").val(),
            };



            //remove comma from split amount
            //  let split_amount = formData.get('split_amount');

            // split_amount = String(split_amount).replace(/,/g, '');

            // formData.set('amount', split_amount);

            $.ajax({
                url: "<?php echo e(route('admin.deals_revamped.save.deal.commission')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(result) {
                    if (result.status == '1') {
                        // getDealSplits(deal_id, true);
                        let dealid = deal.value.id || deal_id;
                        userCommissions.length = 0;
             totalCompanyEarning = 0;

                        getDealSplits(deal_id);
                        getDealReferrals(dealid)
                        showToastify(result.msg, "success")
                        $('#dealSplitCommissionModal').modal('hide'); //hide form modal
                        $("#dealInitCommissionBtn").prop('disabled', false); //enable submit button
                        $("#deal-dropdown-btn-group").removeClass('d-none');
                        $("#commissionDetailSection").removeClass('d-none');
                    } else if (result.status == '2') {
                        $("#dealInitCommissionBtn").prop('disabled', false);
                        $.each(result.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    } else {
                        $("#dealInitCommissionBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log('error')
                    $("#dealInitCommissionBtn").prop('disabled', false);
                    console.log(jqXhr)
                    let data = jqXhr.responseJSON;
                    $("#splitCommissionFormSubmitBtn").prop('disabled', false);
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });
        $(document).on("submit", "#splitForm", function(event) {
            event.preventDefault();
            $("#splitFormSubmitBtn").prop('disabled', true);
            var formData = new FormData($(this)[0]);
            //formData remove the input filed deal_id
            formData.delete('deal_id');
            formData.append('deal_id', deal.value.id);
            axios.post("<?php echo e(route('admin.deals_revamped.split.save')); ?>", formData)
                .then(response => {

                    if (response.data.status === '1') {
                        isValidSubmission = true;
                        getDealSplits(deal_id);
                        getDealReferrals(deal_id)
                        showToastify(response.data.msg, "success");
                        $("#dealSplitModal").modal('hide');
                        $("#splitFormSubmitBtn").prop('disabled', false);

                    } else {
                        $("#splitFormSubmitBtn").prop('disabled', false);

                        showToastify(response.data.msg, "error");
                    }
                })
                .catch(error => {
                    $("#splitFormSubmitBtn").prop('disabled', false);

                    showToastify("Something Went Wrong!", "error");
                    console.error(error);
                });
        });

        $(document).on('change', '#finance-transaction_type', function() {
            let selected_value = $(this).val();

            //find it on deductibles
            let deductible = deductibles.find(x => x.name == selected_value);
            // if (deductible == undefined || deductible == null) {
            //     $("#finance-progression_amount").val(0);
            //     return '';
            // } else {
            //     $("#finance-progression_amount").val(numberFormat(deductible?.type_value)).trigger('keyup');
            //     $("#finance-progression_amount_label").html(`(` + numberFormatAX(deductible?.type_value) + `)`);
            // }
            getSalesProgressions();
        });
        $('#finance-transaction_type').on('select2:clear', function(e) {
            $("#finance-sales_progression").val('').trigger('change');
            $("#finance-progression_amount_label").html('');
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/deals_revamped/partials/_financial.blade.php ENDPATH**/ ?>