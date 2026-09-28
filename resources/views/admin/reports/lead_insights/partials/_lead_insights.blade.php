<h4>Lead Insights</h4>
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Lead Source</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="graph-container" id="lead-insights-lead-source-wise-overview-chart"
                     data-dataset="{{ json_encode($insightData["overview_source_wise"]) }}"></div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Leads over time</h5>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active lead-insights-lead-source-nav-link" id="nav-lead-insights-lead-source-overall-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-lead-insights-lead-source-overall" type="button"
                                        role="tab"
                                        aria-controls="nav-lead-insights-lead-source-overall"
                                        aria-selected="false">Overall
                                </button>
                                <button class="nav-link lead-insights-lead-source-nav-link" id="nav-lead-insights-lead-source-pf-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-lead-insights-lead-source-pf" type="button"
                                        role="tab"
                                        aria-controls="nav-lead-insights-lead-source-pf"
                                        aria-selected="false">Property Finder
                                </button>
                                <button class="nav-link lead-insights-lead-source-nav-link" id="nav-lead-insights-lead-source-bayut-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-lead-insights-lead-source-bayut" type="button"
                                        role="tab"
                                        aria-controls="nav-lead-insights-lead-source-bayut"
                                        aria-selected="false">Bayut
                                </button>
                                <button class="nav-link lead-insights-lead-source-nav-link" id="nav-lead-insights-lead-source-dubizzle-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-lead-insights-lead-source-dubizzle" type="button"
                                        role="tab"
                                        aria-controls="nav-lead-insights-lead-source-dubizzle"
                                        aria-selected="false">Dubizzle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active lead-insights-lead-source-tab-pane" id="nav-lead-insights-lead-source-overall" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-overall-tab">
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <div class="col" >
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Property Finder</h5>
                                        <div class="card-text">
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                <span>{{ $insightData["overview_source_wise"]["pf_count"] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                        <div class="card-text">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Email
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span>{{ collect($insightData["date_wise_source_wise_counts"]["pf"]["email_counts"])->sum() }}</span>
                                                    </h4>
                                                </div>
                                                <div>
                                                    Calls
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span>{{ collect($insightData["date_wise_source_wise_counts"]["pf"]["call_counts"])->sum() }}</span>
                                                    </h4>
                                                </div>
                                                <div>
                                                    WhatsApp
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span>{{ collect($insightData["date_wise_source_wise_counts"]["pf"]["wa_counts"])->sum() }}</span>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Bayut</h5>
                                        <div class="card-text">
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                <span>{{ $insightData["overview_source_wise"]["bayut_count"] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                        <div class="card-text">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Email
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span>{{ collect($insightData["date_wise_source_wise_counts"]["bayut"]["email_counts"])->sum() }}</span>
                                                    </h4>
                                                </div>
                                                <div>
                                                    Calls
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span>{{ collect($insightData["date_wise_source_wise_counts"]["bayut"]["call_counts"])->sum() }}</span>
                                                    </h4>
                                                </div>
                                                <div>
                                                    WhatsApp
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span>{{ collect($insightData["date_wise_source_wise_counts"]["bayut"]["wa_counts"])->sum() }}</span>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Dubizzle</h5>
                                        <div class="card-text">
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                <span>{{ $insightData["overview_source_wise"]["dubizzle_count"] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                        <div class="card-text">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    Email
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span>{{ collect($insightData["date_wise_source_wise_counts"]["dubizzle"]["email_counts"])->sum() }}</span>
                                                    </h4>
                                                </div>
                                                <div>
                                                    Calls
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span>{{ collect($insightData["date_wise_source_wise_counts"]["dubizzle"]["call_counts"])->sum() }}</span>
                                                    </h4>
                                                </div>
                                                <div>
                                                    WhatsApp
                                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                        <span>{{ collect($insightData["date_wise_source_wise_counts"]["dubizzle"]["wa_counts"])->sum() }}</span>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade show active lead-insights-lead-source-tab-pane" id="nav-lead-insights-lead-source-pf" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-pf-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" id="lead-insights-lead-source-pf-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["date_wise_source_wise_counts"]["pf"] ?? []) }}"></div>
                    </div>
                    <div class="tab-pane fade show active lead-insights-lead-source-tab-pane" id="nav-lead-insights-lead-source-bayut" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-bayut-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" id="lead-insights-lead-source-bayut-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["date_wise_source_wise_counts"]["bayut"] ?? []) }}"></div>
                    </div>
                    <div class="tab-pane fade show active lead-insights-lead-source-tab-pane" id="nav-lead-insights-lead-source-dubizzle" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-dubizzle-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" id="lead-insights-lead-source-dubizzle-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["date_wise_source_wise_counts"]["dubizzle"] ?? []) }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mt-2">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Leads Medium Wise</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="graph-container" id="lead-insights-lead-overview-chart"
                     data-dataset="{{ json_encode($insightData["overview"]) }}"></div>
            </div>
        </div>
    </div>
    <div class="col-md-8 mt-2">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Medium Wise leads over time</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="graph-container" id="lead-insights-lead-count-date-wise-chart"
                     data-dataset="{{ json_encode($insightData["date_wise_counts"]) }}"></div>
            </div>
        </div>
    </div>
</div>
