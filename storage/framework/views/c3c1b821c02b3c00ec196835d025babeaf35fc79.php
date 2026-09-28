<div class="modal fade" id="dealSplitCommissionModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Split Deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="modalClose()"></button>
            </div>
            <div class="modal-body">
                <div>
                    <form id="splitCommissionForm" enctype="multipart/form-data">

                        <div class="row p-2">
                            <div class="col-3">Net Commission</div>
                            <div class="col-3 deal_split_edit_net_commission"></div>
                            <div class="col-3">Split Amount</div>
                            <div class="col-3 deal_split_edit_split_amount"></div>
                            <input type="hidden" id="deal_split_edit_split_amount_input">
                        </div>
                        <div class="row p-2">

                            <h5>Update Commission</h5>

                            <div class="col-md-2  col-sm-12 mt-2">Agent/User</div>
                            <div class="col-md-4  col-sm-12 mt-2 pr-0">
                                <div class="input-group">
                                    <input type="hidden" name="id" id="deal_split_commission_id">
                                    <input type="hidden" name="deal_id" id="deal_split_modal_deal_id">
                                    <input type="hidden" id="deal_split_commission_agent_id" name="user_id">






                                    <span id="deal_split_commission_agent_name"></span>

                                </div>
                            </div>
                            <div class="col-md-2  col-sm-12 mt-2">
                                Commission Type
                            </div>
                            <div class="col-md-4  col-sm-12 mt-2 pr-0 deal_split_commission_category">

                            </div>
                            <div class="col-md-3  col-sm-12 mt-2">Commission
                                <small>(From net commission)</small>
                            </div>
                            <div class="col-md-3  col-sm-12 mt-2 pr-0 ">

                                <div class="input-group">
                                    <span class="input-group-text" id="deal_split_commission_type"></span>
                                    <input
                                        id="deal_split_commission_type_value"
                                        type="text" class="form-control"
                                        name="percentage" required>
                                </div>

                            </div>
                            <div class="col-md-2  col-sm-12 mt-2">Amount</div>
                            <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                <div class="input-group">
                                    <span class="input-group-text">AED</span>
                                    <input
                                           id="deal_split_commission_amount"
                                           type="text" class="form-control"
                                           name="amount" required>
                                </div>
                            </div>

                        </div>


                        <div class="mt-4">

                            <div class="hstack gap-2 justify-content-right float-end">
                                <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                   data-bs-dismiss="modal" @click="modalClose()"><i
                                        class="ri-close-line me-1 align-middle"></i> Close</a>
                                <button type="submit" id="splitCommissionFormSubmitBtn" class="btn btn-success action-add">Save</button>
                            </div>
                        </div>
                    </form>
                </div>


            </div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/deals_revamped/partials/financial/splitCommissionEdit.blade.php ENDPATH**/ ?>