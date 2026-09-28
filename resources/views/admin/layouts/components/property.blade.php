<p class="text-muted text-uppercase fs-12 fw-medium mb-2 mt-3">Properties ({{$propertiesCount}} Records)</p>

@if ($properties->count() > 0)
    @foreach ($properties as $property)
        <div class="col-md-12">
            <div class="card mb-1">
                <div class="card-body text-start">
                    <a class="d-flex align-items-center" data-bs-toggle="collapse" href="#lead_{{ $property->id }}"
                        role="button" aria-expanded="false" aria-controls="lead_{{ $property->id }}_{{ $property->id }}">
                        <div class="flex-shrink-0 me-2">
                            <img src="{{ $property?->first_image?->path ?? 'https://ui-avatars.com/api/?name=' . $property->title }}"
                                alt="" class="avatar-xs rounded-circle shadow" />
                        </div>
                        <div class="flex-grow-1">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="fs-14 mb-1"><span class="searchable">{{ $property->ref_no }}</span> -
                                        <span class="searchable">{{ $property->propertyCategory?->title }}</span>
                                        <span class="searchable">{{ $property->unit_no ? '- ' . $property->unit_no : '' }}</span></h6>

                                </div>
                                <div class="col-md-4">
                                    <div title="{{ $property->state }} {{ $property->publish_status }}"
                                        class="d-flex justify-content-between gap-1 align-items-center">
                                        <div
                                            class="btn status_{{ $property->publish_status }} btn-label btn-sm rounded-pill {{ $property->state }}">
                                            <i
                                                class="label-icon align-middle rounded-pill fs-14 me-1 bx
                                    {{ $property->state == 'APPROVED' ? 'bx-check-circle text-success' : '' }}
                                    {{ $property->state == 'REFUSED' ? 'bx-x-circle text-dangers' : '' }}
                                    {{ $property->state == 'PENDING' ? ' bx-hourglass text-warning' : '' }}">
                                            </i>

                                            {{ $property->publish_status }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-0"><i class="mdi mdi-bed-double"></i> {{ $property->bed?->name }} <i
                                    class="mdi mdi-google-maps"></i> <span class="searchable">{{ $property->location }}</span></p>
                        </div>
                    </a>
                </div>
                <div class="collapse border-top border-top-dashed" id="lead_{{ $property->id }}">
                    <div class="card-body text-start">

                        <ul class="list-unstyled vstack gap-2 mb-0">
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class=" mdi-format-list-group "></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Refrence Number</h6>
                                        <small class="text-muted">{{ $property->ref_no }}</small>
                                    </div>
                                </div>
                            </li>
                            @if ($property->unit_no)
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xxs text-muted">
                                            <i class="mdi mdi-office-building-marker-outline "></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Unit No</h6>
                                            <small class="text-muted"><span class="searchable">{{ $property->unit_no }}</span></small>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if ($property->agent_id)
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class=" bx bx-user-circle
                                        "></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Agent</h6>
                                        <small class="text-muted"><span class="searchable">{{ $property->agent?->full_name }}</span></small>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-footer hstack gap-2">
                        <a href="{{ route('admin.listing.index') }}?ref_number={{ $property->ref_no }}"
                            class="btn btn-primary btn-sm w-100"><i class="ri-eye-line align-bottom me-1"></i> View</a>
                        {{-- <button class="btn btn-info btn-sm w-100"><i
                        class="ri-question-answer-line align-bottom me-1"></i>
                    Message</button> --}}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <span class="text-muted">No Property Found</span>
@endif
