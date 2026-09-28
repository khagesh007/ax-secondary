<div class="modal fade" id="attachmentsEditDocumentModal" tabindex="-1" aria-labelledby="attachmentsEditDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attachmentsEditDocumentModalLabel">Edit Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <form id="attachmentsEditDocumentForm" enctype="multipart/form-data" class="was-validated" novalidate>
                        <input type="hidden" id="attachmentsEditDocumentId", name="id">
                        <input type="hidden" id="attachmentsEditDocumentModel", name="model">
                        <input type="hidden" id="attachmentsEditDocumentUploderElementId", name="uploderElementId">
                        <div class="row p-2">
                            <div class="col-md-4 col-sm-12 mt-2">Select Document</div>
                            <div class="col-md-8 col-sm-12 mt-2 pr-0">
                                <div class="input-group">
                                    <select  id="attachmentsEditDocumentDocumentTitle" name="title" class="form-control select2" required>
                                        <option value="">Select Document</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="hstack gap-2 justify-content-right float-end">
                                <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                   data-bs-dismiss="modal"><i
                                        class="ri-close-line me-1 align-middle"></i> Close</a>
                                <button type="submit" id="attachmentsEditDocumentModalSubmitButton" class="btn btn-success action-add">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
