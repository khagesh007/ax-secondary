<div class="col-xxl-4 col-lg-4">
    <div class="card card-height-100 mb-3">
        <div class="card-header align-items-center border-0 d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Property Finder</h4>
            <div class="flex-shrink-0">
                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                    role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#pf-product-tab"
                           role="tab"
                           aria-selected="false">Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" onclick="pfRankMeterChart()" data-bs-toggle="tab"
                           href="#pf-rank-tab" role="tab"
                           aria-selected="true">Quality</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"  data-bs-toggle="tab"
                           href="#pf-verified-tab" role="tab"
                           aria-selected="true">Verified</a>
                    </li>
                </ul><!-- end ul -->
            </div>
        </div>

        <div class="card-body" style="max-height: 400px;">
            <div class="tab-content p-0">
                <div class="tab-pane active" id="pf-product-tab" role="tabpanel">


                    <div id="pf-status"
                         data-colors='["--vz-success", "--vz-primary","--vz-danger", "--vz-warning"]'
                         class="apex-charts" dir="ltr"></div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <h2 class="me-3 ff-secondary mb-0 pf_count">0</h2>
                            <div>
                                <p class="text-muted mb-0">Total Listings</p>
                                <p class="text-success fw-medium mb-0 d-none">
                     <span class="badge badge-soft-success p-1 rounded-circle"><i
                             class="ri-arrow-right-up-line"></i></span> +0 New
                                </p>
                            </div>
                        </div>
                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed  py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-danger align-middle me-2"></i>
                                Premium</p>
                            <div>
                                <span class="text-muted pf_premium">0</span>

                            </div>
                        </div><!-- end -->
                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>
                                Featured</p>
                            <div>
                                <span class="text-muted pf_featured">0</span>

                            </div>
                        </div><!-- end -->
                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                Standard</p>
                            <div>
                                <span class="text-muted  pf_standard">0</span>

                            </div>
                        </div><!-- end -->




                    </div>
                </div>
                <div class="tab-pane" id="pf-verified-tab" role="tabpanel">

                    <div id="pfVerifiedPieChart" data-colors="[&quot;--vz-success&quot;]"
                         class="apex-charts" dir="ltr">
                    </div>

                </div>
                <div class="tab-pane" id="pf-rank-tab" role="tabpanel">
                    <div class="d-flex mt-2">
                        <h4 class=" flex-grow-1"></h4>
                        <div class="flex-shrink-0">
                            <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                role="tablist">
                                <li class="nav-item">

                                    <a data-bs-toggle="tab" href="#pf-rank-meter-tab"
                                       role="tab"
                                       aria-selected="false" onclick="pfRankMeterChart()"
                                       class="btn btn-soft-secondary btn-sm">
                                        <i class=" bx bx-tachometer"></i></a>
                                </li>
                                <li class="nav-item">
                                    <a data-bs-toggle="tab" href="#pf-rank-bar-tab"
                                       role="tab"
                                       aria-selected="false" onclick="pfRankBarChart()"
                                       class="btn btn-soft-secondary btn-sm">
                                        <i class="ri-bar-chart-2-fill"></i>
                                    </a>

                                </li>
                                <li class="nav-item">
                                    <a data-bs-toggle="tab" href="#pf-status-tab-child"
                                       role="tab"
                                       aria-selected="false" onclick="pfStatusPieChart()"
                                       class="btn btn-soft-secondary btn-sm">
                                       Status
                                    </a>

                                </li>
                            </ul><!-- end ul -->
                        </div>
                    </div>
                    <div class="tab-content p-0">
                        <div class="tab-pane active" id="pf-rank-meter-tab" role="tabpanel">
                            <div id="pf_rank_meter" style="position: relative;
                          height: 100vh;
                          overflow: hidden;">

                            </div>
                        </div>
                        <div class="tab-pane" id="pf-rank-bar-tab" role="tabpanel">
                            <div id="pf_bar_chart" style="position: relative;
                          height: 100vh;
                          overflow: hidden;">

                            </div>
                        </div>
                        <div class="tab-pane" id="pf-status-tab-child" role="tabpanel">
                            <div id="pf-status-pie"
                                 data-colors='["--vz-success", "--vz-danger","--vz-primary", "--vz-warning"]'
                                 class="apex-charts" dir="ltr"></div>
                            <div class="mt-3">

                                <div
                                    class="d-flex justify-content-between border-bottom border-bottom-dashed border-top border-top-dashed py-2">
                                    <p class="fw-medium mb-0"><i
                                            class="ri-whatsapp-fill text-success align-middle me-2"></i>
                                        Whatsapp AVG Response Time</p>
                                    <div>
                                        <span class="text-muted pe-5 pf_whatssapp_avg_time">0</span>

                                    </div>
                                </div><!-- end -->

                            </div>
                        </div>

                    </div>


                </div>
            </div><!-- end tab pane -->
        </div><!-- end cardbody -->
    </div><!-- end card -->
