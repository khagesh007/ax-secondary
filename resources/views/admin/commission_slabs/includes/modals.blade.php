
<div class="modal zoomIn" id="slabLimit" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addModalLabel" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Slab Limit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="slabItemForm" class="needs-validation" novalidate>
                    <div class="row g-3">

                        <div class="col-4">
                            <label for="from_value" class="form-label">From</label>
                        </div>
                        <div class="col-8">
                            <input type="hidden" name="id" id="commission_limit_id" value="">

                            <input type="hidden" name="commission_slab_id" id="slab_id" value="">
                            <input type="number" step="0.01" class="form-control" id="from_value" name="from_value"
                                placeholder="10000000" required>
                            <div class="invalid-feedback">
                                Please enter the Lower Limit.
                            </div>
                        </div>

                        <div class="col-4">
                            <label for="to_value" class="form-label">To</label>
                        </div>
                        <div class="col-8">
                            <input type="number" class="form-control" id="to_value" name="to_value"
                                placeholder="2000000">
                            <div class="invalid-feedback">
                                Please enter the Upper Limit.
                            </div>
                        </div>
                        <div class="col-4">
                            <label for="limit_commission_type" class="form-label">Type</label>
                        </div>
                        <div class="col-8">
                            <select class="form-control" id="limit_commission_type" name="commission_type">
                                <option value="Percentage">Percentage</option>
                                <option value="fixed" disabled>Fixed</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="limit_commission_value" class="form-label">Value</label>
                        </div>
                        <div class="col-8">
                            <input type="number" class="form-control" id="limit_commission_value" name="commission_value"
                                   placeholder="10%">
                            <div class="invalid-feedback">
                                Please enter the commission value.
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-lg-12 mt-3">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                            <button id="submitForm" type="submit" class="btn btn-primary action-save">Submit</button>
                        </div>
                    </div>
                    <!--end col-->
                </form>
            </div>
            <!--end row-->

        </div>
    </div>
</div>




@can('Sources View')
<div class="modal zoomIn" id="viewModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header card-header pb-2">
                <h5 class="modal-title" id="addModalLabel">View Source</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-4">
                        <label for="view-name" class="form-label">Source Name:</label>
                    </div>
                    <div class="col-8" id="view-name"></div>
                    <div class="col-4">
                        <label class="form-check-label" for="statusCheckbox">Order:</label>
                    </div>
                    <div class="col-8" id="view-order">
                    </div>
                    <div class="col-4">
                        <label class="form-check-label" for="statusCheckbox">Icon class:</label>
                    </div>
                    <div class="col-8" id="view-icon">
                    </div>
                    <div class="col-4 view_thumbnail">
                        <label class="form-check-label" for="thumbnailCheckbox">Thumbnail:</label>
                    </div>
                    <div class="col-8 view_thumbnail" id="view-thumbnail">
                    </div>
                    <div class="col-4 view_image">
                        <label class="form-check-label" for="view_image">Image:</label>
                    </div>
                    <div class="col-8 view_image" id="view_image">
                    </div>
                    <div class="col-4">
                        <label class="form-check-label" for="badge">badge:</label>
                    </div>
                    <div class="col-8" id="view-badge">
                    </div>
                    <div class="col-4">
                        <label class="form-check-label" for="statusCheckbox">Status:</label>
                    </div>
                    <div class="col-8" id="view-status">
                    </div>
                    <div class="col-4">
                        <label class="form-check-label" for="leadCheckbox">Lead Source:</label>
                    </div>
                    <div class="col-8" id="view-lead">
                    </div>
                    <div class="col-4">
                        <label class="form-check-label" for="contactCheckbox">Contact Source:</label>
                    </div>
                    <div class="col-8" id="view-contact">
                    </div>
                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end">
                            <button id="deleteBtn" class="btn btn-danger action-delete">Delete</button>
                            <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan
