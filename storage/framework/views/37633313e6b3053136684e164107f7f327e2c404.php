<div class="modal fade" id="deductibleModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Add New Fee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <form enctype="multipart/form-data" id="deductibleForm">

                            <div class="row g-3">

                                <div class="col-3">
                                    <input type="hidden" name="deal_id" id="deductible_deal_id">
                                    <input type="hidden" class="deductible-inputs" name="id" id="deductible_form_id">
                                    Deal Price:
                                </div>
                                <div class="col-3">
                                    AED <span id="deductible_deal_price"></span>

                                </div>
                                <div class="col-3">
                                    Gross Commission

                                </div>
                                <div class="col-3">
                                    AED <span id="deductible_gross_commission"></span>
                                </div>
                                <div class="col-3">
                                    Net Commission:

                                </div>
                                <div class="col-3">
                                    AED <span id="deductible_net_commission"></span>
                                </div>
                                <div class="col-md-3  col-sm-12 mt-2">Deal Type</div>
                                <div class="col-md-3  col-sm-12 mt-2 pr-0 label-deal_type">

                                </div>
                                <hr>
                                <div class="col-md-2  col-sm-12 mt-2">Fee Type</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                 <select class="form-control deductible-inputs" name="deductible_type_id" id="finance-deductible_type">
                                     <option value="">Select Fee Type</option>
                                     <?php $__currentLoopData = $fee_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dtype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                         <option data-type="<?php echo e($dtype->type); ?>" data-type_value="<?php echo e($dtype->type_value); ?>" value="<?php echo e($dtype->id); ?>"><?php echo e($dtype->name); ?></option>
                                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                 </select>


                                    <div class="input-group d-none">


                                    </div>
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">Fee Type</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                    <div class="input-group">
                                      <select class="form-control deductible-inputs select2" name="fee_type" id="finance-commission_type">
                                          <option value="">Select Commission Type</option>
                                          <option value="percentage">Percentage</option>
                                          <option value="fixed">Fixed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">Fee Value</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-sizing-default">AED</span>
                                        <input type="text" autocomplete="off" id="finance-commission_value" class="form-control deductible-inputs" name="fee_value" required>
                                    </div>
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">Amount</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-sizing-default">AED</span>
                                        <input type="text" id="deductible_form_amount" class="form-control deductible-inputs" name="amount" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-2  col-sm-12 mt-2">Attachment</div>
                                <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                    <div class="input-group">
                                        <input type="file" class="form-control deductible-inputs" name="attachment">

                                    </div>
                                </div>


                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-warning text-center" role="alert">
                                        <b>Please be aware!!</b> This action will affect other splits of this deal.
                                    </div>
                                </div>
                            </div>


                            <div class="mt-2">

                                <div class="hstack gap-2 justify-content-right float-end">
                                    <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                       data-bs-dismiss="modal"><i
                                                class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="submit" class="btn btn-success action-add" id="deductFormBtn" >
                                         <span v-if="showBtnLoader" class="d-flex align-items-center d-none">
                                                            <span class="spinner-border flex-shrink-0" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </span>
                                                            <span class="flex-grow-1 ms-2">
                                                                Loading...
                                                            </span>
                                                        </span>
                                        <span v-else>Save</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>
    </div>


<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/deals_revamped/partials/financial/deductibleModal.blade.php ENDPATH**/ ?>