<p class="text-muted text-uppercase fs-12 fw-medium mb-2 mt-3">Listing Leads ({{$listingleadsCount}} Records)</p>

@if ($listingleads->count() > 0)
    @foreach ($listingleads as $lead)
        <div class="col-md-12">
            <div class="card mb-1">
                <div class="card-body text-start">
                    <a class="d-flex align-items-center" data-bs-toggle="collapse" href="#listing_lead_{{ $lead->id }}"
                        role="button" aria-expanded="false" aria-controls="listing_lead_{{ $lead->id }}_{{ $lead->id }}">
                        <div class="flex-shrink-0 me-2">
                            <img src="{{'https://ui-avatars.com/api/?name='. $lead->first_name.' '.$lead->last_name}}"
                                alt="" class="avatar-xs rounded-circle shadow" />
                        </div>
                        <div class="flex-grow-1 ">

                                    <h6 class="fs-14 mb-1"><span class="searchable">ID:{{ $lead->id }} {{ $lead->first_name }} {{ $lead->last_name }} </span></h6>



                                    <p class="text-muted mb-0"> <b>Email:</b> <span class="searchable">{{ $lead->email }}</span></p>
                                    <p class="text-muted mb-0"> <b>Mobile:</b> <span class="searchable">{{ $lead->mobile }}</span> &nbsp; Source: <span class="searchable">{{ $lead->source?->name }}</span></p>
                                </div>
                    </a>
                </div>
                <div class="collapse border-top border-top-dashed" id="listing_lead_{{ $lead->id }}">
                    <div class="card-body text-start">

                        <ul class="list-unstyled vstack gap-2 mb-0">
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class="ri-question-answer-line"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Type</h6>
                                        <small class="text-muted"><span class="searchable">{{$lead->type?->name}}</span></small>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class="ri-mac-line"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Medium</h6>
                                        <small class="text-muted"><span class="searchable">{{$lead->medium?->name}}</span></small>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class="mdi mdi-account-circle-outline"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Agent</h6>
                                        <small class="text-muted"><span class="searchable">{{$lead->agent?->full_name}}</span></small>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer hstack gap-2">
                        <a href="{{ route('admin.work.leads.index') }}?id={{ $lead->id }}"
                            class="btn btn-primary btn-sm w-100"><i class="ri-eye-line align-bottom me-1"></i> View</a>

                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <span class="text-muted">No Lead Found</span>
@endif
