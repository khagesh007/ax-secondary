<div class="modal fade" id="dealSplitModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Split Deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <form id="splitForm" enctype="multipart/form-data">
                        <input type="hidden" name="deal_id" id="deal_split_deal_id">
                        <input type="hidden" class="split_form_inputs" name="id" id="deal_split_id">




                        <div class="row">
                            <div class="col-md-2  col-sm-12 mt-2">Agent</div>
                            <div class="col-md-4  col-sm-12 mt-2 pr-0">
                                <div class="input-group">
                                    <select id="deal_split_agent_id" name="agent_id"
                                        class="form-control split_form_inputs" required>
                                        <option value="" disabled selected>Select Agent</option>
                                        @foreach ($agents_for_invite as $agant)
                                            <option value="{{ $agant->id }}">{{ $agant->full_name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <div class="col-2 mt-2">
                                Net Commission:

                            </div>
                            <div class="col-4 mt-2">
                                AED <span id="deal_split_net_commission"></span>
                            </div>

                            <div class="col-md-2  col-sm-12 mt-2">Commission
                            </div>
                            <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <input type="text" onkeyup="calculateSplitAmount()" id="deal_split_percentage"
                                        class="form-control split_form_inputs" name="split_percentage" required>
                                </div>
                            </div>
                            <div class="col-md-2  col-sm-12 mt-2">Split Amount</div>
                            <div class="col-md-4  col-sm-12 mt-2 pr-0">

                                <div class="input-group">
                                    <span class="input-group-text">AED</span>
                                    <input onkeyup="calculateSplitPercentage()" id="deal_split_amount" type="text"
                                        class="form-control cleave split_form_inputs" name="split_amount" required>
                                </div>
                                <span class="text-danger fs-10" id="split_amount_msg"></span>
                            </div>

                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="alert alert-warning text-center" role="alert">
                                    <b>Please be aware!!</b> This action will affect other splits of this deal.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-9">
                            </div>
                            <div class="col-3 d-none">
                                Remaining Amount: <span id="deal_split_remaining_amount_label"></span>
                                <input type="hidden" id="deal_split_remaining_amount">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"  id="split_enable_referral" name="referral_enabled">
                                    <label class="form-check-label" for="formCheck2">
                                        Referral
                                    </label>
                                </div>
                            </div>
                            <div class="col-4 d-none referral_enabled">
                                <select id="deal_split_referral_agent_id" name="referral_agent_id"
                                class="form-control split_form_inputs">
                                <option value="" disabled selected>Select Agent</option>
                                @foreach ($agents_for_invite as $agant)
                                    <option value="{{ $agant->id }}">{{ $agant->full_name }}</option>
                                @endforeach
                            </select>
                            </div>
                            <div class="col-2 d-none referral_enabled">
                                Amount
                            </div>
                            <div class="col-4 d-none referral_enabled">
                                <div class="input-group">
                                    <span class="input-group-text">AED</span>
                                    <input  type="text" class="form-control cleave split_form_inputs"
                                    id="deal_split_referral_amount"
                                    name="referral_amount">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="split_modal_user_list">

                        </div>


                        <div class="mt-4">

                            <div class="hstack gap-2 justify-content-right float-end">
                                <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                    data-bs-dismiss="modal" @click="modalClose()"><i
                                        class="ri-close-line me-1 align-middle"></i> Close</a>
                                <button type="submit" id="splitFormSubmitBtn"
                                    class="btn btn-success action-add">Save</button>
                            </div>
                        </div>
                    </form>
                </div>


            </div>
        </div>
    </div>
</div>