</div>
<div class="col-xxl-4 col-lg-4">
    <div class="card card-height-100 mb-3">
        <div class="card-header align-items-center border-0 d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Bayut</h4>
            <div class="flex-shrink-0">
                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                    role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#bayut-status-tab"
                           role="tab"
                           aria-selected="false">Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" onclick="bayutRankMeterChart()" data-bs-toggle="tab"
                           href="#bayut-quality-tab" role="tab"
                           aria-selected="true">Quality</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" onclick="bayutVerfiedChart()" data-bs-toggle="tab"
                           href="#bayut-verified-tab" role="tab"
                           aria-selected="true">Verified</a>
                    </li>


                </ul><!-- end ul -->
            </div>
        </div>

        <div class="card-body" style="max-height: 400px;">
            <div class="tab-content p-0">
                <div class="tab-pane active" id="bayut-status-tab" role="tabpanel">
                    <div id="bayut-pie-chart"
                         data-colors="[&quot;--vz-success&quot;, &quot;--vz-primary&quot;,  &quot;--vz-danger&quot;, &quot;--vz-warning&quot;]"
                         class="apex-charts" dir="ltr" style="min-height: 212.8px;">

                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <h2 class="me-3 ff-secondary mb-0 bayut_pie_count">0</h2>
                            <div>
                                <p class="text-muted mb-0">Total Listings</p>
                                <p class="text-success fw-medium mb-0 d-none">
                            <span class="badge badge-soft-success p-1 rounded-circle"><i
                                    class="ri-arrow-right-up-line"></i></span> +0 New
                                </p>
                            </div>
                        </div>

                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>
                                Signature</p>
                            <div>
                                <span class="text-muted bayut_signature">0 Listings</span>
                                {{-- <span class="text-success fw-medium fs-12">243hrs</span> --}}
                            </div>
                        </div><!-- end -->
                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-danger align-middle me-2"></i>Hot
                            </p>
                            <div>
                                <span class="text-muted bayut_hot">0 Listings</span>
                                {{-- <span class="text-success fw-medium fs-12">~2050hrs</span> --}}
                            </div>
                        </div><!-- end -->
                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                Standard</p>
                            <div>
                                <span class="text-muted bayut_standard">0 Listings</span>
                                {{-- <span class="text-success fw-medium fs-12">15870hrs</span> --}}
                            </div>
                        </div><!-- end -->



                    </div>
                </div>
                <div class="tab-pane" id="bayut-quality-tab" role="tabpanel"
                     style="    max-height: 400px;">
                    <div class="d-flex mt-2">
                        <h4 class=" flex-grow-1"></h4>
                        <div class="flex-shrink-0">
                            <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                role="tablist">
                                <li class="nav-item">

                                    <a data-bs-toggle="tab" href="#bayut-rank-meter-tab"
                                       role="tab"
                                       aria-selected="false" onclick="bayutRankMeterChart()"
                                       class="btn btn-soft-secondary btn-sm">
                                        <i class=" bx bx-tachometer"></i></a>
                                </li>
                                <li class="nav-item">
                                    <a data-bs-toggle="tab" href="#bayut-rank-bar-tab"
                                       role="tab"
                                       aria-selected="false" onclick="bayutQualityBarChart()"
                                       class="btn btn-soft-secondary btn-sm">
                                        <i class="ri-bar-chart-2-fill"></i>
                                    </a>

                                </li>
                            </ul><!-- end ul -->
                        </div>
                    </div>
                    <div class="tab-content p-0">
                        <div class="tab-pane active" id="bayut-rank-meter-tab" role="tabpanel">
                            <div id="bayut_rank_meter" style="position: relative;
                          height: 100vh;
                          overflow: hidden;">

                            </div>
                        </div>
                        <div class="tab-pane" id="bayut-rank-bar-tab" role="tabpanel">
                            <div id="bayut_quality_bar_chart" style="position: relative;
                          height: 100vh;
                          overflow: hidden;">

                            </div>
                        </div>

                    </div>


                </div>
                <div class="tab-pane" id="bayut-verified-tab" role="tabpanel">
                    <div id="bayut-verified-chart"
                         data-colors="[&quot;--vz-success&quot;, &quot;--vz-primary&quot;,&quot;--vz-warning&quot;]"
                         class="apex-charts" dir="ltr" style="min-height: 212.8px;">
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <h2 class="me-3 ff-secondary mb-0 bayut_pie_count">0</h2>
                            <div>
                                <p class="text-muted mb-0">Total Listings</p>
                                <p class="text-success fw-medium mb-0">
                            <span class="badge badge-soft-success p-1 rounded-circle"><i
                                    class="ri-check-double-line"></i></span> <span
                                        class="bayut_verfied_count"></span> Verified
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div><!-- end cardbody -->
    </div><!-- end card -->
