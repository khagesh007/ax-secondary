<div class="modal fade" id="dealShowModal" tabindex="-1" aria-labelledby="dealShowModalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dealShowModalModalLabel">Deal Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="height: 80vh;
    overflow-y: scroll;">
                <div class="text-center uk-vertical-align-middle mt-4 " id="modalLoader">
                    <div class="spinner-border text-success mt-5 mb-5" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="modalData" class="d-none">
                <form id="bitrixDealImportForm">
                    <div class="row">
                        <div class="col-12">
                    <input type="hidden" name="bitrix_deal_id" id="modal_bitrix_deal_id">
                    <input type="hidden" name="bitrix_dump_id" id="modal_bitrix_dump_id">
                    <input type="hidden" name="agent_id" id="modal_agent_id">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">Field</th>
                                        <th>Bitrix Value</th>
                                        <th>AX Value</th>
                                    </tr>
                                </thead>
                                <tbody id="dealShowModalTableBody">
                                    <tr>
                                        <td>Deal ID</td>
                                        <td id="modal_bitrix_deal_ID" class="htmlData"></td>
                                        <td id="modal_ax_deal_id" class="htmlData">N/A</td>
                                    </tr>

                                    <tr>
                                        <td>Agent
                                        </td>
                                        <td id="modal_bitrix_deal_ASSIGNED_BY_ID" class="htmlData"></td>
                                        <td id="modal_ax_deal_agent" class="htmlData">N/A</td>
                                    </tr>
                                    <tr>
                                        <td>Lead</td>
                                        <td>
                                            <span id="modal_bitrix_deal_LEAD_ID" class="htmlData"></span>
                                            -
                                            <span id="modal_bitrix_deal_TITLE" class="htmlData"></span>
                                            <br>
                                            <span id="modal_bitrix_lead_more_info" class="mt-3 htmlData"></span>
                                        </td>
                                        <td>
                                            <input type="hidden" id="modal_ax_deal_lead_id" name="lead_id">
                                            <span id="modal_ax_deal_lead" class="htmlData"></span>
                                            <span id="modal_ax_deal_lead_selector">
                                                <select name="select_lead_id" id="model_lead_selectbox" class="form-control">
                                                    <option value="">Select Lead</option>
                                                </select>
                                            </span>
                                            <span id="modal_lead_more_info" class="htmlData"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Listing</td>
                                        <td>
                                            <span id="modal_bitrix_deal_UF_CRM_1557731126" class="htmlData"></span>
                                        </td>
                                        <td style="max-width: 150px !important;">
                                            <input type="hidden" id="modal_ax_deal_listing_id" name="lead_ids">
                                            <span id="modal_ax_deal_listing" class="htmlData"></span>
                                            <span id="modal_ax_deal_listing_selector" class="d-none">
                                                <select name="property_id" id="model_listing_selectbox"
                                                    class="form-control">
                                                    <option value="">Select Listing</option>
                                                </select>
                                            </span>
                                            <span id="modal_property_more_info" class="htmlData"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Deal Price</td>
                                        <td> <span id="modal_bitrix_deal_CURRENCY_ID" class="htmlData">AED</span> <span
                                                id="modal_bitrix_deal_OPPORTUNITY" class="htmlData">0</span></td>
                                        <td>

                                            <div class="input-group">
                                                <span class="input-group-text" id="inputGroup-sizing-default">AED</span>
                                                <input type="text" id="modal_ax_deal_price"
                                                    class="form-control cleave" autocomplete="off" name="deal_price">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Type</td>
                                        <td id="modal_bitrix_deal_TYPE_ID" class="htmlData"></td>
                                        <td id="modal_ax_deal_type" class="htmlData">N/A</td>
                                    </tr>
                                    <tr>
                                        <td>Stage</td>
                                        <td id="modal_bitrix_deal_STAGE_ID" class="htmlData"></td>
                                        <td id="modal_ax_deal_title" class="htmlData">N/A</td>
                                    </tr>
                                    <tr>
                                        <td>Source</td>
                                        <td id="modal_bitrix_deal_SOURCE_ID" class="htmlData"></td>
                                        <td id="modal_ax_deal_title" class="htmlData">N/A</td>
                                    </tr>
                                    <tr>
                                        <td>Date Time</td>
                                        <td id="modal_bitrix_deal_timestamps" class="htmlData"></td>
                                        <td id="modal_ax_deal_timestamps" class="htmlData">N/A</td>
                                    </tr>
                                    <tr>
                                        <td>Comment</td>
                                        <td colspan="2" id="modal_bitrix_deal_COMMENTS" class="htmlData"></td>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h4>Bitrix Original Data for reference</h4>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                onclick="closeCollapseLeadInfo()" data-bs-target="#collapseExample"
                                aria-expanded="false" aria-controls="collapseExample">
                                Show Deal Data</button>
                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                onclick="closeCollapseDealInfo()" data-bs-target="#collapseExample2"
                                aria-expanded="false" aria-controls="collapseExample2">
                                Show Lead Data</button>

                            <button class="btn btn-default collapsed" type="button"
                                onclick="hideAccord()">Hide</button>
                            <div class="collapse multi-collapse" id="collapseExample">
                                <div class="card card-body" style="max-height: 300px;">

                                    <pre class="language-markup htmlData"  id="bitrixDealData"></pre>

                                </div>
                            </div>
                            <div class="collapse multi-collapse" id="collapseExample2">
                                <div class="card card-body" style="max-height: 300px;">
                                    <pre id="bitrixLeadData" class="language-markup htmlData"></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mt-3">
                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading">Attention!!</h4>
                                <p>* Details are programmatically mapped with bitrix please check them carefully before
                                    proceeding.
                                <br>
                                    * Please make sure to select the correct lead and listing before proceeding.
                                    <br>
                                    * If lead is not present in AX CRM then it will be created using the details from Bitrix (from left column).

                                </p>
                            </div>
                        </div>
                    </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">
                    Close
                </button>
                <button type="submit" class="btn btn-success admin-approve-send-btn"
                id="bitrixDealImportBtn"
                >
                    Import to AX CRM Deal
                </button>
            </div>

        </form>

    </div>
</div>
</div>
</div>
</div>
