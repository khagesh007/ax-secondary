<div class="modal zoomIn" id="passwordModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Password for <span id="whichPassword"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body px-5 py-3">

                <div class="row d-none" id="title_deed_password_div">
                    <div class="col-lg-6 mt-2">
                        <label for="title_deed_password" class="form-label">Title Deed Password</label>
                    </div>
                    <div class="col-lg-6 mt-2">
                        <input type="text" id="title_deed_password" name="title_deed_password"
                            class="form-control inputs" placeholder="Enter Title Deed File Password" maxlength="20">
                        <div id="title_deed_password"></div>

                    </div>
                </div>

                <div class="row d-none" id="owner_passport_copy_password_div">

                    <div class="col-lg-6 mt-2">
                        <label for="owner_passport_copy_password" class="form-label">Owner's Passport/EID
                            Password</label>
                    </div>
                    <div class="col-lg-6 mt-2">
                        <input type="text" id="owner_passport_copy_password"
                            name="owner_passport_copy_password" class="form-control inputs"
                            placeholder="Enter Passport File Password" maxlength="20">
                    </div>
                </div>

                <div class="row d-none" id="signed_listing_form_password_div">
                    <div class="col-lg-6 mt-2">
                        <label for="signed_listing_form" class="form-label">Contract A/Leasing Form/NOC
                            Password </label>
                    </div>
                    <div class="col-lg-6 mt-2">


                        <div id="signed_listing_form_password"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>




<div class="modal zoomIn" id="extraPasswordModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Password for <span class="extrafieldPassword"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body px-5 py-3">

                <div class="row" id="title_deed_password_div">
                    <div class="col-lg-4 mt-2">
                        <label for="title_deed_password" class="form-label"><span class="extrafieldPassword"></span></label>
                    </div>
                    <div class="col-lg-8 mt-2">
                        <input type="hidden" id="inputFor" name="inputFor">
                        <input type="text" id="extra_field_password" name="extra_field_password"
                            class="form-control inputs" placeholder="File Password" maxlength="20">

                    </div>
                </div>

            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal" id="extraPassInputs">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
