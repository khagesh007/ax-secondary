<div class="modal fade" id="callModal" tabindex="-1" aria-labelledby="callModalLabel" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="callModalLabel"><span id="modal_title">Add </span> Call follow up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="mainForm" class="needs-validation" novalidate>
                    <div class="row g-3">

                        <div class="col-4">

                            <label for="title" class="form-label">Time*</label>
                        </div>
                        <div class="col-8">
                            <input type="hidden" name="id" id="call_contact_id" value="">
                            <input type="datetime-local" class="form-control" id="call_followup_time" name="time" placeholder="Enter follow-up time"
                                   required>
                            <div class="invalid-feedback">
                                Please select a time.
                            </div>
                        </div>

                        <div class="col-4">
                            <label class="form-check-label" for="statusCheckbox">Note</label>
                        </div>
                        <div class="col-8">

                            <textarea class="form-control" name="note" placeholder="Enter note"></textarea>

                        </div>
                        <!--end col-->

                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                                <button id="submitBtn" type="submit" class="btn btn-primary action-save">Submit</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
        </div>
    </div>
</div>



