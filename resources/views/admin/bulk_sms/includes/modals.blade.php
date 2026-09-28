<div id="add-kpi-modal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="get-user-kpis"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="get-user-kpis">SMS Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save-parameters">

                <div class="modal-body" id="add-kpi">
                    <div class="row">
                        {{-- <div class="col-12">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Numbers <small class="text-muted">e.g.
                                        971585858176,+971585858176</small><button class="ms-2 btn btn-sm btn-primary" type="button" id="excel-input">Import Numbers</button> </label>
                                <input type="text" name="input" class="form-control inputs"
                                    placeholder="Enter Numbers with Country Code and comma separated" id="uploadtags">
                                    <span class="mt-2 text-center">OR</span>
                                    <input type="file" name="file" class="form-control inputs"
                                    id="file">
                            </div>
                        </div><!--end col--> --}}
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Numbers <small class="text-muted">e.g.
                                        971585858176,+971585858176</small></label>
                                <input type="text" name="input" class="form-control inputs"
                                    placeholder="Enter Numbers with Country Code and comma separated" id="uploadtags">
                            </div>
                        </div><!--end col-->

                        <div class="col-12 text-center">
                            <span class="mt-4 mb-4">
                                <h4>OR</h4>
                            </span>
                        </div><!--end col-->

                        <div class="col-12">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Import From a File <small
                                        class="text-muted">excel,csv</small></label>
                                <input type="file" name="file" class="form-control inputs" id="file">
                            </div>
                        </div><!--end col-->
                    </div>
                    <div class="row" id="headers-div">

                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card crm-widget">
                                <div class="card-body p-0">
                                    <div class="row row-cols-md-3 row-cols-1">
                                        <div class="col col-lg border-end">
                                            <div class="py-4 px-3">
                                                <h5 class="text-muted text-uppercase fs-13">Total
                                                </h5>
                                                <div class="d-flex align-items-center">

                                                    <div class="flex-grow-1 ms-3">
                                                        <h2 class="mb-0"><span class="counter-value"
                                                                id="total-character">0</span></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->

                                        <div class="col col-lg border-end">
                                            <div class="py-4 px-3">
                                                <h5 class="text-muted text-uppercase fs-13">Parts
                                                </h5>
                                                <div class="d-flex align-items-center">

                                                    <div class="flex-grow-1 ms-3">
                                                        <h2 class="mb-0"><span class=""
                                                                id="total-parts">0</span></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col col-lg border-end">
                                            <div class="py-4 px-3">
                                                <h5 class="text-muted text-uppercase fs-13">Remaining
                                                </h5>
                                                <div class="d-flex align-items-center">

                                                    <div class="flex-grow-1 ms-3">
                                                        <h2 class="mb-0"><span class=""
                                                                id="remaining-character">160</span></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->
                                        <div class="col col-lg border-end">
                                            <div class="py-4 px-3">
                                                <h5 class="text-muted text-uppercase fs-13">Encoding
                                                </h5>
                                                <div class="d-flex align-items-center">

                                                    <div class="flex-grow-1 ms-3">
                                                        <h2 class="mb-0">GSM</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->

                                    </div><!-- end row -->
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label for="campaign_name" class="form-label">Campaign Name</label>
                                <input name="campaign_name" type="campaign_name" placeholder="Name of your Campaign"
                                    id="campaign_name" class="form-control inputs">
                            </div>
                        </div><!--end col-->



                        <div class="col-12">

                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Message</label>
                                <textarea name="message" id="message" class="form-control inputs sms-counter"></textarea>
                            </div>
                        </div><!--end col-->
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="send_checking_link" name="send_checking_link" checked="">
                                <label class="form-check-label" for="send_checking_link">
                                    Send Tracking Link
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Custom Link</label>
                                <input name="custom_link" type="url"
                                    placeholder="if you want to send any custom link paste here." id="custom_link"
                                    class="form-control inputs sms-counter">
                            </div>
                        </div><!--end col-->

                    </div><!--end row-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Preview</label>
                                <div>
                                    <span id="msg-preview">Write your message</span>
                                    <span id="link-preview"></span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>

                        <a href="javascript:void(0);" class="btn btn-link link-success shadow-none fw-medium"
                            data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                    </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div id="import-excel" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog"
    aria-labelledby="import-excel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="import-excel">SMS Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="import-excel-form">

                <div class="modal-body" id="import-excel-form-div">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">File</label>
                                <input type="file" name="file" class="form-control inputs" id="file">
                            </div>
                        </div><!--end col-->


                    </div><!--end row-->

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="javascript:void(0);" class="btn btn-link link-success shadow-none fw-medium"
                            data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                    </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
