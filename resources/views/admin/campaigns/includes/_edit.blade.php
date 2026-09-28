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
                                    <h2 class="mb-0"><span class="counter-value" id="total-character">0</span></h2>
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
                                    <h2 class="mb-0"><span class="" id="total-parts">0</span></h2>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->
                    <div class="col col-lg border-end">
                        <div class="py-4 px-3">
                            <h5 class="text-muted text-uppercase fs-13">1 Message Count
                            </h5>
                            <div class="d-flex align-items-center">

                                <div class="flex-grow-1 ms-3">
                                    <h2 class="mb-0"><span class="" id="remaining-character">160</span></h2>
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
</div>
<div class="row mt-4">
    <div class="col-4">
        <label for="status" class="form-label">Status</label>

    </div>
    <div class="col-8">
        <!-- Radio toggle buttons   -->
        <div class="hstack gap-2 flex-wrap">
            <div class="form-check form-radio-primary mb-3">
                <input class="form-check-input" type="radio" name="status" id="status_pending"
                    {{ $campaign->status == 2 ? 'checked' : '' }} value="2">
                <label class="form-check-label" for="status_pending">
                    Pending
                </label>
            </div>
            <div class="form-check form-radio-success mb-3">
                <input class="form-check-input" type="radio" name="status" id="status_success"
                    {{ $campaign->status == 1 ? 'checked' : '' }} value="1">
                <label class="form-check-label" for="status_success">
                    Approve
                </label>
            </div>
            <div class="form-check form-radio-danger mb-3">
                <input class="form-check-input" type="radio" name="status" id="status_danger"
                    {{ $campaign->status == 0 ? 'checked' : '' }} value="0">
                <label class="form-check-label" for="status_danger">
                    Reject
                </label>
            </div>

        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="campaign_name" class="form-label">Campaign Name</label>
            <input name="campaign_name" type="text" placeholder="Name of your Campaign" id="campaign_name"
                value="{{ $campaign->name }}" class="form-control inputs">
        </div>
    </div><!--end col-->



    <div class="col-12">
        <div class="mb-3">
            <label for="ForminputState" class="form-label">Message</label>
            <textarea name="message" id="message" class="form-control inputs sms-counter">{{ $campaign->message }}</textarea>
        </div>
    </div><!--end col-->

    <div class="col-12">
        <div class="mb-3">
            <label for="ForminputState" class="form-label">Custom Link</label>
            <input name="custom_link" type="url" placeholder="if you want to send any custom link paste here."
                id="custom_link" class="form-control inputs sms-counter" value="{{ $campaign->custom_link }}">
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

<input type="hidden" name="id" value="{{ $campaign->id }}">
