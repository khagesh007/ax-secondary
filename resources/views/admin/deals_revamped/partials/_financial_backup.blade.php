<style>
    .hide {
        display: none;
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

    #commissionCard.card-border-primary .card-footer, .card-border-primary .card-header {
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
<div class="col">

    <div class="d-none" id="commissionCard">
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-sm btn-outline-primary float-end"
                        onclick="closeCommissionCard()">
                    <i class="mdi mdi-close"></i>
                    Close
                </button>
            </div>
        </div>
        <div class="card mt-1" style="border: 1px solid #c48667;">
            <div class="card-header">
                <input type="hidden" name="deal_id" id="commission_form_deal_id">
            <div class="row">
                <div class="col-6">
                    <div class="row">
                        <div class="col-4">
                            <label>Listing Price</label>
                        </div>
                        <div class="col-8">
                            <div class="input-group property-listing-price">


                            </div>
                        </div>
                        <div class="col-4">
                            <label><span class="label-deal_type"></span> Price</label>
                        </div>
                        <div class="col-5">
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroup-sizing-default">AED</span>
                                <input type="text" class="form-control cleave" autocomplete="off"
                                       name="deal_price" id="finance-deal_price" onkeyup="calculateCommission()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 sales-progression-section" id="sales-progression-section">
                    <div class="row">
                        <div class="col-4">
                            <label class=""> Transaction Type
                            <span class="label-sales-prog-prop"></span>
                            </label>

                        </div>
                        <div class="col-8">
                            <select id="finance-transaction_type" name="transaction_type" class="form-control sales-prog-prop">
                                <option value="">Select Transaction Type</option>
                                @foreach(\App\Models\Deal::TRANSACTION_TYPE_SELECT as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach

                            </select>
                            <div class="invalid-feedback">Please Select Transaction Type</div>
                        </div>

                        <div class="col-4 mt-2">
                            <label>Sales Progression
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
                        </div>
                        <div class="col-8 mt-2">
                            <select id="finance-sales_progression" name="conveyancer_id" class="form-control sales-prog-prop">


                            </select>
                            <div class="invalid-feedback">Please Select Sales Progression Agent</div>
                        </div>
                        <div class=" col-4 mt-2 d-none">
                            <label>Sales Progression Fee</label>

                        </div>
                        <div class="col-8 mt-2 d-none">

                            <div class="input-group">
                                <span class="input-group-text">AED</span>
                                <input type="text" class="form-control cleave" autocomplete="off"
                                       name="progression_amount" id="finance-progression_amount"
                                       placeholder="Enter progression fee">
                            </div>


                        </div>
                    </div>
                </div>
            </div>



            </div>


            <td class="card-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Source</th>
                        <th>Amount</th>
                        <th>Percentage</th>
                        <th style="min-width: 110px;">VAT</th>
                        <th class="sales-progression-section">Progression Fee <span id="finance-progression_amount_label"></span></th>
                        <th>Sub Total</th>
                        <th><i class=" ri-settings-3-line"></i> </th>
                    </tr>
                    </thead>
                    <tbody id="commissionList">


                    </tbody>

                </table>
                <div class="row mt-2">

                    <div class="col-2">

                        <button type="button" style="margin-left: 10px;" class="btn btn-soft-success btn-sm" onclick="addCommissionRow()">
                            <i class="ri-add-line align-bottom"></i>
                        </button>

                    </div>
                    <div class="col-10 text-danger text-right" >
                <span class="text-right float-end" id="error_message">
                </span>
                    </div>
                </div>
                <hr>
                <div class="row mt-2">

                    <div class="offset-8 col-2">
                        <b>  Net commission</b>
                    </div>
                    <div class="col-2">
                        <b> <div class="input-group">
                                AED &nbsp; <span class="net_commission">0</span>
                                <input type="hidden" name="net_commission" id="financial-net_commission">
                            </div></b>
                    </div>
                </div>
                <div class="row mt-2">

                    <div class="offset-8 col-2">
                        <b> Total VAT</b>
                    </div>
                    <div class="col-2">
                        <b> <div class="input-group">
                            AED &nbsp; <span class="total_vat"></span>
                            <input type="hidden" name="total_vat" id="financial-total_vat">
                            </div></b>
                    </div>
                </div>
                <div class="row mt-2 sales-progression-section">

                    <div class="offset-8 col-2">
                        <b> Total Progression Fee</b>
                    </div>
                    <div class="col-2">
                        <b> <div class="input-group">
                            AED &nbsp; <span class="total_progression_fee"></span>
                            <input type="hidden" name="total_progression_fee" id="financial-total_progression_fee">
                        </div>
                        </b>
                    </div>
                </div>

                <hr>

                <div class="row mt-2 mb-2">
                    <div class="offset-8 col-2">
                       <b> Gross Commission</b>
                    </div>
                    <div class="col-2">
                        <div class="input-group">
                            <b>  AED &nbsp; <span class="gross_commission" id="gross_commission">0</span>
                            <br>
                            <input type="hidden" name="gross_commission" id="financial-gross_commission">
                            </b>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4">
                        <button type="submit" id="finanical-commission_saveBtn" class="btn btn-success action-save float-end"><span>Save</span></button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div id="notCommissionCard">
        <div class="row mb-2">

            <div class="col-4"><h4>Commissions</h4></div>
            <div class="col-4">

            </div>
            <div class="col-3 deal_primary_agent_card">

            </div>
            <div class="col-1 edit-action">


                <div class="btn-group float-end " id="deal-dropdown-btn-group">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        <i class=" ri-more-2-fill"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:void(0)" onclick="showDealSplitModal()">
                            <i class=" ri-pie-chart-line"></i>
                            New Deal Split</a>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="showDeductibleModal()">
                            <i class=" ri-pie-chart-line"></i>
                            Add Fee</a>
                        <a class="dropdown-item" onclick="showCommissionCard()" href="javascript:void(0)">
                            <i class="bx bx-dollar"></i>
                            Commissions</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" onclick="refreshFinancialData()" href="javascript:void(0)">
                            <i class="mdi mdi-refresh"></i>
                            Refresh Calculation</a>
                    </div>
                </div><!-- /btn-group -->
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="row row-cols-md-3 row-cols-1">
                    <div class="col col-lg border-end">
                        <div class="py-4 px-3">
                            <h5 class="text-muted text-uppercase fs-13">Deal Price <i
                                    class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                            </h5>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="ri-space-ship-line display-6 text-muted"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-0">AED <span class="counter-value status_deal_price">0</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->
                    <div class="col col-lg border-end">
                        <div class="mt-3 mt-md-0 py-4 px-3">
                            <h5 class="text-muted text-uppercase fs-13">Gross Commission <i
                                    class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                            </h5>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-0">AED <span class="counter-value status_gross_commission"
                                                               data-target="">0</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->

                    <div class="col col-lg border-end">
                        <div class="mt-3 mt-lg-0 py-4 px-3">
                            <h5 class="text-muted text-uppercase fs-13">
                                Net Commission <i
                                    class="ri-arrow-down-circle-line text-success fs-18 float-end align-middle"></i>
                            </h5>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class=" ri-bank-card-2-line display-6 text-muted"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-0">AED <span class="counter-value status_net_commission"
                                                               data-target="0">0</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->
                    @can('See Company Earning')
                    <div class="col col-lg">
                        <div class="mt-3 mt-lg-0 py-4 px-3">
                            <h5 class="text-muted text-uppercase fs-13">
                                Company Income <i
                                    class="ri-arrow-down-circle-line text-success fs-18 float-end align-middle"></i>
                            </h5>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class=" ri-wallet-3-line display-6 text-muted"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-0">AED <span class="counter-value company-income"
                                                               data-target="0">0</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->
                        @endcan
                </div>
            </div>
        </div>
        <div>
            @can('Sales Progression Data')
            <div class="card sales-progression-section  @if(auth()->user()->hasRole('Agent')) d-none @endif">
                <div class="card-header">
                    <div class="row mb-2">
                        <div class="col-10">
                            <h5>Progression Fee</h5>

                        </div>
                        <div class="col-2">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-2 mb-2">

                        <div class="col-md-12 col-lg-12" id="progression_fee_agent_card">

                        </div>


                    </div>
                </div>
            </div>
            @endcan
            <div class="card">
                <div class="card-header">
                    <div class="row mb-2">
                        <div class="col-10">
                            <h5>Fee</h5>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-sm btn-outline-primary float-end edit-action"
                                    onclick="showDeductibleModal()">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-2 mb-2">

                        <div class="col-md-12 col-lg-12" id="deductibleList">

                        </div>


                    </div>
                </div>
            </div>
            <div id="splitList">

            </div>
        </div>


    </div>
</div>


@push('page-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.2/axios.min.js"></script>
    <script>
        const deductibles = @json($deductible_types);
        const commissions_constant = [];


        $(".commissionType").select2({
            tags: true
        });
        $("#deductible_type_id").select2({
            placeholder: "Select Fee Type",
            tags: true,
            allowClear: true
        });

        $("#finance-sales_progression").select2({
            placeholder: "Select Sales Progression",
            allowClear: true
        });
        $("#newSplitCommissionSource").select2({
            dropdownParent: $('#dealSplitModal'),
            tags: true
        });
        $("#finance-deductible_type").select2({
            placeholder: "Select Transaction Type",
            allowClear: true,
            tags: true,
            dropdownParent: $('#deductibleModal'),

        });

        let salesProgressionSelected = false;
        const splits = [];

        const selectCommissionSources = (deal_value, selected_value) => {
            let options = '';
            if (deal_value?.type == 'Rental') {
                options = `<option ${selected_value == "Owner Side Commission" ? 'selected' : ''} value="Owner Side Commission">Owner Side Commission</option>
                                        <option ${selected_value == "Tenant Side Commission" ? 'selected' : ''} value="Tenant Side Commission">Tenant Side Commission</option>
                                        <option ${selected_value == "Cashback" ? 'selected' : ''} value="Cashback">Cashback</option>`;

            } else {
                options = `<option ${selected_value == "Seller Side Commission" ? 'selected' : ''} value="Seller Side Commission">Seller Side Commission</option>
                                        <option ${selected_value == "Buyer Side Commission" ? 'selected' : ''} value="Buyer Side Commission">Buyer Side Commission</option>
                                        <option ${selected_value == "Cashback" ? 'selected' : ''} value="Cashback">Cashback</option>`;
            }

            return options;
        }
        $(document).ready(function () {
            setTimeout(function () {
                handleDealStatus(deal.value);
            }, 3000);
                <?php  echo 'var isAllowedToUploadLeadDocuments = '.json_encode($isAllowedToUploadLeadDocuments).';'; ?>

                if(isAllowedToUploadLeadDocuments == true) {
                  console.log('isAllowedToUploadLeadDocuments', isAllowedToUploadLeadDocuments);

                } else {
                    console.log('is not allowed', isAllowedToUploadLeadDocuments);
                }

            $("#deal_split_agent_id").select2({
                placeholder: "Select Agent",
                dropdownParent: $('#dealSplitModal'),
                allowClear: true
            });
            setTimeout(
                function () {
                    if (deal.value !=null && deal.value.agent != null) {
                        $(".deal_primary_agent_card").html(`<div class="d-flex float-end float-right">
                    <img src="${deal.value?.agent?.thumb_url}" alt="" class="avatar-xs rounded-3 me-2">
                    <div>
                        <h5 class="fs-13 mb-0">${deal.value.agent.full_name}</h5>
                        <p class="fs-12 mb-0 text-muted">${deal.value.agent.position}</p>
                    </div>
                </div>`)
                    }
                }, 1000);


        })
        const getSalesProgressions = () => {
            let transaction_type = $("#finance-transaction_type").val();

            if(transaction_type !=null || transaction_type != undefined || transaction_type != '') {


                $.ajax({
                    url: "{{route('deal.get.sales.progressions')}}?transaction_type=" + transaction_type,
                    type: 'GET',
                    dataType: 'json',
                    success: function (result) {
                        if (result.status == '1') {
                            let html = '';
                            let users = result.data || {};

                            for (const key in users) {

                                if (salesProgressionSelected == true) {

                                    html += `<option value="${users[key].id}" >${users[key].full_name}</option>`;
                                } else {
                                    html += `<option value="${users[key].id}" ${users[key].selected == true ? 'selected' : ''}>${users[key].full_name}</option>`;
                                }
                            }

                            $("#finance-sales_progression").val('').trigger('change');
                            $("#finance-sales_progression").empty();
                            $("#finance-sales_progression").append(html);
                        } else {
                            $("#finance-sales_progression").val('').trigger('change');
                        }
                    }
                });
            }
        }
        // const commissions = [];
        const closeCommissionCard = () => {
            $("#commissionCard").addClass('d-none');
            $("#notCommissionCard").removeClass('d-none');
            getDealCommissions(deal_id);
            getDealSplits(deal_id);
        }
        const showCommissionCard = (getCommission = true) => {
            $("#commission_form_deal_id").val(deal_id)
            $('#commissionCard').removeClass('d-none');
            $("#notCommissionCard").addClass('d-none');
            $("#finance-transaction_type").select2({
                placeholder: "Select Transaction Type",
                allowClear: true
            });

            if (deal.value.type == 'Secondary Sale') {
                @can('Sales Progression Data')
                $(".sales-progression-section").removeClass('d-none');

                @else
                $(".sales-progression-section").addClass('d-none');

                @endcan
                getSalesProgressions();
                checkvalidations('sales-prog-prop')
            } else {
                $(".sales-progression-section").addClass('d-none');
            }
            if (getCommission === true) {
                getDealCommissions(deal_id);

            }

            if (deal.value != undefined || deal.value != null) {
                $(".net_commission").html(numberFormat(deal.value.net_commission) || 0);
                $(".total_vat").html('0');
                $(".gross_commission").html(numberFormat(deal.value.gross_commission) || 0);
                $("#financial-net_commission").val(numberFormat(deal.value.net_commission));
                $("#financial-total_vat").val('0');
                $("#financial-gross_commission").val(numberFormat(deal.value.gross_commission));
                $("#finance-deal_price").val(numberFormat(deal.value.deal_price));
                if(deal.value.gross_commission == 0 || deal.value.gross_commission == null || deal.value.gross_commission == '') {
                    $("#finanical-commission_saveBtn").prop('disabled', true);
                } else {
                    $("#finanical-commission_saveBtn").prop('disabled', false);
                }
            }
        }
        const addCommissionRow = () => {
            let salesProgressionClass = 'd-none';
            if(deal.value.type == 'Secondary Sale'){
                salesProgressionClass = '';
            }
            let uuid = uuidv4();
            let sourceList = selectCommissionSources(deal.value, null);
            let html = ` <tr class="mb-1" id="` + uuid + `">
                <td><select class="commissionType" id="commissionType` + uuid + `" name="commission_sources[]" data-id="` + uuid + `" required>
                <option value="" disabled selected>Select Source</option>
                             ` + sourceList + `
                           </select>
<div class="invalid-feedback">Please select source</div>
                       </td>
<td>
<input type="hidden" name="commission_id[]">
                       <div class="input-group">
                           <span class="input-group-text">AED</span>
                           <input type="text"
                id="commissionAmount` + uuid + `" data-id="` + uuid + `"
                class="form-control commissionAmount commissionAmount` + uuid + ` cleave"
                                 name="commission_amount[]" autocomplete="off" required>
                       </div>
                   </td>
                       <td>
                           <div class="input-group">
                               <span class="input-group-text">%</span>
                               <input type="text" class="form-control commissionPercentage commissionPercentage` + uuid + `"
data-id="` + uuid + `" name="commission_percentage[]"
                                      id="percentage` + uuid + `" autocomplete="off" required>
                           </div>
                       </td>

                       <td>
 <div class="form-check mt-2">
                               <input class="form-check-input vat vat` + uuid + `" data-id="` + uuid + `"
                               type="checkbox"   name="commission_vat[]" id="vat` + uuid + `">
                <input type="hidden" name="vat[]" class="vatValue` + uuid + `">
            <label><span class="vatLabel${uuid}"></span></label>

                       </div>
</td>
                       <td class="progression-col `+salesProgressionClass+` sales-progression-section">
                <input type="text" class="form-control  salesProgressionAmt salesProgressionAmt` + uuid + `"
        data-id="` + uuid + `" ${salesProgressionClass == '' ? 'required' : ''} autocomplete="off"
name="sales_progression_amount[]">
                   </td>
<td class="col-1 subTotal` + uuid + `">

</td>
<td class="col-1">
                      <button type="button" class="btn btn-sm btn-outline-danger text-center remove-commission-row-button" onclick="removeCommissionRow('` + uuid + `')">
                               <i class="mdi mdi-minus"></i>

                           </button>
                   </td>
</tr>`;
            $('#commissionList').append(html);
            $(".commissionType").select2({
                placeholder: "Select Source",
                allowClear: true,
                tags: true
            });
            initCleaveInput('.cleave');
        }
        const getDealCommissions = (id) => {
            if (id == undefined || id == null || id == '') {
                return '';
            }
            $.ajax({
                url: route('get.deal.getDealCommissions') + '?deal_id=' + id,
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function (result) {
                    let html = '';

                    if (result.status == '1') {
                        let comisn = result.data;
                        if(comisn.length == 0) {
                            showCommissionCard(false);
                        }
                        commissions_constant.value = comisn;
                        let sales_progression = result.sales_progression || {};
                        salesProgressionAgentRow(result.sales_progression)
                        if (comisn.length > 0) {


                        } else {
                            //this is called when there is no commission and it is being used to hide the commission card when the page or step is loaded
                            showCommissionCard(false);
                            setTimeout(function () {
                                addCommissionRow();
                            }, 1000);


                        }
                        let totalSalesProgressAmt = 0;
                        let total_vat = 0;
                        for (let i = 0; i < comisn.length; i++) {
                            let uuid = uuidv4();
                            totalSalesProgressAmt += parseFloat(comisn[i].sales_progression_amount);
                            let sourceList = selectCommissionSources(deal.value, comisn[i].commission_source);
                            let salprogamt = comisn[i].sales_progression_amount == 0 ? 0 : parseFloat(comisn[i].sales_progression_amount);
                            let vatAmtRow = (comisn[i].vat_amount == 0 || comisn[i].vat_amount == null)? 0 : parseFloat(comisn[i].vat_amount);
                            let subTotal = parseFloat(comisn[i].commission_amount) + vatAmtRow + salprogamt;
                            subTotal = isNaN(subTotal) ? 0 : subTotal;
                            total_vat += parseFloat(comisn[i].vat_amount) || 0;

                            html += `<tr  id="commrow${comisn[i].id}">
                    <td><select class="commissionType" id="commissionType` + uuid + `"  name="commission_sources[]" data-id="` + uuid + `" required>
                            <option value="" selected disabled>Select Source</option>
                               ` + sourceList + `
                           </select>
<div class="invalid-feedback">Please select source</div>
                       </td>
                <td>
                    <input type="hidden" name="commission_id[]" value="${comisn[i].id}">
                       <div class="input-group">
                           <span class="input-group-text">AED</span>
                           <input type="text" id="commissionAmount` + uuid + `" data-id="` + uuid + `"
                    class="form-control commissionAmount cleave"
                    autocomplete="off"
                                  name="commission_amount[]" value="${comisn[i].commission_amount || ''}">
                       </div>
                   </td>
                       <td>
                           <div class="input-group">
                               <span class="input-group-text">%</span>
                               <input type="text" class="form-control commissionPercentage commissionPercentage` + uuid + `"
                                data-id="` + uuid + `" value="${comisn[i].commission_percentage || ''}" name="commission_percentage[]" id="percentage` + uuid + `">
                           </div>
                       </td>

                       <td>
                           <div class="form-check mt-2">
                               <input class="form-check-input vat vat` + uuid + `"
                                    data-id="` + uuid + `"
                                 autocomplete="off"
                                name="commission_vat[]"
                               type="checkbox" id="vat` + uuid + `" ${comisn[i].is_vat_applicable == 1 ? 'checked' : ''}>
                            <label><span class="vatLabel${uuid}">${comisn[i].vat_amount || ''}
                                </span></label>
                                <input type="hidden" name="vat[]" value="${comisn[i].vat_amount || ''}" class="vatValue` + uuid + `">
                        </div>
                   </td>
                <td class="progression-col sales-progression-section">
                    <input type="text" class="form-control salesProgressionAmt salesProgressionAmt` + uuid + `"
                    data-id="` + uuid + `" name="sales_progression_amount[]"
                    autocomplete="off"
                    value="${comisn[i]?.sales_progression_amount || ''}">
                   </td>
                <td class="col-1 subTotal${uuid}">
                    ${numberFormat(subTotal.toFixed(2))}
                </td>
        <td class="col-1">
                <button type="button" class="btn btn-sm btn-outline-danger text-center remove-commission-row-button"
                onclick="removeCommissionRow('commrow${comisn[i].id}','${comisn[i].id}')">
                               <i class="mdi mdi-minus"></i>

                           </button></td>
</tr>`;
                        }
                        $('.total_progression_fee').html(numberFormat(totalSalesProgressAmt) || 0);
                        $('#financial-total_progression_fee').val(totalSalesProgressAmt);
                        $('.total_vat').html(numberFormat(total_vat) || 0);
                        $('#financial-total_vat').val(total_vat);
                        $('#commissionList').html(html);
                        $(".commissionType").select2({
                            tags: true
                        });
                        $(".status_deal_price").html(numberFormat(result.deal?.deal_price));
                        $(".status_gross_commission, .gross_commission").html(numberFormat(result.deal?.gross_commission));
                        $(".status_net_commission, .net_commission").html(numberFormat(result.deal?.net_commission));
                        $("#financial-net_commission").val(result.deal?.net_commission);
                        $("#financial-gross_commission").val(result.deal?.gross_commission);

                        if (result.sales_progression?.conveyancer_id) {
                            salesProgressionSelected = true;
                        }
                        if(deal.value) {
                            if (deal.value.type == 'Secondary Sale') {
                                $(".sales-progression-section").removeClass('d-none');
                            } else {
                                $(".sales-progression-section").addClass('d-none');
                            }
                        }

                        setTimeout(function () {
                            $("#finance-sales_progression").val('');
                            $("#finance-sales_progression").val(result?.sales_progression?.conveyancer_id).trigger('change');
                        }, 1000);
                        $("#finance-transaction_type").val(result.sales_progression?.transaction_type);
                        $("#finance-progression_amount").val(result.sales_progression?.progression_amount);
                        $("#finance-progression_amount_label").html(result.sales_progression?.progression_amount);
                        $("#finance-transaction_type").val(result.sales_progression?.transaction_type).trigger('change');
                        initCleaveInput('.cleave');
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }
        const deleteDealCommission = (id) => {
            let data = {
                id: id,
                deal_id: deal_id
            }
            axios.post(`/api/vue/v1/delete-deal-commission`, data).then((response) => {
                if (response.data.status == 1) {

                    refreshFinancialData();
                    showToastify('Commission Deleted Successfully', 'success')
                } else {
                    showToastify('Something went wrong', 'error')
                }
                //getDealCommissions(deal_id);
            });
        }

        $(document).on('select2:select','.commissionType',function (e) {
            let selectedValue =   $(this).val();
            let selectedElementId = $(this).attr('id');
            // let data = e.params.data;

            $(".commissionType").not(this).each(function() {
                if ($(this).val() == selectedValue) {
                    showToastify('This source is already selected', 'error')
                    $("#"+selectedElementId).val("").trigger('change');
                    return false; // Prevent further processing
                }
            });

        });

        $(document).on('keyup', '.commissionAmount', function () {
            if ($("#finance-deal_price").val() == 0 || $("#finance-deal_price").val() == '' || $("#finance-deal_price").val() == '0') {
                showToastify('Please enter deal price', 'error')
                return '';
            }
            let id = $(this).data('id');
            let totalamt = 0;
            let deal_price = $('#finance-deal_price').val();
            deal_price = String(deal_price).replace(/,/g, '');
            $('.commissionAmount').each(function () {
                let tempA = String($(this).val()).replace(/,/g, '');
                totalamt += parseFloat(tempA);
            });
            if (totalamt > deal_price) {
                $(this).val(0);
                $('.commissionPercentage' + id).val(0);
                showToastify('Total commission amount can not be greater than deal price', 'error')
            }

            let commissionAmount = $('#commissionAmount' + id).val();
            //remove comma from commissionAmount
            commissionAmount = String(commissionAmount).replace(/,/g, '') ?? 0;
            let commissionPercentage = $('.commissionPercentage' + id).val();


            commissionPercentage = parseFloat(commissionAmount) / deal_price * 100;
            commissionPercentage = isNaN(commissionPercentage) ? '' : commissionPercentage.toFixed(2);
            $('.commissionPercentage' + id).val(commissionPercentage || '')
            calculateCommission();
        });
        $(document).on('keyup', '.commissionPercentage', function () {
            if ($("#finance-deal_price").val() == 0 || $("#finance-deal_price").val() == '' || $("#finance-deal_price").val() == '0') {
                showToastify('Please enter deal price', 'error')
                return '';
            }
            let id = $(this).data('id');
            //calculate if total .commissionPercentage count is going above 100 then restrict user
            let totalPercentage = 0;
            $('.commissionPercentage').each(function () {
                totalPercentage += parseFloat($(this).val());
            });
            if (totalPercentage > 100) {
                $(this).val(0);
                $('.commissionPercentage' + id).val(0);
                showToastify('Total Percentage cannot be greater than 100', 'error')
            }


            let commissionAmount = $('#commissionAmount' + id).val();
            let commissionPercentage = $('.commissionPercentage' + id).val();
            let deal_price = $('#finance-deal_price').val();
            deal_price = String(deal_price).replace(/,/g, '');
            commissionAmount = commissionPercentage * (parseFloat(deal_price) / 100);
            commissionAmount = isNaN(commissionAmount) ? '' : commissionAmount.toFixed(2);
            $('#commissionAmount' + id).val(numberFormat(commissionAmount) || '')
            calculateCommission();

        });
        $(document).on('change', '.vat', function () {
            if ($("#finance-deal_price").val() == 0 || $("#finance-deal_price").val() == '' || $("#finance-deal_price").val() == '0') {
                showToastify('Please enter deal price', 'error')
                return '';
            }
            let id = $(this).data('id');
            if (!$(this).is(":checked")) {
                $('.vatValue' + id).val(0);
                $('.vatLabel' + id).html(0);
            }
            calculateCommission();
        });
        $(document).on('change', '#finance-transaction_type', function () {
            let selected_value = $(this).val();

            //find it on deductibles
            let deductible = deductibles.find(x => x.name == selected_value);
            if (deductible == undefined || deductible == null) {
                $("#finance-progression_amount").val(0);
                return '';
            } else {
                $("#finance-progression_amount").val(numberFormat(deductible?.type_value));
                $("#finance-progression_amount_label").html(`(`+numberFormatAX(deductible?.type_value)+`)`);
            }
            getSalesProgressions();
        });
        $('#finance-transaction_type').on('select2:clear', function (e) {
           $("#finance-sales_progression").val('').trigger('change');
           $("#finance-progression_amount_label").html('');
        });
        $(document).on('keyup', '.salesProgressionAmt', function () {
            let totalSalesProgressAmt = 0;
            let sales_prog_amt = $("#finance-progression_amount").val();
            sales_prog_amt = String(sales_prog_amt).replace(/,/g, '');
            $('.salesProgressionAmt').each(function () {
                let tempA = String($(this).val()).replace(/,/g, '') ?? 0;
                totalSalesProgressAmt += parseFloat(tempA) || 0;
            });
            $('.total_progression_fee').html(numberFormat(totalSalesProgressAmt) || 0);
            $('#financial-total_progression_fee').val(totalSalesProgressAmt);
            if ((totalSalesProgressAmt < sales_prog_amt) || totalSalesProgressAmt == 0 || sales_prog_amt == '' || isNaN(sales_prog_amt)) {
                $("#finanical-commission_saveBtn").attr('disabled', true);
               $("#error_message").html(`<span class="progression_error" style="margin-right: 10px;"><i class="ri-information-line"></i>Progression amount total can not be lower than `+sales_prog_amt+`</span>`)
               // showToastify('Individual progression amount can not be greater than main progression amount', 'error')
            }else{
                $("#finanical-commission_saveBtn").attr('disabled', false);
                $(".progression_error").remove()
            }
            calculateCommission();
        });
        // $(document).keyup('keyup','#deal_price', function () {
        //     calculateCommission();
        // });
        $(document).on('keyup','#deal_split_commission_type_value',function (){
           let current_split_amount = $("#deal_split_edit_split_amount_input").val();
            current_split_amount = String(current_split_amount).replace(/, /g, '');

           let current_split_commission_type_value = $(this).val();
           let commission_value = current_split_amount * (current_split_commission_type_value / 100);
           if(current_split_amount < commission_value){
                $("#deal_split_commission_amount").val(0);
                showToastify('Commission amount can not be greater than split amount', 'error')
                return '';
           }
           $("#deal_split_commission_amount").val(numberFormat(commission_value));
        });
        $(document).on('keyup','#deal_split_commission_amount',function (){
            let current_split_amount = $("#deal_split_edit_split_amount_input").val();
            current_split_amount = String(current_split_amount).replace(/, /g, '');
            let current_split_commission_amount = $(this).val();
            current_split_commission_amount = String(current_split_commission_amount).replace(/, /g, '');
            let commission_value = (current_split_commission_amount / current_split_amount) * 100;
            if(commission_value > 100){
                $("#deal_split_commission_type_value").val(0);
                showToastify('Commission amount can not be greater than split amount', 'error')
                return '';
            }
            $("#deal_split_commission_type_value").val(commission_value.toFixed(2));
        });

        const removeCommissionRow = (uuid, commission_id) => {

            $('#' + uuid).remove();
            if (commission_id != undefined) {
                deleteDealCommission(commission_id);
            }
            calculateCommission();
        }

        const calculateCommission = () => {
            let deal_price = $('#finance-deal_price').val();
            deal_price = String(deal_price).replace(/,/g, '');
            deal_price = parseFloat(deal_price);

            let gross_commission = deal_price;
            let total_commission_amount = 0;
            let total_vat = 0;
            let total_sales_progression = 0;
            $('.commissionAmount').each(function () {
                let rowSubTotal = 0;
                let currentRowId = $(this).data('id');
                let current_commission_amount = $(this).val();
                //remove comma from string current_commission_amount
                current_commission_amount = String(current_commission_amount).replace(/,/g, '');
                let isVat = $('.vat' + currentRowId).is(":checked");
                if (isVat) {
                    let temp3 = parseFloat(current_commission_amount) / 100;
                    let currentVat = parseFloat(temp3) * 5;

                    $(".vatValue" + currentRowId).val(currentVat || '')
                    $(".vatLabel" + currentRowId).html(numberFormat(currentVat || 0) || '')
                    total_vat += currentVat || 0;
                    // $(".vatValue" + currentRowId).val(currentVat)
                }
                let saleProgAmt = $('.salesProgressionAmt' + currentRowId).val();
                rowSubTotal = parseFloat(current_commission_amount) + parseFloat($(".vatValue" + currentRowId).val() || 0) + parseFloat(saleProgAmt || 0);
               rowSubTotal = isNaN(rowSubTotal) ? '' : rowSubTotal.toFixed(2);
                $(".subTotal" + currentRowId).html(numberFormat(rowSubTotal || 0) || 0);
                total_commission_amount += parseFloat(current_commission_amount || 0);
                total_sales_progression+= parseFloat(saleProgAmt || 0);
            });

            total_commission_amount = isNaN(total_commission_amount) ? 0 : total_commission_amount;
            let total_net_commission_placeholder = isNaN(total_commission_amount) ? 0 : numberFormat(total_commission_amount || 0);

            $(".net_commission").html(total_net_commission_placeholder)  //old net_commission
            $("#financial-net_commission").val(total_commission_amount.toFixed(2) || 0); //old net_commission


            $(".total_vat").html(numberFormat(total_vat || 0 ))
            let total_gross_commission = parseFloat(total_commission_amount) + parseFloat(total_vat) + parseFloat(total_sales_progression);
            $("#financial-total_vat").val(total_vat.toFixed(2) || 0);

            $(".gross_commission").html(numberFormat(total_gross_commission) || 0);
            $(".total_progression_fee").html(numberFormat(total_sales_progression) || 0);
            $("#financial-total_progression_fee").val(numberFormat(total_sales_progression) || 0);
            $("#financial-gross_commission").val(total_gross_commission.toFixed(2) || 0);
           // $(".net_commission").html(numberFormat(total_gross_commission || 0) || 0)
            //$("#financial-net_commission").val(total_gross_commission.toFixed(2) || 0);
            if(total_gross_commission == 0 || total_gross_commission == null || total_gross_commission == '' || total_gross_commission < 1) {
                $("#finanical-commission_saveBtn").prop('disabled', true);
            } else if(deal.value.type == 'Secondary Sale') {
                if(total_sales_progression == 0 || total_sales_progression == null || total_sales_progression == '' || total_sales_progression < 1){
                    $("#finanical-commission_saveBtn").prop('disabled', true);
                } else {
                    $("#finanical-commission_saveBtn").prop('disabled', false);
                }

            }else{
                $("#finanical-commission_saveBtn").prop('disabled', false);
            }
        }
        $(document).on('keyup', '#finance-deal_price', function () {
            if ($(this).val() == 0 || $(this).val() == '' || $(this).val() == '0') {
                $('.commissionAmount, .commissionPercentage').val(0);
            }
            $(".commissionPercentage").each(function () {
                let id = $(this).data('id');
                // let commissionAmount = $('#commissionAmount' + id).val();
                let commissionPercentage = $('.commissionPercentage' + id).val();
                let deal_price = $('#finance-deal_price').val();
                deal_price = String(deal_price).replace(/,/g, '');
                let commission_amount = commissionPercentage * (parseFloat(deal_price) / 100);
                $('#commissionAmount' + id).val(commission_amount || '')
            });
            calculateCommission();
        });
        const splitRow = (commission) => {
            if(commission?.user == undefined){
                return '';
            }
            // let showEditBtn =
            let html = `
          <!-- ... Commission HTML structure ... -->

         <div class="row finRow" v-if="index2 == 0">
                  <div class="d-flex col-6">
                    <div @click="toggleAccordion('collapseExample45')" class="flex-shrink-0">
                      <div class="avatar-sm rounded">
                        <img src="${commission.user.thumbnail}" alt=""
                             class="member-img img-fluid d-block rounded">
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="fs-14 mb-1">${commission.user.full_name}</h6>
                      <p class="text-muted mb-0">${commission.user.position}</p>
                    </div>
                  </div>
                  <div class="col">
                    <div class="flex-shrink-0 text-left ">

                      <p class="fs-12 mb-0">
                                         <span
                                             class="ms-1 fs-14">${
                commission.commission_value || "0"
            }${commission.commission_type == 'Percentage' ? '%' : '/-'} </span>
                      </p>
                    </div>
                  </div>
                  <div class="col-sm-auto las-col">
                    <div
                        class="d-flex flex-wrap gap-2 li-action-button align-items-center mx-auto my-3 my-lg-0 edit-action d-none">
                      <a href="javascript:void(0)" style="padding: 4px !important;
    height: 30px;" onclick="editCommission('${commission.id}');   event.stopPropagation();"
                         class="hide btn btn-ghost-info btn-icon d-none custom-toggle edit-commission-button">
                        <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span> </a>
                      <a href="#!" style="padding: 4px !important;
    height: 30px;" onclick="removeSplitCommission('${commission.id}')"
                         class="hide btn btn-ghost-danger btn-icon custom-toggle remove-split-commission">
                        <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span> </a>

                    </div>
                    <div class="flex-shrink-0 text-end">
                      <h6 class="fs-14 mb-1">AED ${numberFormat(commission.commission)}</h6>

                    </div>
                  </div>
                </div>
`;
            return html;
        }

        const splitFirstRow = (commission, index) => {
            if(commission?.user == undefined){
                return '';
            }
            let html = `  <div class="">
                <div class="row finRow" v-if="index2 == 0" onclick="toggleAccordion('collapseExample${index}')">
                  <div class="d-flex col-6">
                    <div @click="toggleAccordion('collapseExample45')" class="flex-shrink-0">
                      <div class="avatar-sm rounded">
                        <img src="${commission?.user?.thumbnail}" alt=""
                             class="member-img img-fluid d-block rounded">
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="fs-14 mb-1">${commission.user.full_name}</h6>
                      <p class="text-muted mb-0">${commission.user.position}</p>
                    </div>
                  </div>
                  <div class="col">
                    <div class="flex-shrink-0 text-left ">

                      <p :class="commission.commission_category === 'INDIRECT' ? 'text-danger fs-12 mb-0' : 'text-success fs-12 mb-0'">
                                         <span
                                             class="ms-1 fs-14">${
                commission.commission_value || "0"
            }${commission.commission_type == 'Percentage' ? '%' : '/-'} </span>
                      </p>
                    </div>
                  </div>
                  <div class="col-sm-auto las-col">
                    <div
                        class="d-flex flex-wrap gap-2 li-action-button align-items-center mx-auto my-3 my-lg-0 edit-action">
                      <a href="javascript:void(0)" style="padding: 4px !important;
    height: 30px;" onclick="editCommission('${commission.id}');   event.stopPropagation();"
                         class="hide btn btn-ghost-info btn-icon custom-toggle edit-commission-button">
                        <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span> </a>
                      <a href="#!" style="padding: 4px !important;
    height: 30px;" onclick="deleteDealCommission('${commission.id}','${commission.deal_id}')"
                         class="hide btn btn-ghost-danger btn-icon custom-toggle delete-deal-commission">
                        <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span> </a>

                    </div>
                    <div class="flex-shrink-0 text-end">
                      <h6 class="fs-14 mb-1">AED ${numberFormat(commission.commission)}</h6>

                    </div>
                  </div>
                </div>
              </div>`;
            return html;
        }
        const splitCompanyRow = (commission) => {
            if(commission == undefined || commission == null || commission == ''){
                return '';
            }
            @can('See Company Earning')

            let html = ` <div v-if="split" class="row finRow">
                  <div class="col-6 d-flex">

                    <div class="flex-shrink-0">
                      <div class="avatar-sm rounded bg-dark">
                        <img src="/assets/images/logo/logo.png" style="height: 80%" alt=""
                             class="member-img img-fluid d-block rounded p-2">
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="fs-14 mb-1">AX CAPITAL</h6>
                      <p class="text-muted mb-0">Real Estate Agency</p>
                    </div>

                  </div>
                  <div class="col">-</div>
                  <div class="col-sm-auto">

                    <div class="flex-shrink-0 text-end">
                      <h6 class="fs-14 mb-1">AED ${numberFormat(commission?.amount)}</h6>

                    </div>
                  </div>

                </div>`;
            return html;
            @else
            return '';
            @endcan
        }
        const drawSplits = (splits) => {
            let total_company_earning = 0;
            $("#splitList").html('');
            let splitCommissions = '';
            $.each(splits, function (index, split) {
                let uuid_index = uuidv4();
                let is_primary_agent = deal?.value?.agent_id == split?.user_id ? true : false;

                let actionBtns = `<button type="button" onclick="editSplit('${split.id}')" class="btn btn-sm btn-outline-info edit-split-button edit-action"><i
                                    class="ri-pencil-fill"></i> </button>
    `;
                if(index != 0){
                    actionBtns+= `<button type="button" onclick="removeSplit('${split.id}')" class="btn btn-sm btn-outline-danger remove-split-button ${ (index == '0' || index == 0) ? 'd-none' : ''} edit-action"><i
                                    class="ri-delete-bin-2-line"></i> </button>`;
                }
                let splitHtml = `
        <div class="card">
          <div class="card-header">
          <div class="row">
            <div class="col-12">
          <h4 class="card-title mb-0">${split.commission_source} Commission </h4>
         <span class="float-end ml-2" style="    margin-left: 6px;">${actionBtns}</span>
          <span class="float-end">AED ${numberFormat(split.split_amount)}</span>
        <br>
              <span
                  class="text-muted float-end fs-12"> Created By ${split?.created_by?.full_name}</span>
        </div>
        </div>
        </div>
<div class="card-body">
          <div class="row">
            <div class="col-12">`;
                let i = 0;
                $.each(split.commissions, function (index2, commission) {
                    if (i == 0) {
                        splitHtml += splitFirstRow(commission, uuid_index);
                    }
                    i++;
                });

                let j = 0;
                splitHtml += `<div class="collapse show"  id="collapseExample${uuid_index}">`;
                $.each(split.commissions, function (index2, commission) {
                    if (j > 0) {
                        splitHtml += splitRow(commission);
                    }
                    j++;

                });
                splitHtml += splitCompanyRow(split.company_earning);
                let tce = (split.company_earning == null || split.company_earning == undefined) ? 0 : parseFloat(split?.company_earning?.amount);
                total_company_earning += tce || 0;
                splitHtml += `</div></div></div></div>`;
                $("#splitList").append(splitHtml);
                splitCommissions = '';

            });
            $(".company-income").html(numberFormat(total_company_earning));


        }
        const salesProgressionAgentRow = (sales_progression) => {
            if(sales_progression == {} || sales_progression == undefined || sales_progression == null || sales_progression == ''){
                return '';
            }
            // let showEditBtn =
            let html = `
          <!-- ... Commission HTML structure ... -->

         <div class="row finRow" v-if="index2 == 0">
                  <div class="d-flex col-6">
                    <div @click="toggleAccordion('collapseExample45')" class="flex-shrink-0">
                      <div class="avatar-sm rounded">
                        <img src="${sales_progression.user.thumb_url}" alt=""
                             class="member-img img-fluid d-block rounded">
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="fs-14 mb-1">${sales_progression.user.full_name}</h6>
                      <p class="text-muted mb-0">${sales_progression.user.position}</p>
                    </div>
                  </div>
                  <div class="col">
                    <div class="flex-shrink-0 text-left ">

                      <p>
Assignment Mode: ${sales_progression.conveyancer_selection_mode || ''}
                      </p>
                    </div>
                  </div>
                  <div class="col-sm-auto las-col">
                    <div
                        class="d-flex flex-wrap gap-2 li-action-button align-items-center mx-auto my-3 my-lg-0 edit-action">


                    </div>
                    <div class="flex-shrink-0 text-end">
                      <h6 class="fs-14 mb-1">AED ${numberFormat(sales_progression.progression_amount)}</h6>

                    </div>
                  </div>
                </div>
`;
            $("#progression_fee_agent_card").html(html);
        }
        const editCommission = async (id) => {
            if (id == undefined || id == null || id == '') {
                return '';
            }
            let response = await axios.get(route('get.deal.split.commission') + '?split_id=' + id)
            if (response.status == 200) {
                if (response.data.status == '1') {
                    let data = response.data.data;
                    $("#dealSplitCommissionModal").modal('show');
                    $("#deal_split_commission_id").val(data.id);
                    $("#deal_split_modal_deal_id").val(data.deal_id);
                    $("#deal_split_commission_agent_id").val(data.user_id);
                    $("#deal_split_commission_agent_name").html(data.user?.full_name);
                    $(".deal_split_commission_category").html(data.commission_category);
                    let commType = '';
                    if (data.commission_type == 'Percentage') {
                        commType = '%';
                    } else {
                        commType = '/-';
                    }

                    $("#deal_split_commission_type").html(commType);
                    $("#deal_split_commission_type_value").val(data?.commission_value);
                    $("#deal_split_commission_amount").val(data.commission);
                    $(".deal_split_edit_net_commission").html(numberFormat(deal.value.net_commission));
                    $(".deal_split_edit_split_amount").html(numberFormat(response?.data?.split?.split_amount));
                    $("#deal_split_edit_split_amount_input").val(response?.data?.split?.split_amount);


                    $("#dealSplitCommissionModal").find('.modal-title').html('Edit Deal Split Commission');
                } else {

                }


            } else {
                splits.value = []
                showToastify('Something went wrong', 'error')
            }
        }
        $(document).on('submit', '#splitCommissionForm', function (event) {
            $("#splitCommissionFormSubmitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            //remove comma from split amount
            let split_amount = formData.get('split_amount');
            console.log(split_amount);
           // split_amount = String(split_amount).replace(/,/g, '');

            // formData.set('amount', split_amount);

            $.ajax({
                url: "{{route('update.deal.commission.calculations')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        // getDealSplits(deal_id, true);

                        getDealSplits(deal_id);
                        showToastify(result.msg, "success")
                        $('#dealSplitCommissionModal').modal('hide'); //hide form modal
                        $("#splitCommissionFormSubmitBtn").prop('disabled', false); //enable submit button
                        $('#splitCommissionForm').trigger('reset'); //

                    } else if (result.status == '2') {
                        $("#splitCommissionFormSubmitBtn").prop('disabled', false);
                        $.each(result.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    } else {
                        $("#splitCommissionFormSubmitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    $("#splitCommissionFormSubmitBtn").prop('disabled', false);
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });
        const editSplit = async (id) => {
            let splitSource = getSplitSource(deal.value.type);
            $("#newSplitCommissionSource").html(splitSource);
            if (id == undefined || id == null || id == '') {
                return '';
            }
            let response = await axios.get(route('get.deal.split') + '?split_id=' + id)
            if (response.status == 200) {
                if (response.data.status == '1') {
                    let data = response.data.data;
                    $("#dealSplitModal").modal('show');
                    $("#deal_split_deal_id").val(deal_id);
                    $("#deal_split_deal_price").html(numberFormat(deal.value.deal_price));
                    $("#deal_split_gross_commission").html(numberFormat(deal.value.gross_commission));
                    $("#deal_split_net_commission").html(numberFormat(deal.value.net_commission));
                    $("#split_modal_deal_type").html(deal.value.type);
                    $("#deal_split_id").val(data.id);
                    $("#deal_split_agent_id").val(data.user_id).trigger('change');
                    $("#newSplitCommissionSource").val(data.commission_source).trigger('change');
                    $("#deal_split_amount").val(data.split_amount);
                    $("#deal_split_percentage").val(data.split_percentage);
                    $("#dealSplitModal").find('.modal-title').html('Edit Deal Split');
                } else {

                }
            }
        }
        const getDealSplits = async (id, refresh = false) => {
            if (id == undefined || id == null || id == '') {
                return '';
            }
            let response = await axios.get(route('get.deal.commission.calculations1') + '?deal_id=' + id + `&refresh_calculations=` + refresh)
            if (response.status == 200) {
                splits.value = response.data.data
                drawSplits(splits.value)
                handleDealStatus(deal.value)
            } else {
                splits.value = []
                showToastify('Something went wrong', 'error')
            }
        }

        const toggleAccordion = (element) => {
            if ($("#" + element).hasClass('show')) {
                $("#" + element).collapse('toggle')
            } else {
                $("#" + element).collapse('toggle')
            }

            // $('.collapse').collapse('toggle')

        }
        const deleteDealSplit = (split_id, deal_id) => {
            let data = {
                id: split_id,
                deal_id: deal_id
            }
            axios.post(`/api/vue/v1/deal-split-remove`, data).then((response) => {
                getDealSplits(deal_id, true);
            });
        }
        const removeSplit = (split_id) => {
            Swal.fire({
                html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>' +
                    '<div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4>' +
                    '<p class="text-muted mx-4 mb-0">Are you Sure You want to remove this deal split from this deal ?</p>' +
                    '</div></div>',


                showCancelButton: true,
                cancelButtonText: 'Cancel',
                cancelButtonClass: 'btn btn-warning action-close w-xs',

                showCloseButton: true,
                confirmButtonText: 'Delete',
                confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

                buttonsStyling: false,

            }).then(function (result) {
                if (result.isConfirmed) {
                    deleteDealSplit(split_id, deal_id);

                }
            })

        }
        const removeSplitCommission = (commission_id) => {
            let data = {
                id: commission_id,
            }
            axios.post("{{route('deal.split.commission.remove')}}", data).then((response) => {
                getDealSplits(deal_id, true);
            });
        }

        getDealCommissions(deal_id);
        getDealSplits(deal_id);
        const drawDeductible = (deductible) => {
            let html = `

                <div class="row finRow">
                    <div class="d-lg-flex align-items-center col-6">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded">
                          <span class="avatar-title bg-warning-subtle rounded fs-3" style="background: #fff1f1">
                                    <i class="bx bx-dollar-circle text-danger"></i></span>
                        </div>
                        </div>
                        <div class="ms-lg-3 my-3 my-lg-0">
                        <p class="fs-14 mb-2">${deductible?.deductible_type?.name}</p>
                            <p class="text-muted mb-0"></p>
                            </div>
                        </div>

                        <div class="col">
                            <div class="">AED ${numberFormat(deductible?.amount)} ${deductible?.deductible_type?.type == 'percentage' ? '(' + deductible?.deductible_type?.type_value + '%)' : '/-'}</div>
                        </div>
                        <div class="col-sm-auto las-col">
                            <a href="#!" onclick="editDeductible('` + deductible.id + `', '` + deductible.deal_deductible_type_id + `','` + deductible?.amount + `','` + deductible?.deductible_type?.type + `','` + deductible?.deductible_type?.type_value + `')"
                               class="btn btn-ghost-info btn-icon custom-toggle edit-deductible-button edit-action">
                                <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span> </a>
                            <a href="#!" onclick="deleteDeductible('` + deductible.id + `')"
                               class="btn btn-ghost-danger btn-icon custom-toggle delete-deductible-button edit-action">
                                <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span> </a>

                        </div>
                    </div>`;
            return html;
        }
        const getDealDeductibles = async (id) => {
            let response = await axios.get(`/api/vue/v1/get-deductible?deal_id=${id}`)
            if (response.status == 200) {
                let deductibles = response.data.data;
                let html = '';
                if (response.data.status == '1') {
                    if(deductibles !== null || deductibles !== []) {

                        for (let i = 0; i < deductibles.length; i++) {
                            html += drawDeductible(deductibles[i]);
                        }
                    }else{
                        console.log('hererere')
                        html = `<div class="text-center text-muted">No fee added yet</div>`;
                    }
                    $('#deductibleList').html(html);
                    handleDealStatus(deal.value)
                }
            } else {
                deductibles.value = []
                showToastify('Unable to fetch fee details', 'error')
            }
        }

        const showDeductibleModal = () => {
            $(".deductible-inputs").val('')
            $("#finance-deductible_type").val('').trigger('change');
            $("#deductibleForm").trigger('reset');
            $('#deductibleModal').modal('show');
            $("#deductible_deal_id").val(deal_id);
            $("#deductible_deal_price").html(numberFormat(deal.value.deal_price));
            $("#deductible_gross_commission").html(numberFormat(deal.value.gross_commission));
            $("#deductible_net_commission").html(numberFormat(deal.value.net_commission));
        }
        const editDeductible = (id, type_id, amount, fee_type, fee_value) => {
            $(".deductible-inputs").val('')
            $("#deductibleForm").trigger('reset');
            $('#deductibleModal').modal('show');
            $("#deductible_deal_id").val(deal_id);
            $("#finance-commission_type").val(fee_type);
            $("#finance-commission_value").val(fee_value);
            $("#deductible_deal_price").html(numberFormat(deal.value.deal_price));
            $("#deductible_gross_commission").html(numberFormat(deal.value.gross_commission));
            $("#deductible_net_commission").html(numberFormat(deal.value.net_commission));
            $("#deductible_form_id").val(id);
            $("#finance-deductible_type").val(type_id).trigger('change');
            $("#deductible_form_amount").val(amount);
        }
        $(document).on('submit', '#deductibleForm', function (event) {
            $("#deductFormBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "/api/vue/v1/store-deductible",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        // getDealSplits(deal_id, true);
                        // getDealDeductibles(deal_id);
                        // getDealCommissions(deal_id);
                        refreshFinancialData()
                        showToastify(result.msg, "success")
                        $(".deductible-inputs").val('')
                        $('#deductibleModal').modal('hide'); //hide form modal
                        $("#deductFormBtn").prop('disabled', false); //enable submit button
                        $('#deductibleForm').trigger('reset'); //

                    } else if (result.status == '2') {
                        $("#deductFormBtn").prop('disabled', false);
                        $.each(result.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    } else {
                        $("#deductFormBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    $("#splitFormSubmitBtn").prop('disabled', false);
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });
        getDeal(deal_id)
        setTimeout(() => {
            if (deal.value) {
                $("#finance-deal_price").val(deal.value.deal_price)
                $(".net_commission").html(numberFormat(deal.value.net_commission) || 0)
                //$(".total_vat").html('')
                $(".gross_commission").html(numberFormat(deal.value.gross_commission) || 0)
                $(".status_gross_commission").html(numberFormat(deal.value.gross_commission))
                $(".status_deal_price").html(numberFormat(deal.value.deal_price))
                $(".status_net_commission").html(numberFormat(deal.value.net_commission))
                let deal_meta = deal.value.deal_meta;
                var listing_price;
                if(deal_meta != undefined && deal_meta != null && deal_meta != '') {


                    var listingPriceItem = deal_meta.find(item => item.meta_key === "listing_price");
                    if (listingPriceItem) {
                        listing_price = listingPriceItem.meta_value;
                        $(".property-listing-price").html(numberFormat(listing_price))
                        getDealDeductibles(deal_id);
                    } else {
                        console.log("Listing price not found in deal_meta");
                    }
                }
            }
        }, 1000);

        function initCleaveInput(selector, price = "") {
            //$(selector).val(price).trigger("change");

            $(selector).each(function () {
                new Cleave(this, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand'
                });
            });
        }

        initCleaveInput('.cleave');


        const refreshFinancialData = () => {

            getDealSplits(deal_id, true);
            getDealDeductibles(deal_id);
            getDealCommissions(deal_id);
            getDeal(deal_id);
        }
        const getSplitSource = (deal_type) => {
            if(deal_type == 'Rental'){
                return `<option value="" disabled selected>Select Source</option><option value="Owner Side">Owner Side</option>
                                   <option value="Tenant Side">Tenant Side</option>
                                   <option value="Cashback">Cashback</option>`;
            }else{
                return `<option value="" disabled selected>Select Source</option><option value="Seller Side">Seller Side</option>
                                      <option value="Buyer Side">Buyer Side</option>
                                   <option value="Cashback">Cashback</option>`;
            }
        }

        const showDealSplitModal = () => {
            $("#splitForm").trigger('reset');
            $(".split_form_inputs").val('').trigger('change');
            $('#dealSplitModal').modal('show');
            let splitSource = getSplitSource(deal.value.type);
            $("#newSplitCommissionSource").html(splitSource);

            $("#deal_split_deal_id").val(deal_id);
            $("#deal_split_deal_price").html(numberFormat(deal.value.deal_price));
            $("#deal_split_gross_commission").html(numberFormat(deal.value.gross_commission));
            $("#deal_split_net_commission").html(numberFormat(deal.value.net_commission));
            $("#split_modal_deal_type").html(deal.value.type);
           // $("#deal_split_agent_id").val('').trigger('change');
            $("#dealSplitModal").find('.modal-title').html('Add Deal Split');
        }
        $(document).on('submit', '#splitForm', function (event) {
            $("#splitFormSubmitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "/api/vue/v1/create-deal-split",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        getDealSplits(deal_id, false);
                        showToastify(result.msg, "success")
                        $('#dealSplitModal').modal('hide'); //hide form modal
                        $("#splitFormSubmitBtn").prop('disabled', false); //enable submit button
                        $('#splitForm').trigger('reset'); //

                    } else if (result.status == '2') {
                        $("#splitFormSubmitBtn").prop('disabled', false);
                        $.each(result.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    } else {
                        $("#splitFormSubmitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    $("#splitFormSubmitBtn").prop('disabled', false);
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });

        const calculateSplitAmount = () => {
            let deal_net_commission = deal?.value?.net_commission
            let deal_split_amount = $("#deal_split_amount").val();
            let deal_split_percentage = $("#deal_split_percentage").val();
            if(deal_split_percentage > 100){
                $("#deal_split_amount").val('');
                showToastify("It can\'t be greater than 100%", "error")
                $("#deal_split_percentage").val('');
                return false;
            }
            if (deal_split_percentage > 0) {
                deal_split_amount = (deal_net_commission / 100) * deal_split_percentage;
                $("#deal_split_amount").val(numberFormat(deal_split_amount));
            } else {
                $("#deal_split_amount").val('');
            }
        }
        const calculateSplitPercentage = () => {

            let deal_net_commission = deal?.value?.net_commission
            let deal_split_amount = $("#deal_split_amount").val();
            let deal_split_percentage = $("#deal_split_percentage").val();
            deal_split_amount = deal_split_amount.replace(/,/g, '');
            if(deal_split_amount > deal_net_commission){
                $("#deal_split_percentage").val('');
                $("#deal_split_amount").val('');
                showToastify("It can\'t be greater than net commission", "error")
                return false;

            }
            if (deal_split_amount > 0) {
                deal_split_percentage = (deal_split_amount / deal_net_commission) * 100;
                $("#deal_split_percentage").val(deal_split_percentage.toFixed(2));
            }else{
                $("#deal_split_percentage").val('');
            }
        }
        const deleteDeductible = (id) => {
            Swal.fire({
                html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to remove this fee from this deal ?</p></div></div>',


                showCancelButton: true,
                cancelButtonText: 'Cancel',
                cancelButtonClass: 'btn btn-warning action-close w-xs',

                showCloseButton: true,
                confirmButtonText: 'Delete',
                confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

                buttonsStyling: false,

            }).then(function (result) {
                if (result.isConfirmed) {
                    axios.delete(`/api/vue/v1/deductible-delete/${id}`).then((response) => {

                        refreshFinancialData()
                    })
                }
            })

        }
        $("#finance-deductible_type").on("select2:select", function (e) {

            // let selectedOption = e.params.data;
            let selectedOption = $(this).find("option:selected");
            let value = selectedOption.val();
            let type = selectedOption.attr("data-type");
            let typeValue = selectedOption.attr("data-type_value");
            let deductible_amount = ''
            $("#finance-commission_type").val(type).trigger('change');
            $("#finance-commission_value").val(typeValue);
            if (type == 'percentage') {
                deductible_amount = (deal.value.net_commission / 100) * typeValue;
            } else {
                deductible_amount = typeValue;
            }
            $("#deductible_form_amount").val(numberFormat(deductible_amount));
        });
        function calculateFeeAmount(){
            let type = $("#finance-commission_type").val();
            let typeValue = $("#finance-commission_value").val();
            let deductible_amount = ''
            if (type == 'percentage') {
                deductible_amount = (deal.value.net_commission / 100) * typeValue;
            } else {
                deductible_amount = typeValue;
            }
            $("#deductible_form_amount").val(numberFormat(deductible_amount));
        }
        $("#finance-commission_type").change(function () {
            calculateFeeAmount();
        });
        $("#finance-commission_value").keyup(function () {
            calculateFeeAmount();
        });

        $(document).on("submit", ".dealCommissionForm", function (event) {
            event.preventDefault();
            $("#finanical-commission_saveBtn").prop('disabled', true);
            isValidSubmission = false;
            var formData = new FormData($(this)[0]);
            //formData remove the input filed deal_id
            formData.delete('deal_id');

            formData.append('deal_id', deal.value.id);

            axios.post("{{ route('deal.add.commission') }}", formData)
                .then(response => {

                    if (response.data.status === '1') {
                        isValidSubmission = true;
                        closeCommissionCard();
                        showToastify(response.data.msg, "success");
                        $("#finanical-commission_saveBtn").prop('disabled', false);
                        getDeal(deal.value.id);
                    } else {
                        $("#finanical-commission_saveBtn").prop('disabled', false);
                        isValidSubmission = false;
                        showToastify(response.data.msg, "error");
                    }
                })
                .catch(error => {
                    $("#finanical-commission_saveBtn").prop('disabled', false);
                    isValidSubmission = false;
                    showToastify("Something Went Wrong!", "error");
                    console.error(error);
                });
        });



    </script>

@endpush
