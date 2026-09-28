<?php $__env->startSection('title'); ?>
    AX Calculations
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .hidden {
            display: none;
        }

        .center-tabs {
            display: flex;
            justify-content: center;
        }

        h2 {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#b7846a, #f27a46);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h5 {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#b7846a, #f27a46);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        table tr td {
            padding: 0px !important
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0">AX Calculations </h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">AX</a></li>
                            <li class="breadcrumb-item active">Calculator</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center mb-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="">
                        AX Deal Calculator
                    </h3>
                    <hr>


                    <h5>
                        You are using <span class="ax-text"><span id="calSelector">Secondary Calculator</span></span>
                    </h5>

                    <input type="hidden" class="commission_type_selector" value="percentage">
                    <input type="hidden" class="oqood_commission_type_selector" value="percentage">
                    <input type="hidden" class="roi_purchase_cost_selector" value="percentage">
                    <input type="hidden" class="oqood_paidtodev_selector" value="percentage">

                    


                </div>
            </div>
        </div>
    </div>




    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="nav flex-column nav-pills customActive" id="v-pills-tab" role="tablist"
                        aria-orientation="vertical">

                        <a class="nav-link calSelection mb-2 active" data-calculator="Secondary Calculator"
                            data-val="simple" data-class="basicCalculator" id="v-simple-tab" data-bs-toggle="pill"
                            href="#v-simple" role="tab" aria-controls="v-simple" aria-selected="true">Secondary</a>
                        <a class="nav-link calSelection mb-2" data-calculator="Annual Services Charges Calculator"
                            data-val="annualServiceCharges" data-class="annualServiceChargesCalculator" id="v-asc-tab"
                            data-bs-toggle="pill" href="#v-asc" role="tab" aria-controls="v-asc"
                            aria-selected="false">Service Charges</a>
                        <a class="nav-link calSelection mb-2"data-calculator="ROI Calculator" data-val="roi"
                            data-class="roiCalculator" id="v-roi-tab" data-bs-toggle="pill" href="#v-roi" role="tab"
                            aria-controls="v-roi" aria-selected="false">ROI</a>
                        <a class="nav-link calSelection" data-calculator="Oqood Calculator" data-val="oqood"
                            data-class="oqoodCalculator" id="v-oqood-tab" data-bs-toggle="pill" href="#v-oqood"
                            role="tab" aria-controls="v-oqood" aria-selected="false">Oqood</a>

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body d-grid text-center">
                    
                    <span class="cursor-pointer" style="font-size:20px" data-bs-toggle="modal" data-bs-target="#myModal">
                        Fees <i class=" bx bx-info-circle "></i></span>
                    
                </div>
            </div>
        </div>
        <div class="col-lg-9">

            <div class="card">
                <div class="card-body">
                    <div class="row hideCalculator mt-1 _solid_basicCalculator" id="simpleCalculator">
                        <div class="col-md-12">

                            <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3 text-center" role="tablist">

                                <li class="nav-item calBasicSelection " data-class="ctocCal" data-val="ctoc">
                                    <a class="nav-link  fs-12  active calctoc" data-class="ctocCal" data-val="ctoc"
                                        data-bs-toggle="tab" href="#ctoc" role="tab" aria-selected="false">
                                        CASH TO CASH
                                    </a>
                                </li>
                                <li class="nav-item calBasicSelection ms-1" data-class="mtomCal" data-val="mtom">
                                    <a class="nav-link  fs-12 " data-class="mtomCal" data-val="mtom"
                                        data-bs-toggle="tab" href="#annualServiceCharges" role="tab"
                                        aria-selected="false">
                                        MORTGAGE TO MORTGAGE
                                    </a>
                                </li>
                                <li class="nav-item calBasicSelection ms-1" data-class="mtocCal" data-val="mtoc">
                                    <a class="nav-link  fs-12 " data-class="mtocCal" data-val="mtoc"
                                        data-bs-toggle="tab" href="#roi" role="tab" aria-selected="false">
                                        MORTGAGE (SELLER) TO CASH (BUYER)
                                    </a>
                                </li>
                                <li class="nav-item calBasicSelection ms-1" data-class="ctomCal" data-val="ctom">
                                    <a class="nav-link  fs-12 " data-class="ctomCal" data-val="ctom"
                                        data-bs-toggle="tab" href="#roi" role="tab" aria-selected="false">
                                        CASH (SELLER) TO MORTGAGE (BUYER)
                                    </a>
                                </li>

                            </ul>

                            <hr>
                            <form action="javascript:void(0);" id="simpleCalDnone" class="">
                                <div class="row gy-2 gx-3 mb-3 align-items-center">
                                    <div
                                        class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck ctoc mtom ctom mtoc">
                                        <label class="" for="autoSizingInputGroup">Property Sales Value
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control calculate property_sales_value"
                                                id="property_sales_value" placeholder="Property Sales Value "
                                                step="0.0001" value="0">
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck  d-none">
                                        <label class="" for="payable_to_developer">Payable to Developer</label>
                                        <div class="input-group">
                                            <div class="input-group-text">%</div>
                                            <input type="number" class="form-control calculate percentage_val"
                                                id="payable_to_developer" placeholder="Payable to Developer"
                                                value="0" step="0.0001" min="0" max="100">
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck  d-none">
                                        <label class="" for="paid_to_developer">Paid To Developer</label>
                                        <div class="input-group">
                                            <div class="input-group-text">%</div>
                                            <input type="number" class="form-control calculate percentage_val"
                                                id="paid_to_developer" placeholder="Developer Fee" value="0"
                                                step="0.0001" min="0" max="100">
                                        </div>
                                    </div><!--end col-->


                                    <div
                                        class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck ctoc mtom ctom mtoc">
                                        <label class="" for="autoSizingInputGroup"><i class="bx bx-info-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                data-bs-original-title="DLD Fee for Apartment AED 580 and for Land AED 430."></i>
                                            DLD Fee + AED <span id="dld_updated_value">580</span> <input type="number"
                                                style="width:50px" class=" dld_extra_fee d-none" value="580"
                                                id="dld_extra_fee"> <i class="ri ri-edit-2-line"
                                                id="changeDldvalueEditor"></i></label>
                                        <div class="input-group">
                                            <div class="input-group-text">%</div>
                                            <input type="number"
                                                class="form-control calculate percentage_val secondaryPercentage pvalue"
                                                id="dld_fee" placeholder="DLD Fee" value="0" step="0.0001"
                                                min="0" max="100">
                                        </div>
                                    </div>


                                    <div
                                        class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck ctoc mtom ctom mtoc">
                                        <label class="" for="autoSizingInputGroup">Commission <small><small>( 5% VAT
                                                    Applicable )</small></small></label>
                                        <div class="input-group">
                                            <div class="input-group-text commission_type_set">%</div>
                                            <input type="number"
                                                class="form-control calculate percentage_val_ pvalue_ _secondaryPercentage"
                                                id="commission" placeholder="Commission" value="0" step="0.0001"
                                                min="0" max="100">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">Type</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item ctoc_commission_option" type="button"
                                                        data-value="Manager Cheque">Manager Cheque</a></li>
                                                <li><a class="dropdown-item ctoc_commission_option" type="button"
                                                        data-value="Cash">Cash</a></li>
                                                <li><a class="dropdown-item ctoc_commission_option" type="button"
                                                        data-value="Bank Transfer">Bank Transfer</a></li>
                                            </ul>
                                        </div>
                                    </div><!--end col-->

                                    

                                    <div
                                        class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck ctoc mtom ctom mtoc">
                                        <label class="" for="autoSizingInputGroup">Trustee Fee</label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control calculate" id="trustee_fee"
                                                placeholder="Trustee Fee" value="0" step="0.0001">

                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">Type</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item ctoc_trustee_option" type="button"
                                                        data-value="Cash">Cash</a></li>
                                                <li><a class="dropdown-item ctoc_trustee_option" type="button"
                                                        data-value="Card">Card</a></li>
                                            </ul>
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck d-none">
                                        <label class="" for="autoSizingInputGroup">Premium</label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control calculate" id="premium"
                                                placeholder="Premium" value="0" step="0.0001">
                                        </div>
                                    </div><!--end col-->


                                    <div
                                        class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck mtom  mtoc  d-none">
                                        <label class="" for="blocking_fee">Blocking Fee</label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control calculate" id="blocking_fee"
                                                placeholder="Blocking Fee" value="0" step="0.0001">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">Type</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item ctoc_blocking_option" type="button"
                                                        data-value="Cash">Cash</a></li>
                                                <li><a class="dropdown-item ctoc_blocking_option" type="button"
                                                        data-value="Card">Card</a></li>
                                            </ul>

                                        </div>
                                    </div><!--end col-->

                                    <div
                                        class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck mtom  mtoc  d-none">
                                        <label class="" for="releasing_fee">Release Fee</label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control calculate" id="releasing_fee"
                                                placeholder="Release Fee" value="0" step="0.0001">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">Type</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item ctoc_releasing_option" type="button"
                                                        data-value="Cash">Cash</a></li>
                                                <li><a class="dropdown-item ctoc_releasing_option" type="button"
                                                        data-value="Card">Card</a></li>
                                            </ul>

                                        </div>
                                    </div><!--end col-->

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck   d-none">
                                        <label class="" for="seller_fee">From Seller</label>
                                        <div class="input-group">
                                            <div class="input-group-text">%</div>
                                            <input type="number" class="form-control calculate" id="seller_fee"
                                                placeholder="Fee from Seller" value="0" step="0.0001">
                                        </div>
                                    </div><!--end col-->

                                    <div
                                        class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck ctoc mtom ctom mtoc">
                                        <label class="" for="convincing_fee">Conveyancing Fee</label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control calculate" id="convincing_fee"
                                                placeholder="Conveyancing Fee" value="0" step="0.0001">

                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">Type</button>
                                            <ul class="dropdown-menu dropdown-menu-end">

                                                <li><a class="dropdown-item cf_option" type="button"
                                                        data-value="Cash">Cash</a></li>
                                                <li><a class="dropdown-item cf_option" type="button"
                                                        data-value="Card">Card</a></li>
                                            </ul>
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 basicCalCheck  d-none">
                                        <label class="" for="buyer_fee">From buyer</label>
                                        <div class="input-group">
                                            <div class="input-group-text commission_type_set">%</div>
                                            <input type="number" class="form-control calculate" id="buyer_fee"
                                                placeholder="Fee from buyer" value="0" step="0.0001">
                                        </div>
                                    </div><!--end col-->


                                </div>
                                <div class="row gy-2 gx-3 mb-3 align-items-center" id="appendHere">

                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-end">
                                        <button class="btn btn-primary" id="customModelOpener" type="button">Add Seller
                                            Fee</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row hideCalculator d-none mt-1" id="roiCalculator">
                        <div class="col-md-12">
                            <form action="javascript:void(0);">
                                <div class="row gy-2 gx-3 mb-3 align-items-center">


                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">Gross Rent
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control roiCalculate roi_gross_rent"
                                                id="gross_rent" placeholder="Gross Rent" step="0.0001" value="1">
                                        </div>
                                    </div><!--end col-->


                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">Services Charges
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control roiCalculate roi_service_charges"
                                                id="service_charges" placeholder="Services Charges" step="0.0001"
                                                value="1">
                                        </div>
                                    </div><!--end col-->


                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">Purchasing Value
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control roiCalculate roi_purchase_value"
                                                id="purchase_value" placeholder="Purchasing Value" step="0.0001"
                                                value="1">
                                        </div>
                                    </div><!--end col-->



                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        
                                        <label class="cost_multiplier_label" for="autoSizingInputGroup">Cost Multiplier
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text purchase_cost_set_">%</div>
                                            <input type="number" class="form-control roiCalculate roi_multipler"
                                                id="roi_multipler" placeholder="Profit Rate" step="0.0001"
                                                value="6.25">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>


                    <div class="row hideCalculator d-none mt-1" id="annualServiceChargesCalculator">
                        <div class="col-md-12">
                            <form action="javascript:void(0);">
                                <div class="row gy-2 gx-3 mb-3 align-items-center">

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">PER SQFT
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control ascCalculate asc_sc" id="asc_sc"
                                                placeholder="S.C (P.SQFT)" step="0.0001" value="1">
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">Propety Size
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">SQFT</div>
                                            <input type="number" class="form-control ascCalculate asc_ps" id="asc_ps"
                                                placeholder="Propety Size" step="0.0001" value="1">
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 d-none">
                                        <label class="" for="autoSizingInputGroup">Total Days
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">#</div>
                                            <input type="number" class="form-control ascCalculate asc_notd"
                                                id="asc_notd" placeholder="No Of Days" step="1" value="365">
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">No. Of Days
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">#</div>
                                            <input type="number" class="form-control ascCalculate asc_nod"
                                                id="asc_nod" placeholder="No Of Days" step="1" value="1">
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row hideCalculator d-none mt-1" id="oqoodCalculator">
                        <div class="col-md-12">
                            <form action="javascript:void(0);">
                                <div class="row gy-2 gx-3 mb-3 align-items-center">

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">Original Price
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control oqoodCalculate aqood_op"
                                                id="aqood_op" placeholder="Original Price" step="0.0001"
                                                value="0">
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">Sale Price
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control oqoodCalculate aqood_sp"
                                                id="aqood_sp" placeholder="Sale Price" step="0.0001" value="0">
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">Paid to Developer
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text oqood_paidtodev_set">%</div>
                                            <input type="number"
                                                class="form-control oqoodCalculate aqood_paidtod   pvalue oqoodPercentage"
                                                id="aqood_paidtod" placeholder="Paid to Developer" step="0.01"
                                                value="0">
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3 d-none">
                                        <label class="" for="autoSizingInputGroup">Payable to Developer
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-text">%</div>
                                            <input type="number" class="form-control oqoodCalculate aqood_ptod"
                                                id="aqood_ptod" placeholder="Payable to Developer" step="0.01"
                                                value="0">
                                        </div>

                                    </div>

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">DLD Fee + AED <span
                                                id="oqood_dld_updated_value">40</span> <input type="number"
                                                style="width:50px" class=" oqood_dld_extra_fee d-none" value="40"
                                                id="oqood_dld_extra_fee"> <i class="ri  ri-edit-2-line"
                                                id="oqoodcCangeDldvalueEditor"></i>
                                        </label>

                                        <div class="input-group">
                                            <div class="input-group-text">%</div>
                                            <input type="number"
                                                class="form-control oqoodCalculate aqood_dld oqoodPercentage percentage_val pvalue"
                                                id="aqood_dld" placeholder="DLD Fee" step="0.01" value="0">

                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">Commission <small><small>( 5% VAT
                                                    Applicable )</small></small>
                                        </label>
                                        


                                        <div class="input-group">
                                            <div class="input-group-text oqood_commission_type_set">%</div>
                                            <input type="number"
                                                class="form-control oqoodCalculate aqood_commission percentage_val_ _pvalue _oqoodPercentage"
                                                id="aqood_commission" placeholder="Commission" step="0.01"
                                                value="0">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">Type</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item oqood_commission_option" type="button"
                                                        data-value="Manager Cheque">Manager Cheque</a></li>
                                                <li><a class="dropdown-item oqood_commission_option" type="button"
                                                        data-value="Cash">Cash</a></li>
                                                <li><a class="dropdown-item oqood_commission_option" type="button"
                                                        data-value="Bank Transfer">Bank Transfer</a></li>
                                            </ul>
                                        </div>

                                    </div>

                                    <div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                                        <label class="" for="autoSizingInputGroup">Trustee Fee
                                        </label>
                                        


                                        <div class="input-group">
                                            <div class="input-group-text">AED</div>
                                            <input type="number" class="form-control oqoodCalculate aqood_trustee"
                                                id="aqood_trustee" placeholder="Trustee Fee" step="0.01"
                                                value="0">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">Type</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item oqood_trustee_fee_option" type="button"
                                                        data-value="Card">Card</a></li>
                                                <li><a class="dropdown-item oqood_trustee_fee_option" type="button"
                                                        data-value="Cash">Cash</a></li>
                                            </ul>
                                        </div>

                                    </div>



                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->

        </div>
    </div>











    <div class="row hideCalculations" id="simpleCal">
        <div class="col-12 d-none">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Calculations</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Property Sales Value </th>

                                    <th class="basicCalCheck ctoc mtom ctom mtoc text-center" scope="col">DLD Fee % +
                                        580<br>
                                        <span class="fw-normal" id="show_dld"></span>
                                    </th>
                                    <th class="d-none basicCalCheck text-center" scope="col">
                                        Payable to Developer %<br>
                                        <span class="fw-normal" id="show_ptd"></span>
                                    </th>

                                    <th class="d-none basicCalCheck text-center" scope="col">
                                        Paid To Developer %<br>
                                        <span class="fw-normal" id="show_paidtd"></span>
                                    </th>


                                    <th class="basicCalCheck ctoc mtom ctom mtoc text-center" scope="col">
                                        Commission %<br>
                                        <span class="fw-normal" id="show_com"></span>
                                    </th>
                                    <th class="basicCalCheck ctoc mtom ctom mtoc text-center" scope="col">
                                        VAT %<br>
                                        <span class="fw-normal" id="show_vat"></span>
                                    </th>
                                    <th class="basicCalCheck d-none text-center" scope="col">
                                        Premium AED<br>
                                        <span class="fw-normal" id="show_premium"></span>
                                    </th>

                                    <th class="basicCalCheck ctoc mtom ctom mtoc text-center" scope="col">
                                        Trustee Fee<br>
                                        <span class="fw-normal" id="show_tf"></span>
                                    </th>

                                    <th class="basicCalCheck  mtom  mtoc text-center d-none" scope="col">
                                        Blocking Fee<br>
                                        <span class="fw-normal" id="show_bf"></span>
                                    </th>
                                    <th class="basicCalCheck  mtom  mtoc text-center d-none" scope="col">
                                        Release Fee<br>
                                        <span class="fw-normal" id="show_rf"></span>
                                    </th>

                                    <th class=" text-center" scope="col">
                                        Total Cost <br>
                                        <span class="fw-normal" id="show_tc"></span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <th class="text-nowrap process_sales_value" scope="row" id="process_sales_value">
                                    </th>
                                    <td class="basicCalCheck ctoc mtom ctom mtoc"><span id="process_dld"
                                            class="process_dld"></span></td>
                                    <td class="basicCalCheck d-none"><span id="process_ptd" class="process_ptd"></span>
                                    </td>
                                    <td class="basicCalCheck d-none"><span id="process_paidtd"
                                            class="process_paidtd"></span></td>
                                    <td class="basicCalCheck ctoc mtom ctom mtoc"><span id="process_com"
                                            class="process_com"></span></td>
                                    <td class="basicCalCheck ctoc mtom ctom mtoc"><span id="process_vat"
                                            class="process_vat"></span></td>
                                    <td class="basicCalCheck d-none"><span id="process_premium"
                                            class="process_premium"></span></td>
                                    <td class="basicCalCheck ctoc mtom ctom mtoc"><span id="process_tf"
                                            class="process_tf"></span></td>
                                    <td class="basicCalCheck  mtom  mtoc d-none"><span id="process_bf"
                                            class="process_bf"></span></td>
                                    <td class="basicCalCheck  mtom  mtoc d-none"><span id="process_rf"
                                            class="process_rf"></span></td>
                                    <td class=""><span id="process_ts" class="process_ts"></span></td>

                                </tr>



                            </tbody>
                        </table>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div> <!-- end col -->

        <div class="col-lg-12">
            <div class=" pricing-box ribbon-box ribbon-fill text-center">

                <div class="row">
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-body border-start mt-4 mt-lg-0">
                                <div class="card-header bg-light">
                                    <h5 class="fs-15 mb-0">Breakdown</h5>
                                </div>
                                <div class="card-body pb-0">
                                    <table class="table table-sm table-nowrap text-start">
                                        <tbody id="customCalHolder">
                                            <tr class="d-none basicCalCheck ">
                                                <th scope="row">Payable to Developer:</th>
                                                <td colspan="3">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_ptd">0</span>
                                                </td>

                                            </tr>


                                            <tr class="d-none basicCalCheck ">
                                                <th scope="row">Paid to Developer:</th>
                                                <td colspan="3">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_paidtd">0</span>
                                                </td>
                                            </tr>

                                            <tr class="basicCalCheck ctoc mtom mtoc ctom">
                                                <th scope="row">DLD:</th>
                                                <td colspan="">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_dld">0</span>
                                                </td>
                                                <td>
                                                    (<span class="">Manager Cheque</span>)
                                                </td>
                                            </tr>

                                            <tr class="basicCalCheck ctoc mtom mtoc ctom">
                                                <th scope="row">Commission (+ 5% VAT):</th>
                                                <td colspan="">

                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_com">0</span>
                                                    <small> + AED</small> <span
                                                        class="ax-text_ fw-semibold process_vat">0</span>
                                                    <small> = AED</small> <span
                                                        class="ax-text_ fw-semibold commission_vat">0</span>
                                                </td>
                                                <td>
                                                    (<span class="process_com_option">Manager Cheque</span>)
                                                </td>
                                            </tr>


                                            

                                            <tr class="basicCalCheck ctoc mtom mtoc ctom">
                                                <th scope="row">Trustee Fee:</th>
                                                <td colspan="">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_tf">0</span>
                                                </td>
                                                <td>
                                                    (<span class="process_tf_option">Cash</span>)
                                                </td>
                                            </tr>

                                            <tr class="d-none basicCalCheck mtom mtoc">
                                                <th scope="row">Blocking Fee:</th>
                                                <td colspan="">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_bf">0</span>
                                                </td>
                                                <td>
                                                    (<span class="process_bf_option">Cash</span>)
                                                </td>
                                            </tr>

                                            <tr class="d-none basicCalCheck mtom mtoc">
                                                <th scope="row">Releasing Fee:</th>
                                                <td colspan="">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_rf">0</span>
                                                </td>
                                                <td>
                                                    (<span class="process_rf_option">Cash</span>)
                                                </td>
                                            </tr>

                                            <tr class="basicCalCheck d-none">
                                                <th scope="row">Premium:</th>
                                                <td colspan="3">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_premium">0</span>
                                                </td>
                                            </tr>

                                            <tr class="basicCalCheck d-none">
                                                <th scope="row">From Seller:</th>
                                                <td colspan="3">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_seller_fee">0</span>
                                                </td>
                                            </tr>

                                            <tr class="basicCalCheck d-none">
                                                <th scope="row">From Buyer:</th>
                                                <td colspan="3">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_buyer_fee">0</span>
                                                </td>
                                            </tr>
                                            <tr class="basicCalCheck ctoc mtom mtoc ctom">
                                                <th scope="row">Conveyancing Fee:</th>
                                                <td colspan="">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_convincing_fee">0</span>
                                                </td>
                                                <td>
                                                    (<span class="process_cf_option ">Cash</span>)
                                                </td>

                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body h-100">
                                
                                <div id="property-value-container">
                                    <div>
                                        <h5 class="ax-text">
                                            Property Value
                                            <i id="toggle-icon" class="ri-eye-close-line"
                                                onclick="toggleVisibility()"></i>
                                        </h5>
                                    </div>
                                    <div id="property-details" class="py-1">
                                        <h2 class="primary-text-grad">
                                            <b><small>AED</small> <span
                                                    class="process_sales_value primary-text-grad">0</span></b>
                                        </h2>
                                        (<span class="">Manager Cheque</span>)
                                    </div>
                                </div>

                                <div id="total-cost-container">

                                    <hr>
                                    <div>
                                        <h5 class="ax-text">Total Cost
                                            <i id="toggle-icon-cost" class="ri-eye-close-line"
                                                onclick="costtoggleVisibility()"></i>
                                        </h5>
                                    </div>
                                    <div id="cost-details" class="py-1">
                                        <h2 class="ax-text"><b><small>AED</small> <span class="process_ts">0</span></b>
                                        </h2>
                                    </div>
                                </div>
                                <div id="additionFee">

                                </div>


                                <div class="text-center plan-btn mt-2 d-none">
                                    <a href="javascript:void(0);" id="genChart"
                                        class="btn btn-primary w-sm waves-effect waves-light">Generate Graph</a>
                                </div>
                            </div>
                        </div>
                    </div><!--end col-->


                    

                </div><!--end row-->
            </div>
        </div><!--end row-->
    </div>

    <div class="row d-none hideCalculations" id="roiCal">
        <div class="col-lg-12">
            <div class="pricing-box ribbon-box ribbon-fill text-center">

                <div class="row">



                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-body border-start mt-4 mt-lg-0">
                                <div class="card-header bg-light">
                                    <h5 class="fs-15 mb-0">Breakdown</h5>
                                </div>
                                <div class="card-body pb-0">
                                    <table class="table table-sm table-nowrap text-start">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Services Charges:</th>

                                                <td colspan="3"><small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_roi_service_charges">0</span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th scope="row">Purchasing Value:</th>

                                                <td colspan="3"><small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_roi_purchase_value">0</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Cost Multiplier:</th>

                                                <td colspan="3"><small>%</small> <span
                                                        class="ax-text_ fw-semibold process_roi_multipler">0</span></td>
                                            </tr>

                                            <tr>
                                                <th scope="row">Purchasing Cost:</th>

                                                <td colspan="3"><small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_roi_purchasing_cost">0</span>
                                                    <span class="purchasing_calculations"></span>
                                                </td>
                                            </tr>





                                            <tr>
                                                <th scope="row">Net Rent:</th>
                                                <td colspan="3"><small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_roi_net_rent">0</span></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Total Cost:</th>
                                                <td colspan="3"><small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_roi_total_cost">0</span></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body h-100">
                                <div>
                                    
                                    <h5 class="ax-text">Return On Investment</h5>
                                </div>

                                <div class="py-1">
                                    <h2 class="ax-text"><b><span class="roi_return">0.00</span> <small>%</small></b> </h2>
                                </div>

                                <div class="text-center plan-btn mt-2 d-none">
                                    <a href="javascript:void(0);" id="genRoiChart"
                                        class="btn btn-primary w-sm waves-effect waves-light">Generate Graph</a>
                                </div>
                            </div>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </div>
        </div><!--end row-->
    </div>

    <div class="row d-none hideCalculations" id="annualServiceChargesCal">
        <div class="col-lg-12">
            <div class=" pricing-box ribbon-box ribbon-fill text-center">
                <div class="row">
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-body border-start mt-4 mt-lg-0">
                                <div class="card-header bg-light">
                                    <h5 class="fs-15 mb-0">Breakdown</h5>
                                </div>
                                <div class="card-body pb-0">
                                    <table class="table table-sm table-nowrap text-start">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Service Charge (P.SQFT):</th>

                                                <td colspan="3"><small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_asc_sc">0</span></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Propety Size:</th>
                                                <td colspan="3"> <small>SQFT</small> <span
                                                        class="ax-text_ fw-semibold process_asc_ps">0</span></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">1D SQFT Service Charge:</th>
                                                <td colspan="3"> <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_asc_oneysc">0</span></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">1Y SQFT Service Charge:</th>
                                                <td colspan="3"> <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_asc_oysc">0</span></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Total Days:</th>
                                                <td colspan="3"> <sup><small></small></sup> <span
                                                        class="ax-text_ fw-semibold process_asc_notd">0</span></td>
                                            </tr>

                                            <tr>
                                                <th scope="row">No. of Remaining Days:</th>
                                                <td colspan="3"> <sup><small></small></sup> <span
                                                        class="ax-text_ fw-semibold process_asc_nod">0</span></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Total Amount:</th>
                                                <td colspan="3"> <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_asc_payable">0</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body h-100">
                                <div>
                                    
                                    <h5 class="ax-text">Pro-Rata Balance</h5>
                                </div>

                                <div class="py-1">
                                    <h2 class="ax-text"><b><small>AED</small> <span class="asc_pro">00</span></b></h2>
                                </div>

                                <div class="text-center plan-btn mt-2 d-none">
                                    <a href="javascript:void(0);" id="genAscChart"
                                        class="btn btn-primary w-sm waves-effect waves-light">Generate Graph</a>
                                </div>
                            </div>
                        </div>
                    </div><!--end col-->

                </div><!--end row-->
            </div>
        </div><!--end row-->
    </div>

    <div class="row d-none hideCalculations" id="oqoodCal">
        <div class="col-lg-12">
            <div class="pricing-box ribbon-box ribbon-fill text-center">

                <div class="row ">


                    <div class="col-md-9 ">
                        <div class="card">
                            <div class="card-body border-start mt-4 mt-lg-0">
                                <div class="card-header bg-light">
                                    <h5 class="fs-15 mb-0">Breakdown</h5>
                                </div>
                                <div class="card-body pb-0">
                                    <table class="table table-sm table-nowrap text-start">
                                        <tbody>
                                            


                                            <tr>
                                                <th scope="row">Payable to Developer:</th>
                                                <td colspan="3"> <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_aqood_ptd">0</span> (<span
                                                        class="payablePercentage"></span>%)</td>
                                                
                                            </tr>

                                            <tr>
                                                <th scope="row">DLD Fee:</th>
                                                <td><small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_aqood_dld_value">0</span></td>

                                                <td><span>(<span class=" fw-semibold process_aqood_dld">0</span>% + 40
                                                        <small>AED</small>)
                                                    </span> <span class=""> ( Manager Cheque )</span></td>
                                            </tr>

                                            <tr>
                                                <th scope="row">Commission + 5% VAT:</th>
                                                <td colspan="1">
                                                    <small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_com_only">0</span>
                                                    <small> + AED</small> <span
                                                        class="ax-text_ fw-semibold process_vat_only">0</span>
                                                    <small> = AED</small> <span
                                                        class="ax-text_ fw-semibold process_aqood_commission">0</span>
                                                </td>
                                                <td class="">(<span
                                                        class="process_aqood_commission_type ">Cash</span>)
                                                </td>
                                            </tr>


                                            <tr>
                                                <th scope="row">Trustee Fee:</th>
                                                <td colspan="1"><small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_aqood_trustee">0</span></td>
                                                <td>(<span class="process_aqood_trustee_type ">Cash</span>)
                                                </td>
                                            </tr>


                                            <tr>
                                                <th scope="row">Premium:</th>
                                                <td colspan="1"><small>AED</small> <span
                                                        class="ax-text_ fw-semibold process_aqood_premium">0</span></td>
                                                <td>(<span class="">Manager
                                                        Cheque</span>)&nbsp;&nbsp;&nbsp;<span><b>Paid
                                                            To Developer:</b> &nbsp;&nbsp;</span><small>AED</small>
                                                    <span class="ax-text_ fw-semibold process_aqood_paidtd">0 </span>
                                                    (<span class="paidPercentage"></span>%)
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body h-100">
                                <div>
                                    
                                    <h5 class="ax-text">Seller Receivable</h5>
                                </div>

                                <div class="py-1">
                                    <h2 class="ax-text"><b><small>AED</small> <span class="oqood_sr">00</span></b></h2>
                                </div>

                                <hr>
                                <div>
                                    
                                    <h5 class="ax-text"> Balance PP to Developer</h5>
                                </div>

                                <div class="py-1">
                                    <h2 class="ax-text"><b><small>AED</small> <b><span
                                                    class="oqood_bptod">00</span></b></b></h2>
                                </div>

                                <div class="text-center plan-btn mt-2 d-none">
                                    <a href="javascript:void(0);" id="genOqoodChart"
                                        class="btn btn-primary w-sm waves-effect waves-light">Generate Graph</a>
                                </div>
                            </div>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </div>
        </div><!--end row-->

        <div class="col-lg-4 d-none">

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Chart</h4>
                </div><!-- end card header -->

                <div class="card-body emptySpace">
                    <div id="aqoodChart"
                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                        class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->

        </div>
    </div>


    <!-- Default Modals -->
    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Deal Calculator</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">

                    <h5>Conveyancing Fees</h5>

                    <table class="table table-nowrap">
                        <tbody>
                            <tr>
                                <td><span>Cash to Cash deal</span></td>
                                <td>AED 3150</td>
                            </tr>
                            <tr>
                                <td><span>Mortgage to Mortgage deal</span></td>
                                <td>AED 5250</td>
                            </tr>
                            <tr>
                                <td><span> Mortgage seller to Cash buyer deal</span></td>
                                <td>AED 5250</td>
                            </tr>
                            <tr>
                                <td><span>Cash seller to Mortgage buyer deal</span></td>
                                <td>AED 3150</td>
                            </tr>
                            <tr>
                                <td><span>Oqood deal</span></td>
                                <td>AED 3150</td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <h5>Trustee Fees</h5>

                    <table class="table table-nowrap">
                        <tbody>
                            <tr>
                                <td><span>For Ready Deal up to AED 500k</span></td>
                                <td> AED 2100 </td>
                            </tr>
                            <tr>
                                <td><span>For Ready Deal above AED 500k</span></td>
                                <td>AED 4200</td>
                            </tr>

                            <tr>
                                <td><span>Oqood Deal (Off Plan Re-Sale) up to 500k </span></td>
                                <td>AED 3650</td>
                            </tr>
                            <tr>
                                <td><span>Oqood (Off Plan Re-Sale) above AED 500k</span></td>
                                <td>AED 5250</td>
                            </tr>
                        </tbody>
                    </table>
                    <h5>Title deed Fees</h5>

                    <table class="table table-nowrap">
                        <tbody>
                            <tr>
                                <td><span>Land</span></td>
                                <td>AED 430</td>
                            </tr>
                            <tr>
                                <td><span>Apartment</span></td>
                                <td>AED 580</td>
                            </tr>
                            <tr>
                                <td><span>Oqqod</span></td>
                                <td>AED 40</td>
                            </tr>
                        </tbody>
                    </table>

                    <h5>Other Fee</h5>

                    <table class="table table-nowrap">
                        <tbody>
                            <tr>
                                <td><span>Blocking Fees</span></td>
                                <td>AED 1545 (Buyer) </td>
                            </tr>
                            <tr>
                                <td><span>Mortgage Release Fees</span></td>
                                <td>AED 1605 (Seller)</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



    <!-- Default Modals -->
    <div id="customModal" class="modal fade" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="customModalLabel">Field Creator</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">

                    <form action="">
                        <div class="mb-3">
                            <label for="prefic-name" class="form-label">Prefix</label>
                            <input type="text" class="form-control" id="prefix" value="AED"
                                placeholder="Enter Prefix">
                        </div>
                        <div class="mb-3">
                            <label for="label-name" class="form-label">Input Label</label>
                            <input type="text" class="form-control" id="label" placeholder="Enter Label">
                        </div>

                        <button class="btn btn-primary" id="insertInput" type="button">Insert</button>
                        <button class="btn btn-primary" id="insertBlockingFee" type="button">Add Blocking Fees</button>
                        <button class="btn btn-primary" id="insertMRF" type="button">Mortgage Release Fees</button>
                    </form>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/libs/apexcharts/apexcharts.min.js')); ?>"></script>



    <script>
        /* Configuration */

        /* Simple */
        var property_sales_value = 0;
        var payable_to_developer = 0;
        var paid_to_developer = 0;
        var dld_fee = 0;
        var commission = 0;
        var vat = 0;
        var dld_extra_fee = 0;

        var commission_vat = 0;
        var trustee_fee = 0;
        var blocking_fee = 0;
        var releasing_fee = 0;
        var premium = 0;
        var process_dld = 0;
        var process_ptd = 0;
        var process_paidtd = 0;
        var process_com = 0;
        var process_vat = 0;
        var process_tf = 0;
        var process_bf = 0;
        var process_rf = 0;
        var process_premium = 0;
        var process_ts = 0;
        var seller_fee = 0;
        var buyer_fee = 0;
        var process_seller_fee = 0;
        var process_buyer_fee = 0;
        var convincing_fee = 0;



        /* ROI */
        var roi_gross_rent = 0;
        var roi_service_charges = 0;
        var roi_purchase_value = 0;
        var roi_purchasing_cost = 0;
        var roi_process_purchasing_cost = 0;
        var roi_multipler = 0;
        var roi_net_rent = 0;
        var roi_total_cost = 0;
        var roi_return = 0;

        /* ASC */
        var asc_sc = 0;
        var asc_ps = 0;
        var asc_nod = 0;
        var asc_payable = 0;
        var asc_pro = 0;
        var asc_notd = 0;
        var asc_tsc = 0;


        /* OQOOD */
        var aqood_op = 0;
        var aqood_sp = 0;

        var peraqood_paidtod = 0;
        var peraqood_ptod = 0;

        var aqood_paidtod = 0;
        var aqood_ptod = 0;

        var aqood_dld = 0;
        var aqood_trustee = 0;
        var oqood_sr = 0;
        var oqood_bptod = 0;
        var oqood_premium = 0;
        var oqood_commission = 0;

        var process_oqood_commission = 0;
        var process_oqood_vat = 0;


        var oqood_dld_value = 0;
        var oqood_dld_extra_fee = 0;
        var oqood_seller_receivable = 0;
        var oqood_balance_pp_to_developer = 0;

        var payable_percentage = 0;
        var paid_percentage = 0;

        $(document).ready(function() {
            $('.dropdown-item').click(function() {

            })


            $('.calBasicSelection').click(function() {
                let type = $(this).data('val');
                $('.basicCalCheck').addClass('d-none');
                $('.calculate').val(0)
                $('.' + type).removeClass('d-none');
                // $('#simpleCalDnone').removeClass('d-none');
            })
            $(document).on('keyup', '.calculate', function() {
                secondaryCalculator();
            })

            $(document).on('keyup', '.roiCalculate', function() {

                roiCalculator();
            })
            $(document).on('keyup', '.ascCalculate', function() {

                ascCalculator();
            })
            $(document).on('keyup', '.oqoodCalculate', function() {
                oqoodCalculator()
            })

            $('.commission_type_selector').change(function() {
                let commission_type_selector = $(this).val();
                if (commission_type_selector == 'fixed') {
                    $('.commission_type_set').removeClass('percentage_val');
                    $('.commission_type_set').html('AED');
                }
                if (commission_type_selector == 'percentage') {
                    $('.commission_type_set').addClass('percentage_val');
                    $('.commission_type_set').html('%');
                }
            });


            $('.commission_type_set').click(function() {

                if ($(this).html() == '%') {
                    $(this).html('AED');
                    $('.commission_type_selector').val('fixed');
                    $('.commission_type_set').removeClass('percentage_val');
                } else if ($(this).html() == 'AED') {
                    $(this).html('%');
                    $('.commission_type_selector').val('percentage');
                    $('.commission_type_set').removeClass('percentage_val');
                }
                secondaryCalculator();
            })

            $('.oqood_commission_type_set').click(function() {

                if ($(this).html() == '%') {
                    $(this).html('AED');
                    $('.oqood_commission_type_selector').val('fixed');
                    $('.oqood_commission_type_set').removeClass('percentage_val pvalue');
                } else if ($(this).html() == 'AED') {
                    $(this).html('%');
                    $('.oqood_commission_type_selector').val('percentage');
                    $('.oqood_commission_type_set').removeClass('percentage_val');
                }
                oqoodCalculator();
            })

            $('.purchase_cost_set').click(function() {

                if ($(this).html() == '%') {
                    $(this).html('AED');
                    $('.roi_purchase_cost_selector').val('fixed');
                    $('.purchase_cost_set').removeClass('percentage_val');
                } else if ($(this).html() == 'AED') {
                    $(this).html('%');
                    $('.roi_purchase_cost_selector').val('percentage');
                    $('.purchase_cost_set').removeClass('percentage_val');
                }
                oqoodCalculator();
            })


            $('.oqood_paidtodev_set').click(function() {

                if ($(this).html() == '%') {
                    $(this).html('AED');
                    $('.oqood_paidtodev_selector').val('fixed');
                    $('.oqood_paidtodev_set').removeClass('percentage_val');
                } else if ($(this).html() == 'AED') {
                    $(this).html('%');
                    $('.oqood_paidtodev_selector').val('percentage');
                    $('.oqood_paidtodev_set').removeClass('percentage_val');
                }
                oqoodCalculator();
            })

        })




        $('.percentage_val').change(function() {
            var percentage_val = parseFloat($(this).val());
            if (percentage_val > 100) {
                $(this).val(100);
                if ($(this).hasClass('oqoodPercentage')) {
                    oqoodCalculator();
                }
                if ($(this).hasClass('secondaryPercentage')) {
                    secondaryCalculator();
                }
            }
        })




        $('#changeDldvalueEditor').click(function() {

            $('.dld_extra_fee').removeClass('d-none');
            $('#dld_updated_value').addClass('d-none');
            $('#changeDldvalueEditor').addClass('d-none');

        })

        $('.dld_extra_fee').change(function() {
            $('#dld_updated_value').html($(this).val());

            $('.dld_extra_fee').addClass('d-none');
            $('#dld_updated_value').removeClass('d-none');
            $('#changeDldvalueEditor').removeClass('d-none');
            secondaryCalculator();
        })



        $('#oqoodcCangeDldvalueEditor').click(function() {

            $('.oqood_dld_extra_fee').removeClass('d-none');
            $('#oqood_dld_updated_value').addClass('d-none');
            $('#oqoodcCangeDldvalueEditor').addClass('d-none');

        })

        $('.oqood_dld_extra_fee').change(function() {
            $('#oqood_dld_updated_value').html($(this).val());

            $('.oqood_dld_extra_fee').addClass('d-none');
            $('#oqood_dld_updated_value').removeClass('d-none');
            $('#oqoodcCangeDldvalueEditor').removeClass('d-none');
            oqoodCalculator();
        })




        /*$('.calculate').keyup(function() {
            var calculate_val = parseFloat($(this).val());
            if (isNaN(calculate_val)) {
                $(this).val(0);
            }
        })


        $('.roiCalculate').keyup(function() {
            var calculate_val = parseFloat($(this).val());
            if (isNaN(calculate_val)) {
                $(this).val(1);
            }
        })*/


        $('#payable_to_developer').change(function() {
            var payable_to_developer = parseFloat($(this).val());
            var paid_to_developer = parseFloat($('#paid_to_developer').val());
            if (payable_to_developer > 100) {
                $(this).val(100);
            }
            if (payable_to_developer > 0) {
                $('#paid_to_developer').val(100 - payable_to_developer);
            }
        })


        $('#paid_to_developer').change(function() {
            var paid_to_developer = parseFloat($(this).val());
            var payable_to_developer = parseFloat($('#payable_to_developer').val());
            if (paid_to_developer > 100) {
                $(this).val(100);
            }
            if (paid_to_developer > 0) {
                $('#payable_to_developer').val(100 - paid_to_developer);
            }

        })

        $('.classSelection').click(function() {
            $('.classSelection').removeClass(
                'basicCalculator annualServiceChargesCalculator roiCalculator oqoodCalculator');
            var cn_class = $(this).data('class')
            $(this).addClass(cn_class);
            let _val = $(this).attr('data-val');
            $('#' + _val + 'Calculator').addClass('solid_' + cn_class);
        })

        $('.calSelection').click(function() {
            /*basicCalculator  annualServiceChargesCalculator  roiCalculator oqoodCalculator*/
            var val = $(this).attr('data-val');
            let calculator = $(this).attr('data-calculator');
            $('.calSelection').removeClass('active');
            $(this).addClass('active');
            $('#calSelector').text(calculator);
            $('.hideCalculations').addClass('d-none');
            $('.hideCalculator').addClass('d-none');
            $('#' + val + 'Cal').removeClass('d-none');
            $('#' + val + 'Calculator').removeClass('d-none');

        });









        $('.oqood_commission_option').click(function() {
            let type = $(this).data('value');
            $('.process_aqood_commission_type').html(type);
        });

        $('.oqood_trustee_fee_option').click(function() {
            let type = $(this).data('value');
            $('.process_aqood_trustee_type').html(type);
        })


        $('.ctoc_trustee_option').click(function() {
            let type = $(this).data('value');
            $('.process_tf_option').html(type);
        });
        $('.ctoc_blocking_option').click(function() {
            let type = $(this).data('value');
            $('.process_bf_option').html(type);
        })

        $('.ctoc_releasing_option').click(function() {
            let type = $(this).data('value');
            $('.process_rf_option').html(type);
        });

        $('.ctoc_commission_option').click(function() {
            let type = $(this).data('value');
            $('.process_com_option').html(type);
        })

        $('.cf_option').click(function() {
            let type = $(this).data('value');
            $('.process_cf_option').html(type);
        })




        $('#genChart').click(function() {
            var options1 = {
                series: [property_sales_value, process_dld, process_com, process_vat,
                    process_premium, process_tf, process_bf, process_rf
                ],
                colors: ['#ffb822', '#249EFA', '#1dc9b7', '#fd397a', '#5578eb', '#1dc9b7',
                    '#fd397a',
                    '#5578eb'
                ],
                chart: {

                    type: 'donut',
                    width: 300
                },
                dataLabels: {
                    enabled: false
                },
                labels: ['Property Sales Value', 'DLD Fee', 'Commission', 'VAT', 'Premium',
                    'Trustee Fee',
                    'Blocking Fee', 'Release Fee'
                ],
                legend: {
                    show: false,
                },
                fill: {
                    type: 'gradient',
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                tooltip: {

                },
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '22px',
                                    offsetY: -5
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    color: undefined,
                                    offsetY: +5,
                                    formatter: function(val) {
                                        return val
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: '#ffa500',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0)
                                    }
                                }
                            }
                        }
                    }
                }
            };
            var chart1 = new ApexCharts(document.querySelector("#simple_dount_chart"), options1);
            chart1.render();
        })

        $('#genRoiChart').click(function() {
            var options1 = {
                series: [roi_gross_rent, roi_service_charges, roi_purchase_value,
                    roi_process_purchasing_cost, roi_net_rent, roi_total_cost
                ],
                colors: ['#ffb822', '#249EFA', '#1dc9b7', '#fd397a', '#5578eb', '#1dc9b7',
                    '#fd397a',
                    '#5578eb'
                ],
                chart: {

                    type: 'donut',
                    width: 300
                },
                dataLabels: {
                    enabled: false
                },
                labels: ['Gross Rent', 'Services Charges', 'Purchasing Value', 'Purchasing Cost',
                    'Net Rent', 'Total Cost'
                ],
                legend: {
                    show: false,
                },
                fill: {
                    type: 'gradient',
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                tooltip: {

                },
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '22px',
                                    offsetY: -5
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    color: undefined,
                                    offsetY: +5,
                                    formatter: function(val) {
                                        return val
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'ROI',
                                    color: '#ffa500',
                                    formatter: function(w) {
                                        return formatNumberWithCommas(roi_return) + '%';
                                    }

                                }
                            }
                        }
                    }
                }
            };
            var chart1 = new ApexCharts(document.querySelector("#roiChart"), options1);
            chart1.render();
        })

        $('#genAscChart').click(function() {
            var options1 = {
                series: [asc_sc, asc_ps, asc_nod, asc_payable, asc_pro],
                colors: ['#ffb822', '#249EFA', '#1dc9b7', '#fd397a', '#5578eb', '#1dc9b7',
                    '#fd397a',
                    '#5578eb'
                ],
                chart: {

                    type: 'donut',
                    width: 300
                },
                dataLabels: {
                    enabled: false
                },
                labels: ['S.C (P.SQFT)', 'Propety Size', 'No Of Days', 'Total Paid/Payable',
                    'Pro-Rata Balance'
                ],
                legend: {
                    show: false,
                },
                fill: {
                    type: 'gradient',
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                tooltip: {

                },
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '22px',
                                    offsetY: -5
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    color: undefined,
                                    offsetY: +5,
                                    formatter: function(val) {
                                        return val
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Pro-Rata Balance',
                                    color: '#ffa500',
                                    formatter: function(w) {
                                        return formatNumberWithCommas(asc_pro);
                                    }

                                }
                            }
                        }
                    }
                }
            };
            var chart1 = new ApexCharts(document.querySelector("#ascChart"), options1);
            chart1.render();
        })

        $('#genOqoodChart').click(function() {
            var options1 = {
                series: [aqood_op, aqood_sp, aqood_paidtod, aqood_ptod, aqood_dld, aqood_trustee],
                colors: ['#ffb822', '#249EFA', '#1dc9b7', '#fd397a', '#5578eb', '#1dc9b7',
                    '#fd397a',
                    '#5578eb'
                ],
                chart: {

                    type: 'donut',
                    width: 300
                },
                dataLabels: {
                    enabled: false
                },
                labels: ['Original Price', 'Selling Price', 'Paid To Developer',
                    'Payable to Developer',
                    'DLD Fee', 'Trustee Fee'
                ],
                legend: {
                    show: false,
                },
                fill: {
                    type: 'gradient',
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                tooltip: {

                },
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '22px',
                                    offsetY: -5
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    color: undefined,
                                    offsetY: +5,
                                    formatter: function(val) {
                                        return val
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Selling Price',
                                    color: '#ffa500',
                                    formatter: function(w) {
                                        return formatNumberWithCommas(aqood_sp);
                                    }

                                }
                            }
                        }
                    }
                }
            };
            var chart1 = new ApexCharts(document.querySelector("#aqoodChart"), options1);
            chart1.render();
        })

        $("#axCalculator").addClass('active');



        function formatNumberWithCommas(number) {
            // Check if the input is a valid number
            if (isNaN(number)) {
                return "Invalid Number";
            }
            return number.toLocaleString();
        }


        function oqoodCalculator() {
            $('.oqood_dld_extra_fee').addClass('d-none');
            $('#oqood_dld_updated_value').removeClass('d-none');
            $('#oqoodcCangeDldvalueEditor').removeClass('d-none');

            aqood_op = parseFloat($('#aqood_op').val());
            aqood_sp = parseFloat($('#aqood_sp').val());
            peraqood_paidtod = parseFloat($('#aqood_paidtod').val());
            peraqood_ptod = parseFloat($('#aqood_ptod').val());
            aqood_dld = parseFloat($('#aqood_dld').val());
            aqood_trustee = parseFloat($('#aqood_trustee').val());
            oqood_commission = parseFloat($('#aqood_commission').val());
            oqood_dld_extra_fee = parseFloat($('#oqood_dld_extra_fee').val());

            if (isNaN(aqood_op)) {
                aqood_op = 0;
            }
            if (isNaN(aqood_sp)) {
                aqood_sp = 0;
            }
            if (isNaN(peraqood_paidtod)) {
                peraqood_paidtod = 0;
            }
            if (isNaN(peraqood_ptod)) {
                peraqood_ptod = 0;
            }
            if (isNaN(aqood_dld)) {
                aqood_dld = 0;
            }
            if (isNaN(aqood_trustee)) {
                aqood_trustee = 0;
            }
            if (isNaN(oqood_commission)) {
                oqood_commission = 0;
            }
            if (isNaN(oqood_dld_extra_fee)) {
                oqood_dld_extra_fee = 0;
            }

            oqood_paidtodev_selector = $('.oqood_paidtodev_selector').val();
            if (oqood_paidtodev_selector == 'fixed') {

                aqood_paidtod = peraqood_paidtod;
                aqood_ptod = aqood_op - aqood_paidtod;

            }

            if (oqood_paidtodev_selector == 'percentage') {
                aqood_paidtod = (aqood_op * peraqood_paidtod) / 100;
                aqood_ptod = aqood_op - aqood_paidtod;


            }

            payable_percentage = (aqood_ptod * 100) / aqood_op;
            paid_percentage = (aqood_paidtod * 100) / aqood_op;

            if (isNaN(payable_percentage)) {
                payable_percentage = 0;
            }
            if (isNaN(paid_percentage)) {
                paid_percentage = 0;
            }

            oqood_premium = aqood_sp - aqood_op;


            oqood_seller_receivable = oqood_premium + aqood_paidtod;
            oqood_balance_pp_to_developer = aqood_paidtod - aqood_op;

            oqood_dld_value = ((aqood_sp * aqood_dld) / 100) + oqood_dld_extra_fee;


            commission_type_selector = $('.oqood_commission_type_selector').val();
            if (commission_type_selector == 'fixed') {
                process_oqood_commission = oqood_commission;
            }
            if (commission_type_selector == 'percentage') {
                process_oqood_commission = ((aqood_sp * oqood_commission) / 100);
            }




            process_oqood_vat = process_oqood_commission * 5 / 100;

            oqood_commission = process_oqood_commission + process_oqood_vat;

            $('.oqood_sr').html(formatNumberWithCommas(oqood_seller_receivable));
            $('.oqood_bptod').html(formatNumberWithCommas(oqood_balance_pp_to_developer));
            $('.process_aqood_paidtd').html(formatNumberWithCommas(aqood_paidtod));
            $('.process_aqood_ptd').html(formatNumberWithCommas(aqood_ptod));
            $('.process_aqood_dld').html(formatNumberWithCommas(aqood_dld));
            $('.process_aqood_dld_value').html(formatNumberWithCommas(oqood_dld_value));
            $('.process_aqood_trustee').html(formatNumberWithCommas(aqood_trustee));
            $('.process_aqood_premium').html(formatNumberWithCommas(oqood_premium));

            $('.process_com_only').html(formatNumberWithCommas(process_oqood_commission));
            $('.process_vat_only').html(formatNumberWithCommas(process_oqood_vat));
            $('.process_aqood_commission').html(formatNumberWithCommas(oqood_commission));
            $('.paidPercentage').html(paid_percentage.toFixed(2));
            $('.payablePercentage').html(payable_percentage.toFixed(2));
        }

        function secondaryCalculator() {
            $('.dld_extra_fee').addClass('d-none');
            $('#dld_updated_value').removeClass('d-none');
            $('#changeDldvalueEditor').removeClass('d-none');

            property_sales_value = parseFloat($('#property_sales_value').val());
            payable_to_developer = parseFloat($('#payable_to_developer').val());
            paid_to_developer = parseFloat($('#paid_to_developer').val());
            dld_fee = parseFloat($('#dld_fee').val());
            commission = parseFloat($('#commission').val());
            vat = 5;
            dld_extra_fee = parseFloat($('#dld_extra_fee').val());
            trustee_fee = parseFloat($('#trustee_fee').val());
            blocking_fee = parseFloat($('#blocking_fee').val());
            releasing_fee = parseFloat($('#releasing_fee').val());
            premium = parseFloat($('#premium').val());
            seller_fee = parseFloat($('#seller_fee').val());
            buyer_fee = parseFloat($('#buyer_fee').val());
            convincing_fee = parseFloat($('#convincing_fee').val());

            if (isNaN(property_sales_value)) {
                property_sales_value = 0;
            }
            if (isNaN(payable_to_developer)) {
                payable_to_developer = 0;
            }
            if (isNaN(paid_to_developer)) {
                paid_to_developer = 0;
            }
            if (isNaN(dld_fee)) {
                dld_fee = 0;
            }
            if (isNaN(commission)) {
                commission = 0;
            }
            if (isNaN(vat)) {
                vat = 0;
            }
            if (isNaN(trustee_fee)) {
                trustee_fee = 0;
            }
            if (isNaN(blocking_fee)) {
                blocking_fee = 0;
            }
            if (isNaN(releasing_fee)) {
                releasing_fee = 0;
            }
            if (isNaN(premium)) {
                premium = 0;
            }
            if (isNaN(seller_fee)) {
                seller_fee = 0;
            }
            if (isNaN(buyer_fee)) {
                buyer_fee = 0;
            }
            if (isNaN(dld_extra_fee)) {
                dld_extra_fee = 0;
            }
            if (isNaN(convincing_fee)) {
                convincing_fee = 0;
            }




            $('#show_dld').html(dld_fee);
            $('#show_com').html(commission);
            $('#show_vat').html(vat);
            $('#show_tf').html(trustee_fee);
            $('#show_bf').html(blocking_fee);
            $('#show_rf').html(releasing_fee);
            $('#show_ptd').html(payable_to_developer);
            $('#show_paidtd').html(paid_to_developer);
            $('#show_premium').html(premium);



            process_dld = (property_sales_value * dld_fee) / 100 + dld_extra_fee;
            process_ptd = (property_sales_value * payable_to_developer) / 100;
            process_paidtd = (property_sales_value * paid_to_developer) / 100;

            commission_type_selector = $('.commission_type_selector').val();

            if (commission_type_selector == 'fixed') {
                process_com = commission;
            }
            if (commission_type_selector == 'percentage') {
                process_com = (property_sales_value * commission) / 100;
            }

            // process_com = (property_sales_value * commission) / 100;


            process_vat = (process_com * vat) / 100;
            commission_vat = process_com + process_vat;
            process_tf = trustee_fee;
            process_bf = blocking_fee;
            process_rf = releasing_fee;
            process_premium = premium;
            process_seller_fee = (property_sales_value * seller_fee) / 100;
            process_buyer_fee = (property_sales_value * buyer_fee) / 100;


            process_ts = property_sales_value + process_dld + process_com + process_vat + process_tf +
                process_bf + process_rf + process_premium + convincing_fee;

            $('.process_sales_value').html(formatNumberWithCommas(property_sales_value));
            $('.process_dld').html(formatNumberWithCommas(process_dld));
            $('.process_ptd').html(formatNumberWithCommas(process_ptd));
            $('.process_paidtd').html(formatNumberWithCommas(process_paidtd));
            $('.process_com').html(formatNumberWithCommas(process_com));
            $('.process_vat').html(formatNumberWithCommas(process_vat));
            $('.commission_vat').html(formatNumberWithCommas(commission_vat));
            $('.process_premium').html(formatNumberWithCommas(process_premium));
            $('.process_tf').html(formatNumberWithCommas(process_tf));
            $('.process_ts').html(formatNumberWithCommas(process_ts));
            $('.process_bf').html(formatNumberWithCommas(process_bf));
            $('.process_rf').html(formatNumberWithCommas(process_rf));
            $('.process_seller_fee').html(formatNumberWithCommas(process_seller_fee));
            $('.process_buyer_fee').html(formatNumberWithCommas(process_buyer_fee));
            $('.process_convincing_fee').html(formatNumberWithCommas(convincing_fee));

            // run foreach .checques and divide  property_sales_value by data-number and show result
            $('.checques').each(function() {
                var number = $(this).attr('data-number');
                var result = process_ts / number;
                $(this).html(formatNumberWithCommas(result));
            })


            $('#simpleCal').removeClass('d-none');

            sellerCount()
        }


        function roiCalculator() {
            roi_gross_rent = parseFloat($('#gross_rent').val());
            roi_service_charges = parseFloat($('#service_charges').val());
            roi_purchase_value = parseFloat($('#purchase_value').val());

            roi_multipler = parseFloat($('#roi_multipler').val());

            if (isNaN(roi_gross_rent)) {
                roi_gross_rent = 1;
            }
            if (isNaN(roi_service_charges)) {
                roi_service_charges = 1;
            }
            if (isNaN(roi_purchase_value)) {
                roi_purchase_value = 1;
            }
            if (isNaN(roi_multipler)) {
                roi_multipler = 1;
            }


            roi_process_purchasing_cost = roi_purchase_value * (roi_multipler / 100);

            roi_total_cost = roi_purchase_value + roi_process_purchasing_cost;


            roi_net_rent = roi_gross_rent - roi_service_charges;

            roi_return = (roi_net_rent / roi_total_cost) * 100;
            let purchasing_calculations = '  <b>( ' + roi_purchase_value + ' * ' + roi_multipler + ' / 100 )</b>';
            $('.roi_return').html(roi_return.toFixed(2));
            $('.process_roi_service_charges').html(formatNumberWithCommas(roi_service_charges));
            $('.process_roi_purchase_value').html(formatNumberWithCommas(roi_purchase_value));
            $('.process_roi_purchasing_cost').html(formatNumberWithCommas(roi_process_purchasing_cost));
            $('.process_roi_multipler').html(formatNumberWithCommas(roi_multipler));
            $('.process_roi_net_rent').html(formatNumberWithCommas(roi_net_rent));
            $('.process_roi_total_cost').html(formatNumberWithCommas(roi_total_cost));
            $('.purchasing_calculations').html(purchasing_calculations);


        }


        function ascCalculator() {
            asc_sc = parseFloat($('#asc_sc').val());
            asc_ps = parseFloat($('#asc_ps').val());
            asc_nod = parseFloat($('#asc_nod').val());
            asc_notd = parseFloat($('#asc_notd').val());

            if (isNaN(asc_sc)) {
                asc_sc = 1;
            }
            if (isNaN(asc_ps)) {
                asc_ps = 1;
            }
            if (isNaN(asc_nod)) {
                asc_nod = 1;
            }
            if (isNaN(asc_notd)) {
                asc_notd = 1;
            }

            let service_charge = asc_sc * asc_ps;
            let y_service_charge = asc_sc * asc_ps * asc_notd;
            let asc_payable = service_charge * asc_nod;
            let new_pro = (service_charge / asc_notd) * asc_nod
            console.log(new_pro)






            $('.asc_pro').html(formatNumberWithCommas(new_pro));
            $('.process_asc_sc').html(formatNumberWithCommas(asc_sc));
            $('.process_asc_ps').html(formatNumberWithCommas(asc_ps));
            $('.process_asc_nod').html(formatNumberWithCommas(asc_nod));
            $('.process_asc_notd').html(formatNumberWithCommas(asc_notd));
            $('.process_asc_payable').html(formatNumberWithCommas(asc_payable));
            $('.process_asc_oneysc').html(formatNumberWithCommas(service_charge));
            $('.process_asc_oysc').html(formatNumberWithCommas(y_service_charge));


        }



        $('#customModelOpener').click(function() {
            $('#customModal').modal('show');
        });

        $('#insertBlockingFee').click(function() {
            let prefix = 'AED';
            let label = 'Blocking Fee';
            let value = '1545';

            labelid = label.replace(/\s/g, '');

            let input = `<div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                            <label class="" for="autoSizingInputGroup">${label} (Seller Fee) <i class="text-danger ri  ri-delete-bin-7-line removeSellerFee" ></i></label>
                            <div class="input-group">
                                <div class="input-group-text">${prefix}</div>
                                <input type="number" class="form-control customCals calculate"
                                    id="${labelid}" placeholder="${label}" step="0.0001"
                                    value="${value}">
                            </div>
                        </div>`;
            $('#label').val('');
            $('#prefix').val('AED');

            $('#customModal').modal('hide');
            $('#appendHere').append(input);
        });
        $('#insertMRF').click(function() {
            let prefix = 'AED';
            let label = 'Mortgage Release Fees';
            let value = '1605';

            labelid = label.replace(/\s/g, '');

            let input = `<div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                            <label class="" for="autoSizingInputGroup">${label} (Seller Fee) <i class="text-danger ri  ri-delete-bin-7-line removeSellerFee" ></i></label>
                            <div class="input-group">
                                <div class="input-group-text">${prefix}</div>
                                <input type="number" class="form-control customCals calculate"
                                    id="${labelid}" placeholder="${label}" step="0.0001"
                                    value="${value}">
                            </div>
                        </div>`;
            $('#label').val('');
            $('#prefix').val('AED');

            $('#customModal').modal('hide');
            $('#appendHere').append(input);
        });

        $('#insertInput').click(function() {
            let prefix = $('#prefix').val();
            let label = $('#label').val();

            labelid = label.replace(/\s/g, '');

            let input = `<div class="col-sm-12 col-md-4 col-lg-4 col-xxs-12 mt-3">
                            <label class="" for="autoSizingInputGroup">${label} (Seller Fee) <i class="text-danger ri  ri-delete-bin-7-line removeSellerFee" ></i></label>
                            <div class="input-group">
                                <div class="input-group-text">${prefix}</div>
                                <input type="number" class="form-control customCals calculate"
                                    id="${labelid}" placeholder="${label}" step="0.0001"
                                    value="0">
                            </div>
                        </div>`;
            $('#label').val('');
            $('#prefix').val('AED');

            $('#customModal').modal('hide');
            $('#appendHere').append(input);
        });

        function sellerCount() {
            // remove all seller fee
            $('.sellerfee').remove();
            $('#additionFee').html('');
            let total = 0;
            $('.customCals').each(function() {
                let val = parseFloat($(this).val());
                total += val;
                let label = $(this).attr('placeholder');
                let prefix = $(this).prev().html();
                let input = `<tr class="sellerfee">
                                <th scope="row">${label}:</th>
                                <td colspan="3"> <small>${prefix}</small>
                                    <span class="ax-text_ fw-semibold">${val}</span></td>
                            </tr>`;
                $('.custom-totalcost').html(total);
                let invoice = $('#invoice-for').val();
                $('#invoice-for-who').html(invoice);

                $('.custom-property-value').html($('#property-value').val());
                $('#customCalHolder').append(input);

                let sellerfeediv = `<hr><div>
                                    <h5 class="ax-text">Seller Fee
                                        <i id="toggle-icon-seller" class="ri-eye-close-line"
                                                onclick="sellertoggleVisibility()"></i>
                                        </h5>
                                </div>
                                <div id="seller-details" class="py-1">
                                    <h2 class="ax-text"><b><small>AED</small> <span class="process_ts">${total}</span></b></h2>
                                </div>`
                $('#additionFee').html(sellerfeediv);


            });
        }

        function toggleVisibility() {
            var details = document.getElementById('property-details');
            var icon = document.getElementById('toggle-icon');

            if (details.classList.contains('hidden')) {
                details.classList.remove('hidden');
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-close-line');
            } else {
                details.classList.add('hidden');
                icon.classList.remove('ri-eye-close-line');
                icon.classList.add('ri-eye-line');
            }
        }
        function costtoggleVisibility() {
            var details = document.getElementById('cost-details');
            var icon = document.getElementById('toggle-icon-cost');

            if (details.classList.contains('hidden')) {
                details.classList.remove('hidden');
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-close-line');
            } else {
                details.classList.add('hidden');
                icon.classList.remove('ri-eye-close-line');
                icon.classList.add('ri-eye-line');
            }
        }

        function sellertoggleVisibility() {
            var details = document.getElementById('seller-details');
            var icon = document.getElementById('toggle-icon-seller');

            if (details.classList.contains('hidden')) {
                details.classList.remove('hidden');
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-close-line');
            } else {
                details.classList.add('hidden');
                icon.classList.remove('ri-eye-close-line');
                icon.classList.add('ri-eye-line');
            }
        }

        $(document).on('click', '.removeSellerFee', function() {
            // write fundtion so it will remove the seller fee of clicked outer col
            $(this).parent().parent().remove();
            sellerCount();
        })

        //
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/calculator/index.blade.php ENDPATH**/ ?>