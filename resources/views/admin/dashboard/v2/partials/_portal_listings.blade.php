{{-- @dd($propertyFinderData["quality"]["ranks"]) --}}

{{-- @dd($dubizzleData) --}}
<div class="row">



    <div class="col-xxl-4 col-lg-4">
        <div class="card card-height-100 mb-3 shadow-lg rounded-lg border-0">
            <div class="card-header align-items-center border-0 d-flex p-3 bg-light rounded-top">
                <h4 class="card-title mb-0 flex-grow-1 text-dark">PF</h4>
                <div class="flex-shrink-0">
                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#pf-product-tab" role="tab"
                                aria-selected="false">
                                <i class="ri-home-line me-1"></i> Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#pf-rank-tab" role="tab"
                                aria-selected="true">
                                <i class="ri-star-line me-1"></i> Quality
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#pf-verified-tab" role="tab"
                                aria-selected="true">
                                <i class="ri-check-line me-1"></i> Verified
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body p-4" style="max-height: 420px;">
                <div class="tab-content p-0">
                    <div class="tab-pane active" id="pf-product-tab" role="tabpanel">
                        <div id="pf-level-chart" style="height: 200px;"
                            data-dataset="{{ json_encode($propertyFinderData['pf_level_counts'] ?? []) }}"></div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                @php
                                    $pfTotalPropertiesCount = array_sum($propertyFinderData['pf_level_counts'] ?? []);
                                @endphp

                                <h2 class="me-3 ff-secondary mb-0 text-dark dubizzle_count">
                                    {{ $pfTotalPropertiesCount ?? 0 }}</h2>


                                <div>
                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0 d-none">
                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                class="ri-arrow-right-up-line"></i></span> +0 New
                                    </p>
                                </div>
                            </div>
                            @foreach ($propertyFinderData['pf_level_counts'] ?? [] as $key => $level)
                                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                    <p class="fw-medium mb-0 text-muted">
                                        <i
                                            class="ri-checkbox-blank-circle-fill text-{{ $key == 'Featured' ? 'primary' : ($key == 'Premium' ? 'danger' : 'success') }} align-middle me-2"></i>
                                        {{ $key }}
                                    </p>
                                    <div>
                                        <span class="text-muted pf_premium">{{ $level }} Listings</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="tab-pane active" id="pf-rank-tab" role="tabpanel">
                        <div class="d-flex mt-2">
                            <div class="flex-grow-1"></div>
                            <div class="flex-shrink-0">
                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#pf-rank-meter-tab" role="tab"
                                            aria-selected="false" class="btn btn-soft-secondary btn-sm rounded-circle">
                                            <i class="bx bx-tachometer"></i>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#pf-rank-bar-tab" role="tab"
                                            aria-selected="false" class="btn btn-soft-secondary btn-sm rounded-circle">
                                            <i class="ri-bar-chart-2-fill"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content p-0">
                            <div class="tab-pane active" id="pf-rank-meter-tab" role="tabpanel">
                                <div id="pf-gauge-chart" style="height: 200px;"
                                    data-dataset="{{ $propertyFinderData['average_pf_rank'] ?? 0 }}"></div>
                                <h5 class="text-dark mt-2">Avg Score: <span
                                        class="text-muted">{{ $propertyFinderData['average_pf_rank'] ?? 0 }}</span></h5>
                            </div>
                            <div class="tab-pane active" id="pf-rank-bar-tab" role="tabpanel">
                                <div id="pf-rank-bar-chart" style="height: 200px;"
                                    data-dataset='{!! json_encode($propertyFinderData['pf_rank_ranges']) !!}'></div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane active" id="pf-verified-tab" role="tabpanel">
                        <div id="pf-verified-pie-chart" style="height: 200px;"
                            data-val1="{{ $propertyFinderData['verified_count'] }}"
                            data-val2="{{ $propertyFinderData['total_properties_count'] - $propertyFinderData['verified_count'] }}"
                            data-lab1="Verified" data-lab2="Unverified">
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <h2 class="me-3 ff-secondary mb-0 pf_pie_count text-dark">
                                    {{ $propertyFinderData['total_properties_count'] ?? 0 }}</h2>
                                <div>
                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0">
                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                class="ri-check-double-line"></i></span>
                                        <span
                                            class="pf_verified_count">{{ $propertyFinderData['verified_count'] ?? 0 }}</span>
                                        Verified
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
        <div class="card card-height-100 mb-3 shadow-lg rounded-lg border-0">
            <div class="card-header align-items-center border-0 d-flex p-3 bg-light rounded-top">
                <h4 class="card-title mb-0 flex-grow-1 text-dark">Bayut</h4>
                <div class="flex-shrink-0">
                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#bayut-product-tab" role="tab"
                                aria-selected="false">
                                <i class="ri-home-line me-1"></i> Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#bayut-rank-tab" role="tab"
                                aria-selected="true">
                                <i class="ri-star-line me-1"></i> Quality
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#bayut-verified-tab" role="tab"
                                aria-selected="true">
                                <i class="ri-check-line me-1"></i> Verified
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body p-4" style="max-height: 420px;">
                <div class="tab-content p-0">
                    <div class="tab-pane active" id="bayut-product-tab" role="tabpanel">
                        <div id="bayut-level-chart" style="height: 200px;"
                            data-dataset="{{ json_encode($bayutData['bayut_level_counts'] ?? []) }}"></div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                @php
                                    $bayutTotalPropertiesCount = array_sum($bayutData['bayut_level_counts'] ?? []);
                                @endphp

                                <h2 class="me-3 ff-secondary mb-0 text-dark dubizzle_count">
                                    {{ $bayutTotalPropertiesCount ?? 0 }}
                                </h2>
                                <div>

                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0 d-none">
                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                class="ri-arrow-right-up-line"></i></span> +0 New
                                    </p>
                                </div>
                            </div>
                            @foreach ($bayutData['bayut_level_counts'] ?? [] as $key => $level)
                                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                    <p class="fw-medium mb-0 text-muted">
                                        <i
                                            class="ri-checkbox-blank-circle-fill text-{{ $key == 'Signature' ? 'primary' : ($key == 'Hot' ? 'danger' : 'success') }} align-middle me-2"></i>
                                        {{ $key }}
                                    </p>
                                    <div>
                                        <span class="text-muted bayut_premium">{{ $level }} Listings</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="tab-pane active" id="bayut-rank-tab" role="tabpanel">
                        <div class="d-flex mt-2">
                            <div class="flex-grow-1"></div>
                            <div class="flex-shrink-0">
                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#bayut-rank-meter-tab" role="tab"
                                            aria-selected="false"
                                            class="btn btn-soft-secondary btn-sm rounded-circle">
                                            <i class="bx bx-tachometer"></i>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#bayut-rank-bar-tab" role="tab"
                                            aria-selected="false"
                                            class="btn btn-soft-secondary btn-sm rounded-circle">
                                            <i class="ri-bar-chart-2-fill"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content p-0">
                            <div class="tab-pane active" id="bayut-rank-meter-tab" role="tabpanel">
                                <div id="bayut-gauge-chart" style="height: 200px;"
                                    data-dataset="{{ $bayutData['average_bayut_rank'] ?? 0 }}"></div>
                                <h5 class="text-dark mt-2">Avg Score: <span
                                        class="text-muted">{{ $bayutData['average_bayut_rank'] ?? 0 }}</span></h5>
                            </div>
                            <div class="tab-pane active" id="bayut-rank-bar-tab" role="tabpanel">
                                <div id="bayut-rank-bar-chart" style="height: 200px;"
                                    data-dataset='{!! json_encode($bayutData['bayut_rank_ranges']) !!}'></div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane active" id="bayut-verified-tab" role="tabpanel">
                        <div id="bayut-verified-pie-chart" style="height: 200px;"
                            data-val1="{{ $bayutData['verified_count'] }}"
                            data-val2="{{ $bayutData['total_properties_count'] - $bayutData['verified_count'] }}"
                            data-lab1="TruChecked" data-lab2="Unverified"></div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <h2 class="me-3 ff-secondary mb-0 bayut_pie_count text-dark">
                                    {{ $bayutData['total_properties_count'] ?? 0 }}</h2>
                                <div>
                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0">
                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                class="ri-check-double-line"></i></span>
                                        <span
                                            class="bayut_verified_count">{{ $bayutData['verified_count'] ?? 0 }}</span>
                                        Verified
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
        <div class="card card-height-100 mb-3 shadow-lg rounded-lg border-0">
            <div class="card-header align-items-center border-0 d-flex p-3 bg-light rounded-top">
                <h4 class="card-title mb-0 flex-grow-1 text-dark">Dubizzle</h4>
                <div class="flex-shrink-0">
                    <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#dubizzle-product-tab"
                                role="tab" aria-selected="false">
                                <i class="ri-home-line me-1"></i> Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#dubizzle-rank-tab" role="tab"
                                aria-selected="true">
                                <i class="ri-star-line me-1"></i> Quality
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#dubizzle-verified-tab" role="tab"
                                aria-selected="true">
                                <i class="ri-check-line me-1"></i> Verified
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body p-4" style="max-height: 420px;">
                <div class="tab-content p-0">
                    <div class="tab-pane active" id="dubizzle-product-tab" role="tabpanel">
                        <div id="dubizzle-level-chart" style="height: 200px;"
                            data-dataset="{{ json_encode($dubizzleData['dubizzle_level_counts'] ?? []) }}"></div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                @php
                                    $totalPropertiesCount = array_sum($dubizzleData['dubizzle_level_counts'] ?? []);
                                @endphp

                                <h2 class="me-3 ff-secondary mb-0 text-dark dubizzle_count">
                                    {{ $totalPropertiesCount ?? 0 }}</h2>
                                <div>
                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0 d-none">
                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                class="ri-arrow-right-up-line"></i></span> +0 New
                                    </p>
                                </div>
                            </div>
                            @foreach ($dubizzleData['dubizzle_level_counts'] ?? [] as $key => $level)
                                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                    <p class="fw-medium mb-0 text-muted">
                                        <i
                                            class="ri-checkbox-blank-circle-fill text-{{ $key == 'Premium' ? 'primary' : ($key == 'Featured' ? 'danger' : 'success') }} align-middle me-2"></i>
                                        {{ $key }}
                                    </p>
                                    <div>
                                        <span class="text-muted dubizzle_premium">{{ $level }} Listings</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="tab-pane active" id="dubizzle-rank-tab" role="tabpanel">
                        <div class="d-flex mt-2">
                            <div class="flex-grow-1"></div>
                            <div class="flex-shrink-0">
                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#dubizzle-rank-meter-tab" role="tab"
                                            aria-selected="false"
                                            class="btn btn-soft-secondary btn-sm rounded-circle">
                                            <i class="bx bx-tachometer"></i>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#dubizzle-rank-bar-tab" role="tab"
                                            aria-selected="false"
                                            class="btn btn-soft-secondary btn-sm rounded-circle">
                                            <i class="ri-bar-chart-2-fill"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content p-0">
                            <div class="tab-pane active" id="dubizzle-rank-meter-tab" role="tabpanel">
                                <div id="dubizzle-gauge-chart" style="height: 200px;"
                                    data-dataset="{{ $dubizzleData['average_dubizzle_rank'] ?? 0 }}"></div>
                                <h5 class="text-dark mt-2">Avg Score: <span
                                        class="text-muted">{{ $dubizzleData['average_dubizzle_rank'] ?? 0 }}</span>
                                </h5>
                            </div>
                            <div class="tab-pane active" id="dubizzle-rank-bar-tab" role="tabpanel">
                                <div id="dubizzle-rank-bar-chart" style="height: 200px;"
                                    data-dataset='{!! json_encode($dubizzleData['dubizzle_rank_ranges']) !!}'></div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane active" id="dubizzle-verified-tab" role="tabpanel">
                        <div id="dubizzle-verified-pie-chart" style="height: 200px;"
                            data-val1="{{ $dubizzleData['verified_count'] }}"
                            data-val2="{{ $dubizzleData['total_properties_count'] - $dubizzleData['verified_count'] }}"
                            data-lab1="TruChecked" data-lab2="Unverified"></div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <h2 class="me-3 ff-secondary mb-0 dubizzle_pie_count text-dark">
                                    {{ $dubizzleData['total_properties_count'] ?? 0 }}</h2>
                                <div>
                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0">
                                        <span class="badge badge-soft-success p-1 rounded-circle"><i
                                                class="ri-check-double-line"></i></span>
                                        <span
                                            class="dubizzle_verified_count">{{ $dubizzleData['verified_count'] ?? 0 }}</span>
                                        Verified
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- <div class="col-xxl-4 col-lg-4">
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
                            <a class="nav-link" data-bs-toggle="tab" href="#dubizzle-rank-tab" role="tab"
                                aria-selected="true">Quality</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#dubizzle-verified-tab" role="tab"
                                aria-selected="true">Verified</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body" style="max-height: 420px;">
                <div class="tab-content p-0">
                    <div class="tab-pane active" id="dubizzle-product-tab" role="tabpanel">
                        <div id="dubizzle-level-chart" style="height: 200px;"
                            data-dataset="{{ json_encode($dubizzleData['product']['levels'] ?? []) }}"></div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <h2 class="me-3 ff-secondary mb-0 dubizzle_count">
                                    {{ $dubizzleData['product']['portal_total'] ?? 0 }}</h2>
                                <div>
                                    <p class="text-muted mb-0">Total Listings</p>
                                    <p class="text-success fw-medium mb-0 d-none">
                                        <span class="badge badge-soft-success p-1 rounded-circle">
                                            <i class="ri-arrow-right-up-line"></i>
                                        </span> +0 New
                                    </p>
                                </div>
                            </div>
                            @foreach ($dubizzleData['product']['levels'] ?? [] as $level)
                                <div class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                                    <p class="fw-medium mb-0">
                                        <i
                                            class="ri-checkbox-blank-circle-fill text-{{ $level->name == 'Featured' ? 'primary' : ($level->name == 'Premium' ? 'danger' : 'success') }} align-middle me-2"></i>
                                        {{ $level->name }}
                                    </p>
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
                                            aria-selected="false" class="btn btn-soft-secondary btn-sm">
                                            <i class=" bx bx-tachometer"></i></a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" href="#dubizzle-rank-bar-tab" role="tab"
                                            aria-selected="false" class="btn btn-soft-secondary btn-sm">
                                            <i class="ri-bar-chart-2-fill"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content p-0">
                            <div class="tab-pane active" id="dubizzle-rank-meter-tab" role="tabpanel">
                                <div id="dubizzle-gauge-chart" style="height: 200px;"
                                    data-dataset="{{ $dubizzleData['quality']['rank_avg'] ?? 0 }}">
                                </div>
                            </div>
                            <div class="tab-pane active" id="dubizzle-rank-bar-tab" role="tabpanel">
                                <div id="dubizzle-rank-bar-chart" style="height: 200px;"
                                    data-dataset="{{ $dubizzleData['quality']['ranks'] ?? 0 }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane active" id="dubizzle-verified-tab" role="tabpanel">
                        <div id="dubizzle-verified-pie-chart" style="height: 200px;"
                            data-dataset="{{ json_encode($dubizzleData['verified']['list'] ?? []) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>


