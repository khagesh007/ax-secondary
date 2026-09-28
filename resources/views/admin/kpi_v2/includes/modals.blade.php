<div id="edit-kpi-modal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog"
    aria-labelledby="get-user-kpis" aria-hidden="true">
    <div class="modal-dialog ">
        <form id="edit-target-form">
            <input type="hidden" id="edit-target-settings-agent_id" name="agent_id">
            <input type="hidden" id="edit-target-settings-manager_id" name="manager_id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="get-user-kpis">Edit Agent KPI targets for <span id="edit-kpi-modal-month-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="edit-kpi">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <div>
                                <label for="edit-target-settings-sales_target" class="form-label">Sales Target</label>
                                <input type="text" class="form-control cleave" name="targets[sales_target]" id="edit-target-settings-sales_target" placeholder="Sales Target">
                                <small class="text-danger">Note: It must be like this 4300000 for 4.3M</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <div>
                                <label for="edit-target-settings-live_listings" class="form-label">Number of Listings (Live)</label>
                                <input type="text" class="form-control" name="targets[live_listings]" id="edit-target-settings-live_listings" placeholder="Number of Listings (Live)">
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <div>
                                <label for="edit-target-settings-new_listings" class="form-label">Number of Listings (New)</label>
                                <input type="text" class="form-control" name="targets[new_listings]" id="edit-target-settings-new_listings" placeholder="Number of Listings (New)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="javascript:void(0);" class="btn btn-danger  shadow-none fw-medium"
                   data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                <button type="submit" class="btn btn-primary">Submit</button>


            </div>
        </div>
        </form>
    </div>
</div>

<div id="edit-group-kpi-modal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog"
    aria-labelledby="get-user-kpis" aria-hidden="true">
    <div class="modal-dialog ">
        <form id="edit-group-target-form">
            <input type="hidden" id="edit-group-target-settings-group_id" name="group_id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="get-user-kpis">Edit Team KPI targets for <span id="edit-group-kpi-modal-month-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="edit-kpi">
                <div class="row">

                    <div class="col-12">
                        <div class="mb-3">
                            <div>
                                <label for="edit-group-target-settings-sales_value" class="form-label">Sales Target Value (In Million)</label>
                                <input type="text" class="form-control cleave" name="targets[sales_value]"
                                       id="edit-group-target-settings-sales_value" placeholder="Enter Sales Target Value">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-none">
                        <div class="mb-3">
                            <div>
                                <label for="edit-group-target-settings-average_transaction_value" class="form-label">Average Transaction Value (In Million)</label>
                                <input type="text" class="form-control" name="targets[average_transaction_value]"
                                       id="edit-group-target-settings-average_transaction_value" placeholder="Enter Average Transaction Value">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-none">
                        <div class="mb-3">
                            <div>
                                <label for="edit-group-target-settings-avg_rental_revenue" class="form-label">Average Rental Revenue</label>
                                <input type="text" class="form-control" name="targets[avg_rental_revenue]"
                                       id="edit-group-target-settings-avg_rental_revenue" placeholder="Enter Average Rental Revenue">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-none">
                        <div class="mb-3">
                            <div>
                                <label for="edit-group-target-settings-avg_sales_revenue" class="form-label">Average Sales Revenue</label>
                                <input type="text" class="form-control" name="targets[avg_sales_revenue]"
                                       id="edit-group-target-settings-avg_sales_revenue" placeholder="Enter Average Sales Revenue">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="javascript:void(0);" class="btn btn-danger  shadow-none fw-medium"
                   data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                <button type="submit" class="btn btn-primary">Submit</button>


            </div>
        </div>
        </form>
    </div>
</div>

