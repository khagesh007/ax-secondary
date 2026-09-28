<div class="row mt-3">
    @if ($lead->criteria)


    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-secure-payment-line align-bottom me-1 text-muted"></i>
                    Lead Criteria</h5>
            </div>
            <div class="card-body">
                @if ($lead && $lead->criteria && $lead->criteria->min_price)
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Budget:</p>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0">{{ $lead->criteria->min_price }} - {{ $lead->criteria->max_price }}</h6>
                        </div>
                    </div>
                @endif

                @if ($lead->for)
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">For:</p>
                        </div>
                        <div class="flex-grow-1 ms-2">

                            @if (is_array($lead->for))
                                <h6 class="mb-0">{{ $lead->for[0] ?? '' }}</h6>
                            @else
                                <h6 class="mb-0">{{ $lead->for }}</h6>
                            @endif
                        </div>
                    </div>
                @endif



                @if ($lead && $lead->criteria && $lead->criteria->min_bed_id)
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Beds:</p>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0">{{ $lead?->criteria?->minBed?->name }} -
                                {{ $lead?->criteria?->maxBed->name }}
                            </h6>
                        </div>
                    </div>
                @endif
                @if ($lead->criteria)
                <div class="d-flex align-items-center mb-2  flex-wrap">
                    <div class="flex-shrink-0">
                        <p class="text-muted mb-0">Community:</p>
                    </div>

                        @foreach ($lead->criteria->locations as $loc)
                            @if ($loc->parentable_type == 'App\Models\Community')
                                <div class="flex-grow-1 ms-1">

                                    <span class="badge bg-light text-body rounded-pills mt-1"
                                        style="font-size:12px">{{ getLocation($loc->parentable_type, $loc->parentable_id) }}</span>

                                </div>
                            @endif
                        @endforeach



                </div>
                @endif
                @if ($lead->criteria)

                    <div class="d-flex align-items-center mb-2 flex-wrap">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Location:</p>
                        </div>

                        @foreach ($lead->criteria->locations as $loc)
                            @if ($loc->parentable_type == 'App\Models\SubCommunity' || $loc->parentable_type == 'App\Models\CommunityProperty')
                                <div class="flex-grow-1 ms-1">
                                    <span class="badge bg-light text-body rounded-pills mt-1"
                                        style="font-size:12px">{{ getLocation($loc->parentable_type, $loc->parentable_id) }}</span>

                                </div>
                            @endif
                        @endforeach



                    </div>
                @endif

            </div>
        </div>

    </div>
    @endif
    {{-- {{$lead->criteria}} --}}
    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-secure-payment-line align-bottom me-1 text-muted"></i>
                    Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0">
                        <p class="text-muted mb-0">Name:</p>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <h6 class="mb-0">{{ $lead->first_name }} {{ $lead->last_name }}</h6>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0">
                        <p class="text-muted mb-0">Mobile:</p>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <h6 class="mb-0">{{ $lead->mobile }}</h6>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0">
                        <p class="text-muted mb-0">Source:</p>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <h6 class="mb-0">{{ $lead?->leadSource?->name }}</h6>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0">
                        <p class="text-muted mb-0">Medium:</p>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <h6 class="mb-0">{{ $lead?->leadMedium?->name }}</h6>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