</div>
<div class="col-xxl-4 col-lg-4">
    <div class="card card-height-100 mb-3">
        <div class="card-header align-items-center border-0 d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Dubizzle</h4>
            <div class="flex-shrink-0">
                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                    role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#dubizzle-status-tab"
                           role="tab"
                           aria-selected="false">Product</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab"
                           href="#dubizzle_quality_tab" role="tab"
                           aria-selected="true" onclick="dubizzleRankMeterChart()">Quality</a>
                    </li>

                </ul><!-- end ul -->
            </div>
        </div>

        <div class="card-body" style="max-height: 400px;">
            <div class="tab-content p-0">
                <div class="tab-pane active" id="dubizzle-status-tab" role="tabpanel">
                    <div id="dubizzle-doghnout-chart"
                         data-colors='["--vz-success", "--vz-primary", "--vz-warning","--vz-danger"]'
                         class="apex-charts" dir="ltr">

                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <h2 class="me-3 ff-secondary mb-0 dubizzle_total_count">0</h2>
                            <div>
                                <p class="text-muted mb-0">Total Listings</p>
                                <p class="text-success fw-medium mb-0 d-none">
                            <span class="badge badge-soft-success p-1 rounded-circle"><i
                                    class="ri-arrow-right-up-line"></i></span> +0 New
                                </p>
                            </div>
                        </div>
                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>Featured
                            </p>
                            <div>
                                <span class="text-muted dubizzle_featured">0 Listings</span>
                                {{-- <span class="text-success fw-medium fs-12">~2050hrs</span> --}}
                            </div>
                        </div><!-- end -->
                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                Standard</p>
                            <div>
                                <span class="text-muted dubizzle_standard">0 Listings</span>
                                {{-- <span class="text-success fw-medium fs-12">15870hrs</span> --}}
                            </div>
                        </div><!-- end -->



                    </div>
                </div>

                <div class="tab-pane" id="dubizzle_quality_tab" role="tabpanel">

                    <div class="d-flex mt-2">
                        <h4 class=" flex-grow-1"></h4>
                        <div class="flex-shrink-0">
                            <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                                role="tablist">
                                <li class="nav-item">

                                    <a data-bs-toggle="tab" href="#dubizzle-rank-meter-tab"
                                       role="tab"
                                       aria-selected="false" onclick="dubizzleRankMeterChart()"
                                       class="btn btn-soft-secondary btn-sm">
                                        <i class=" bx bx-tachometer"></i></a>
                                </li>
                                <li class="nav-item">
                                    <a data-bs-toggle="tab" href="#dubizzle-rank-bar-tab"
                                       role="tab"
                                       aria-selected="false" onclick="dubizzleQualityBarChart()"
                                       class="btn btn-soft-secondary btn-sm">
                                        <i class="ri-bar-chart-2-fill"></i>
                                    </a>

                                </li>
                            </ul><!-- end ul -->
                        </div>
                    </div>
                    <div class="tab-content p-0">
                        <div class="tab-pane active" id="dubizzle-rank-meter-tab" role="tabpanel">
                            <div id="dubbizle_rank_meter" style="position: relative;
                          height: 100vh;
                          overflow: hidden;">

                            </div>
                        </div>
                        <div class="tab-pane" id="dubizzle-rank-bar-tab" role="tabpanel">

                            <div id="dubizzle_quality_bar_chart" style="position: relative;
                          height: 100vh;
                          overflow: hidden;">

                            </div>

                        </div>

                    </div>

                </div>


            </div>
        </div><!-- end cardbody -->
    </div><!-- end card -->
</div>
