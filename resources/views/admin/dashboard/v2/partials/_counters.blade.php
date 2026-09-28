{{-- @dd($listingsCount) --}}
<div class="row row-cols-1 row-cols-md-3 g-4 mb-2">
    <div class="col">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-start align-items-center">
                    <h5 class="card-title text-uppercase fw-medium text-muted text-truncate mb-0">Listings</h5>
                    @if(request("exemptListingDateFilter") == false)
                        <span class="badge badge-soft-info ms-2">Published in past {{ $noOfDays }} days</span>
                    @else
                        <span class="badge badge-soft-info ms-2">Live</span>
                    @endif
                </div>
                <div class="card-text">
                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                        <span class="counter-value" data-target="{{ propertyTotalCounter(auth()->user()->id) ?? 0 }}">{{ propertyTotalCounter(auth()->user()->id) ?? 0 }}</span>
                    </h4>
                </div>
                <div class="card-text">
                    <div class="d-flex justify-content-between">
                        <div>
                            PF
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $listingsCount['pf'] ?? 0 }}">{{ $listingsCount['pf'] ?? 0 }}</span>
                            </h4>
                        </div>
                        <div>
                            Bayut
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $listingsCount['bayut'] ?? 0 }}">{{ $listingsCount['bayut'] ?? 0 }}</span>
                            </h4>
                        </div>
                        <div>
                            Dubizzle
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $listingsCount['dubizzle'] ?? 0 }}">{{ $listingsCount['dubizzle'] ?? 0 }}</span>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <a href="{{ route('admin.listing.index') }}"
                           class="text-decoration-underline ax-text">View all listings</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white ax-grad-background rounded fs-3">
                                            <i class="bx bx-home-circle"></i>
                                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-start align-items-center">
                    <h5 class="card-title text-uppercase fw-medium text-muted text-truncate mb-0">Leads</h5>
                    <span class="badge badge-soft-info ms-2">Recorded in past {{ $noOfDays }} days</span>
                </div>
                <div class="card-text">
                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                        <span class="counter-value" data-target="{{ $leadsCount->total ?? 0 }}">{{ $leadsCount->total ?? 0 }}</span>
                    </h4>
                </div>
                <div class="card-text">
                    <div class="d-flex justify-content-between">
                        <div>
                            PF
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $leadsCount->pf ?? 0 }}">{{ $leadsCount->pf ?? 0 }}</span>
                            </h4>
                        </div>
                        <div>
                            Bayut
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $leadsCount->bayut }}">{{ $leadsCount->bayut }}</span>
                            </h4>
                        </div>
                        <div>
                            Dubizzle
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $leadsCount->dubizzle ?? 0 }}">{{ $leadsCount->dubizzle ?? 0 }}</span>
                            </h4>
                        </div>
                        <div>
                            Other
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $leadsCount->other ?? 0 }}">{{ $leadsCount->other ?? 0 }}</span>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <a href="{{ route('admin.work.leads.index') }}"
                           class="text-decoration-underline ax-text">View all leads</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white ax-grad-background rounded fs-3">
                                            <i class="bx bx-phone-call"></i>
                                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-start align-items-center">
                    <h5 class="card-title text-uppercase fw-medium text-muted text-truncate mb-0">Deals</h5>
                    <span class="badge badge-soft-info ms-2">Completed in past {{ $noOfDays }} days</span>
                </div>

                <div class="card-text">
                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                        <span class="counter-value" data-target="{{ $dealsCount->total ?? 0 }}">{{ $dealsCount->total ?? 0 }}</span>
                    </h4>
                </div>
                <div class="card-text">
                    <div class="d-flex justify-content-between">
                        <div>
                            PF
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $dealsCount->pf ?? 0 }}">{{ $dealsCount->pf ?? 0 }}</span>
                            </h4>
                        </div>
                        <div>
                            Bayut
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $dealsCount->bayut ?? 0 }}">{{ $dealsCount->bayut ?? 0 }}</span>
                            </h4>
                        </div>
                        <div>
                            Dubizzle
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $dealsCount->dubizzle ?? 0 }}">{{ $dealsCount->dubizzle ?? 0 }}</span>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <a href="{{ route('admin.work.deals.index') }}"
                           class="text-decoration-underline ax-text">View all deals</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white ax-grad-background rounded fs-3">
                                            <i class="bx bx-wallet"></i>
                                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
