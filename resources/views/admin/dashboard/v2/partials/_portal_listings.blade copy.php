{{-- @dd($propertyFinderData["quality"]["ranks"]) --}}
<div class="row">
    <div class="col-xxl-4 col-lg-4">
        <div class="card card-height-100 mb-3">
            <div class="card-header align-items-center border-0 d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Property Finder</h4>
                <div class="flex-shrink-0">
                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#pf-product-tab"
                               role="tab" aria-selected="false">Product</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab"
                               href="#pf-rank-tab" role="tab" aria-selected="true">Quality</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#pf-verified-tab"
                               role="tab" aria-selected="true">Verified</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body" style="max-height: 400px;">
                <div class="tab-content p-0">
                    <div class="tab-pane active" id="pf-product-tab" role="tabpanel">
                        <div
                            id="pf-level-chart"
                            style="height: 200px;"
                            data-dataset="{{ json_encode($propertyFinderData["product"]["levels"] ?? []) }}"
                        ></div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <h2 class="me-3 ff-secondary mb-0 pf_count">{{ $propertyFinderData["product"]["portal_total"] ?? 0 }}</h2>
                                <div>
                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0 d-none">
                                        <span class="badge badge-soft-success p-1 rounded-circle">
                                            <i class="ri-arrow-right-up-line"></i>
                                        </span> +0 New
                                    </p>
                                </div>
                            </div>
                            @foreach($propertyFinderData["product"]["levels"] ?? [] as $level)
                                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                    <p class="fw-medium mb-0">
                                        <i class="ri-checkbox-blank-circle-fill text-{{ $level->name == 'Premium' ? 'danger' : ($level->name == 'Featured' ? 'primary' : 'success') }} align-middle me-2"></i>
                                        {{ $level->name }}</p>
                                    <div>
                                        <span class="text-muted pf_premium">{{ $level->count }} Listings</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane active" id="pf-rank-tab" role="tabpanel">
                        <div class="d-flex mt-2">
                            <h4 class=" flex-grow-1"></h4>
                            <div class="flex-shrink-0">
                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#pf-rank-meter-tab" role="tab"
                                           aria-selected="false"
                                           class="btn btn-soft-secondary btn-sm">
                                            <i class=" bx bx-tachometer"></i></a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#pf-rank-bar-tab" role="tab"
                                           aria-selected="false"
                                           class="btn btn-soft-secondary btn-sm">
                                            <i class="ri-bar-chart-2-fill"></i>
                                        </a>

                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#pf-status-tab-child"
                                           role="tab" aria-selected="false"
                                           class="btn btn-soft-secondary btn-sm">
                                            Status
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content p-0">
                            <div class="tab-pane active" id="pf-rank-meter-tab" role="tabpanel">
                                <div id="pf-gauge-chart"
                                     style="height: 200px;"
                                     data-dataset="{{ $propertyFinderData["quality"]["rank_avg"] ?? 0 }}"
                                >
                                </div>
                            </div>
                            <div class="tab-pane active" id="pf-rank-bar-tab" role="tabpanel">
                                <div id="pf-rank-bar-chart"
                                     style="height: 200px;"
                                     {{-- data-dataset="{{ $propertyFinderData["quality"]["ranks"] ?? 0 }}" --}}
                                     data-dataset="{{ base64_encode(json_encode($propertyFinderData["quality"]["ranks"] ?? 0)) }}"

                                >
                                </div>
                            </div>
                            <div class="tab-pane active" id="pf-status-tab-child" role="tabpanel">
                                <div id="pf-status-pie-chart"
                                     style="height: 200px;"
                                     data-dataset="{{ json_encode($propertyFinderData["quality"]["status"] ?? 0) }}"
                                >
                                </div>
                                <div class="mt-3">
                                    <div
                                        class="d-flex justify-content-between border-bottom border-bottom-dashed border-top border-top-dashed py-2">
                                        <p class="fw-medium mb-0"><i
                                                class="ri-whatsapp-fill text-success align-middle me-2"></i>
                                            Whatsapp AVG Response Time</p>
                                        <div>
                                            <span class="text-muted pe-5 pf_whatssapp_avg_time">{{ $propertyFinderData["quality"]["whatsapp_response_time"] ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane active" id="pf-verified-tab" role="tabpanel">
                        <div id="pf-verified-pie-chart"
                             style="height: 200px;"
                             data-dataset="{{ json_encode($propertyFinderData["verified"]["list"] ?? []) }}"
                        >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-4 col-lg-4">
        <div class="card card-height-100 mb-3">
            <div class="card-header align-items-center border-0 d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Bayut</h4>
                <div class="flex-shrink-0">
                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#bayut-product-tab"
                               role="tab" aria-selected="false">Product</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab"
                               href="#bayut-rank-tab" role="tab" aria-selected="true">Quality</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#bayut-verified-tab"
                               role="tab" aria-selected="true">Verified</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body" style="max-height: 400px;">
                <div class="tab-content p-0">
                    <div class="tab-pane active" id="bayut-product-tab" role="tabpanel">
                        <div
                            id="bayut-level-chart"
                            style="height: 200px;"
                            data-dataset="{{ json_encode($bayutData["product"]["levels"] ?? []) }}"
                        ></div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <h2 class="me-3 ff-secondary mb-0 bayut_count">{{ $bayutData["product"]["portal_total"] ?? 0 }}</h2>
                                <div>
                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0 d-none">
                                        <span class="badge badge-soft-success p-1 rounded-circle">
                                            <i class="ri-arrow-right-up-line"></i>
                                        </span> +0 New
                                    </p>
                                </div>
                            </div>
                            @foreach($bayutData["product"]["levels"] ?? [] as $level)
                                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                    <p class="fw-medium mb-0">
                                        <i class="ri-checkbox-blank-circle-fill text-{{ $level->name == 'Signature' ? 'primary' : ($level->name == 'Hot' ? 'danger' : 'success') }} align-middle me-2"></i>
                                        {{ $level->name }}</p>
                                    <div>
                                        <span class="text-muted bayut_premium">{{ $level->count }} Listings</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane active" id="bayut-rank-tab" role="tabpanel">
                        <div class="d-flex mt-2">
                            <h4 class=" flex-grow-1"></h4>
                            <div class="flex-shrink-0">
                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#bayut-rank-meter-tab" role="tab"
                                           aria-selected="false"
                                           class="btn btn-soft-secondary btn-sm">
                                            <i class=" bx bx-tachometer"></i></a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#bayut-rank-bar-tab" role="tab"
                                           aria-selected="false"
                                           class="btn btn-soft-secondary btn-sm">
                                            <i class="ri-bar-chart-2-fill"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content p-0">
                            <div class="tab-pane active" id="bayut-rank-meter-tab" role="tabpanel">
                                <div id="bayut-gauge-chart"
                                     style="height: 200px;"
                                     data-dataset="{{ $bayutData["quality"]["rank_avg"] ?? 0 }}"
                                >
                                </div>
                            </div>
                            <div class="tab-pane active" id="bayut-rank-bar-tab" role="tabpanel">
                                <div id="bayut-rank-bar-chart"
                                     style="height: 200px;"
                                     data-dataset="{!! $bayutData["quality"]["ranks"] ?? 0 !!}"
                                >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane active" id="bayut-verified-tab" role="tabpanel">
                        <div id="bayut-verified-pie-chart"
                             style="height: 200px;"
                             data-dataset="{{ json_encode($bayutData["verified"]["list"] ?? []) }}"
                        >
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <h2 class="me-3 ff-secondary mb-0 bayut_pie_count">{{ $bayutData["product"]["portal_total"] ?? 0 }}</h2>
                                <div>
                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0">
                                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                                class="ri-check-double-line"></i></span> <span
                                            class="bayut_verfied_count">{{ $bayutData["verified"]["verified"] ?? 0 }}</span> Verified
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xxl-4 col-lg-4">
        <div class="card card-height-100 mb-3">
            <div class="card-header align-items-center border-0 d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Dubizzle</h4>
                <div class="flex-shrink-0">
                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#dubizzle-product-tab"
                               role="tab" aria-selected="false">Product</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab"
                               href="#dubizzle-rank-tab" role="tab" aria-selected="true">Quality</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#dubizzle-verified-tab"
                               role="tab" aria-selected="true">Verified</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body" style="max-height: 400px;">
                <div class="tab-content p-0">
                    <div class="tab-pane active" id="dubizzle-product-tab" role="tabpanel">
                        <div
                            id="dubizzle-level-chart"
                            style="height: 200px;"
                            data-dataset="{{ json_encode($dubizzleData["product"]["levels"] ?? []) }}"
                        ></div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <h2 class="me-3 ff-secondary mb-0 dubizzle_count">{{ $dubizzleData["product"]["portal_total"] ?? 0 }}</h2>
                                <div>
                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0 d-none">
                                        <span class="badge badge-soft-success p-1 rounded-circle">
                                            <i class="ri-arrow-right-up-line"></i>
                                        </span> +0 New
                                    </p>
                                </div>
                            </div>
                            @foreach($dubizzleData["product"]["levels"] ?? [] as $level)
                                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                    <p class="fw-medium mb-0">
                                        <i class="ri-checkbox-blank-circle-fill text-{{ $level->name == 'Featured' ? 'primary' : ($level->name == 'Premium' ? 'danger' : 'success') }} align-middle me-2"></i>
                                        {{ $level->name }}</p>
                                    <div>
                                        <span class="text-muted dubizzle_premium">{{ $level->count }} Listings</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane active" id="dubizzle-rank-tab" role="tabpanel">
                        <div class="d-flex mt-2">
                            <h4 class=" flex-grow-1"></h4>
                            <div class="flex-shrink-0">
                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#dubizzle-rank-meter-tab" role="tab"
                                           aria-selected="false"
                                           class="btn btn-soft-secondary btn-sm">
                                            <i class=" bx bx-tachometer"></i></a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#dubizzle-rank-bar-tab" role="tab"
                                           aria-selected="false"
                                           class="btn btn-soft-secondary btn-sm">
                                            <i class="ri-bar-chart-2-fill"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content p-0">
                            <div class="tab-pane active" id="dubizzle-rank-meter-tab" role="tabpanel">
                                <div id="dubizzle-gauge-chart"
                                     style="height: 200px;"
                                     data-dataset="{{ $dubizzleData["quality"]["rank_avg"] ?? 0 }}"
                                >
                                </div>
                            </div>
                            <div class="tab-pane active" id="dubizzle-rank-bar-tab" role="tabpanel">
                                <div id="dubizzle-rank-bar-chart"
                                     style="height: 200px;"
                                     data-dataset="{{ $dubizzleData["quality"]["ranks"] ?? 0 }}"
                                >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane active" id="dubizzle-verified-tab" role="tabpanel">
                        <div id="dubizzle-verified-pie-chart"
                             style="height: 200px;"
                             data-dataset="{{ json_encode($dubizzleData["verified"]["list"] ?? []) }}"
                        >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