<script>
    createDonutChartFromDataset('pf-level-chart');
    renderPortalQualityChart("pf-gauge-chart");
    createBarChartFromDataset('pf-rank-bar-chart');
    renderPortalPieChartTruChecked("pf-verified-pie-chart");

    createDonutChartFromDataset('bayut-level-chart');
    renderPortalQualityChart("bayut-gauge-chart");
    createBarChartFromDataset('bayut-rank-bar-chart');
    renderPortalPieChartTruChecked("bayut-verified-pie-chart");

    createDonutChartFromDataset('dubizzle-level-chart');
    renderPortalQualityChart("dubizzle-gauge-chart");
    createBarChartFromDataset('dubizzle-rank-bar-chart');
    renderPortalPieChartTruChecked("dubizzle-verified-pie-chart");

    $('#pf-rank-bar-tab').removeClass('active');
    $('#pf-rank-tab').removeClass('active');
    $('#pf-verified-tab').removeClass('active');

    $('#bayut-rank-bar-tab').removeClass('active');
    $('#bayut-rank-tab').removeClass('active');
    $('#bayut-verified-tab').removeClass('active');

    $('#dubizzle-rank-bar-tab').removeClass('active');
    $('#dubizzle-rank-tab').removeClass('active');
    $('#dubizzle-verified-tab').removeClass('active');
</script>
