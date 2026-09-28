<form id="republishRequestFormSubmission" class="modal-content needs-validation" novalidate>
    <div class="modal-header bg-soft-danger">
        <h5 class="modal-title" id="archiveModalLabel">Republish Request<span id="whichPassword"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
    </div>
    <div class="modal-body px-5 py-3">
        <div class="alert alert-primary" role="alert">
            <b>{{ $data->user->full_name }}</b> Requested to republish this Listing.
        </div>

        <div class="row mb-1">
            <div class="col-md-12 text-center">
                <button class="btn btn-sm btn-primary fetch-all-portal-updated-data">Fetch portal data</button>
                <button class="btn btn-outline-primary btn-load d-none" id="fetch-all-portal-updated-data-placeholder" disabled>
                    <span class="d-flex align-items-center">
                        <span class="flex-grow-1 me-2">
                            Loading...
                        </span>
                        <span class="spinner-border flex-shrink-0" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </span>
                </button>
            </div>
        </div>
        {{-- <hr> --}}
        <div class="loader-div d-none text-center" id="loader-div" style="min-height: 75px;position: absolute;left:49%">
            <div class="loader-portals"></div>
        </div>
        <div class="row" id="portal-data-div">
            <div class="col-md-4">
                <div class="card mb-1">
                    <div class="card-body">
                        <a class="d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#leadDiscovered1"
                            role="button" aria-expanded="false" aria-controls="leadDiscovered1">
                            <div class="flex-shrink-0">
                                <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644265820230215063058.png"
                                    alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-14 mb-1">Property Finder</h6>
                                <p class="text-muted mb-0">{{ $pf_level->meta_value??'-' }}</p>

                            </div>
                            <span class="text-center mt-2" style="font-size: 20px"><i class=" ri-arrow-down-s-line" ></i></span>


                        </a>

                    </div>
                    <div class="border-top border-top-dashed collapse" id="leadDiscovered1" style="">
                        <div class="card-body">
                            <ul class="list-unstyled vstack gap-2 mb-0" id="pf-meta-data-div">
                                @include('admin.listing.includes.partials._meta_data',['metas'=>$pf_metas])
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-1">
                    <div class="card-body">
                        <a class="d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#leadDiscovered2"
                            role="button" aria-expanded="false" aria-controls="leadDiscovered2">
                            <div class="flex-shrink-0">
                                <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644259820230215062958.png"
                                    alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-14 mb-1">Bayut</h6>
                                <p class="text-muted mb-0">{{ $bayut_level->meta_value??'-' }}</p>
                            </div>
                            <span class="text-center mt-2" style="font-size: 20px"><i class=" ri-arrow-down-s-line" ></i></span>

                        </a>

                    </div>
                    <div class="border-top border-top-dashed collapse" id="leadDiscovered2" style="">
                        <div class="card-body">
                            <ul class="list-unstyled vstack gap-2 mb-0" id="bayut-meta-data-div">
                                @include('admin.listing.includes.partials._meta_data',['metas'=>$bayut_metas])
                            </ul>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-1">
                    <div class="card-body">
                        <a class="d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#leadDiscovered3"
                            role="button" aria-expanded="false" aria-controls="leadDiscovered3">
                            <div class="flex-shrink-0">
                                <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644264020230215063040.png"
                                    alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-14 mb-1">Dubizzle</h6>
                                <p class="text-muted mb-0">{{ $dubizzle_level->meta_value??'-' }}</p>
                            </div>
                            <span class="text-center mt-2" style="font-size: 20px"><i class=" ri-arrow-down-s-line" ></i></span>

                        </a>
                    </div>
                    <div class="border-top border-top-dashed collapse" id="leadDiscovered3" style="">
                        <div class="card-body">
                            <ul class="list-unstyled vstack gap-2 mb-0" id="dubizzle-meta-data-div">
                                @include('admin.listing.includes.partials._meta_data',['metas'=>$dubizzle_metas])
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="">
            <input type="hidden" name="request_id" value="{{ $data->id }}">
            <div class="col-lg-12 mt-2">
                <div class="d-flex align-items-center border border-dashed p-2 rounded mt-2">
                    <div class="flex-shrink-0 avatar-sm">
                        <div class="avatar-title bg-light rounded">
                            <i class="ri-file-ppt-2-line fs-20 text-danger"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1"><a href="{{ $data->file }}" target="_blank">New Contract</a></h6>
                        {{-- <small class="text-muted">4.5 MB</small> --}}
                    </div>
                    <div class="hstack gap-3 fs-16">
                        <a href="{{ $data->file }}" target="_blank" class="text-muted"><i
                                class="ri-download-2-line"></i></a>
                    </div>
                </div>
            </div>
            {{--
        <div class="col-lg-12 mt-2">
            <a href="{{ $data->file }}">View</a>
        </div> --}}

        <div class="col-lg-12 mt-2">
            <label for="contract_file_password" class="form-label">Contract File Password</label>
            <input type="text" name="contract_file_password" id="issue_date" class="form-control"
                value="{{ $data->password }}" placeholder="Enter contract file password">
        </div>

            <div class="col-lg-6 mt-2">
                <label for="issue_date" class="form-label">Contract Start<span class="text-danger">*</span></label>
                <input type="date" name="issue_date" id="issue_date" class="form-control"
                    value="{{ $data->issue_date }}">
            </div>
            <div class="col-lg-6 mt-2">
                <label for="expiry_date" class="form-label">Contract End<span class="text-danger">*</span></label>
                <input type="date" name="expiry_date" id="expiry_date" class="form-control"
                    value="{{ $data->expiry_date }}">
            </div>

            <div class="col-lg-12 mt-2">
                <label for="document_type_name" class="form-label">Status</label>
                <select name="status" id="request_status" class="form-control select2">
                    <option value="REPUBLISH_REQUESTED"
                        {{ $data->status == 'REPUBLISH_REQUESTED' ? 'selected' : '' }}>
                        Republish Requested</option>
                    <option value="REPUBLISH_APPROVED" {{ $data->status == 'REPUBLISH_APPROVED' ? 'selected' : '' }}>
                        Republish Approved</option>
                    <option value="REPUBLISH_REJECTED" {{ $data->status == 'REPUBLISH_REJECTED' ? 'selected' : '' }}>
                        Republish Rejected</option>
                </select>
            </div>

            <div class="col-lg-12 mt-2 contract_note_div d-none">
                <label for="document_type_name" class="form-label">Note</label>
                <textarea rows="4" name="note" id="contract_note" class="form-control"
                    placeholder="Write the reason for rejection..."></textarea>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success action-save" id="saveRepublishRequestSubmission">
            <span id="resquest-response-text">Approve</span> Request
        </button>
        <button class="btn btn-outline-primary btn-load d-none" id="saveRepublishRequestSubmission-placeholder" disabled>
            <span class="d-flex align-items-center">
                <span class="flex-grow-1 me-2">
                    Loading...
                </span>
                <span class="spinner-border flex-shrink-0" role="status">
                    <span class="visually-hidden">Loading...</span>
                </span>
            </span>
        </button>

        <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
            Close
        </button>
    </div>
</form>
