{{-- @dd($insightData["listing_overview_source_wise"]) --}}
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
                <div class="graph-container" style="height: 200px;" id="lead-insights-lead-source-wise-overview-chart"
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
                                <button class="nav-link lead-insights-lead-source-nav-link" id="nav-lead-insights-lead-source-other-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-lead-insights-lead-source-other" type="button"
                                        role="tab"
                                        aria-controls="nav-lead-insights-lead-source-other"
                                        aria-selected="false">Other
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
                        <div class="row g-2">
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
                                            <ul style="list-style-type: none; margin-left: -35px">
                                                <li title="Email"><i class="las la-envelope"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["pf"]["email_counts"])->sum() }}</span></li>
                                                <li title="WhatsApp"><i class="lab la-whatsapp"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["pf"]["wa_counts"])->sum() }}</span></li>
                                                <li title="Call"><i class="las la-phone"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["pf"]["call_counts"])->sum() }}</span></li>
                                                <li title="Other"><i class="las la-low-vision"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["pf"]["other_counts"])->sum() }}</span></li>
                                            </ul>
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
                                            <ul style="list-style-type: none; margin-left: -35px">
                                                <li title="Email"><i class="las la-envelope"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["bayut"]["email_counts"])->sum() }}</span></li>
                                                <li title="WhatsApp"><i class="lab la-whatsapp"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["bayut"]["wa_counts"])->sum() }}</span></li>
                                                <li title="Call"><i class="las la-phone"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["bayut"]["call_counts"])->sum() }}</span></li>
                                                <li title="Other"><i class="las la-low-vision"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["bayut"]["other_counts"])->sum() }}</span></li>
                                            </ul>
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
                                            <ul style="list-style-type: none; margin-left: -35px">
                                                <li title="Email"><i class="las la-envelope"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["dubizzle"]["email_counts"])->sum() }}</span></li>
                                                <li title="WhatsApp"><i class="lab la-whatsapp"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["dubizzle"]["wa_counts"])->sum() }}</span></li>
                                                <li title="Call"><i class="las la-phone"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["dubizzle"]["call_counts"])->sum() }}</span></li>
                                                <li title="Other"><i class="las la-low-vision"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["dubizzle"]["other_counts"])->sum() }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Other</h5>
                                        <div class="card-text">
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                <span>{{ $insightData["overview_source_wise"]["other_count"] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                        <div class="card-text">
                                            <ul style="list-style-type: none; margin-left: -35px">
                                                <li title="Email"><i class="las la-envelope"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["other"]["email_counts"])->sum() }}</span></li>
                                                <li title="WhatsApp"><i class="lab la-whatsapp"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["other"]["call_counts"])->sum() }}</span></li>
                                                <li title="Call"><i class="las la-phone"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["other"]["wa_counts"])->sum() }}</span></li>
                                                <li title="Other"><i class="las la-low-vision"></i> <span>{{ collect($insightData["date_wise_source_wise_counts"]["other"]["other_counts"])->sum() }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade show active lead-insights-lead-source-tab-pane" id="nav-lead-insights-lead-source-pf" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-pf-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" style="height: 200px;" id="lead-insights-lead-source-pf-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["date_wise_source_wise_counts"]["pf"] ?? []) }}"></div>
                    </div>
                    <div class="tab-pane fade show active lead-insights-lead-source-tab-pane" style="height: 200px;" id="nav-lead-insights-lead-source-bayut" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-bayut-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" style="height: 200px;" id="lead-insights-lead-source-bayut-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["date_wise_source_wise_counts"]["bayut"] ?? []) }}"></div>
                    </div>
                    <div class="tab-pane fade show active lead-insights-lead-source-tab-pane" id="nav-lead-insights-lead-source-dubizzle" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-dubizzle-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" style="height: 200px;" id="lead-insights-lead-source-dubizzle-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["date_wise_source_wise_counts"]["dubizzle"] ?? []) }}"></div>
                    </div>
                    <div class="tab-pane fade show active lead-insights-lead-source-tab-pane" id="nav-lead-insights-lead-source-other" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-other-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" style="height: 200px;" id="lead-insights-lead-source-other-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["date_wise_source_wise_counts"]["other"] ?? []) }}"></div>
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
                <div class="graph-container" style="height: 200px;" id="lead-insights-lead-overview-chart"
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
                <div class="graph-container" style="height: 200px;" id="lead-insights-lead-count-date-wise-chart"
                     data-dataset="{{ json_encode($insightData["date_wise_counts"]) }}"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mt-2">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Listing Lead Source</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="graph-container" style="height: 200px;" id="listing-lead-insights-lead-source-wise-overview-chart"
                     data-dataset="{{ json_encode($insightData["listing_overview_source_wise"]) }}"></div>
            </div>
        </div>
    </div>
    <div class="col-md-8 mt-2">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Listing Leads over time</h5>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active listing-lead-insights-lead-source-nav-link" id="nav-listing-lead-insights-lead-source-overall-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-listing-lead-insights-lead-source-overall" type="button"
                                        role="tab"
                                        aria-controls="nav-listing-lead-insights-lead-source-overall"
                                        aria-selected="false">Overall
                                </button>
                                <button class="d-none nav-link listing-lead-insights-lead-source-nav-link" id="nav-listing-lead-insights-lead-source-pf-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-listing-lead-insights-lead-source-pf" type="button"
                                        role="tab"
                                        aria-controls="nav-listing-lead-insights-lead-source-pf"
                                        aria-selected="false">Dialmydata
                                </button>
                                <button class="d-none nav-link listing-lead-insights-lead-source-nav-link" id="nav-listing-lead-insights-lead-source-bayut-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-listing-lead-insights-lead-source-bayut" type="button"
                                        role="tab"
                                        aria-controls="nav-listing-lead-insights-lead-source-bayut"
                                        aria-selected="false">Axchat
                                </button>
                                <button class="d-none nav-link listing-lead-insights-lead-source-nav-link" id="nav-listing-lead-insights-lead-source-dubizzle-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-listing-lead-insights-lead-source-dubizzle" type="button"
                                        role="tab"
                                        aria-controls="nav-listing-lead-insights-lead-source-dubizzle"
                                        aria-selected="false">Axcontacts
                                </button>
                                <button class="d-none nav-link listing-lead-insights-lead-source-nav-link" id="nav-listing-lead-insights-lead-source-other-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-listing-lead-insights-lead-source-other" type="button"
                                        role="tab"
                                        aria-controls="nav-listing-lead-insights-lead-source-other"
                                        aria-selected="false">Other
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active listing-lead-insights-lead-source-tab-pane" id="nav-listing-lead-insights-lead-source-overall" role="tabpanel"
                         aria-labelledby="nav-listing-lead-insights-lead-source-overall-tab">
                        <div class="row g-2">
                            <div class="col" >
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Dialmydata</h5>
                                        <div class="card-text">
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                <span>{{ $insightData["listing_overview_source_wise"]["dialmydata"] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                        {{-- <div class="card-text">
                                            <ul style="list-style-type: none; margin-left: -35px">
                                                <li title="Email"><i class="las la-envelope"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["dialmydata"]["dialmydatas"])->sum() }}</span></li>
                                                <li title="WhatsApp"><i class="lab la-whatsapp"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["dialmydata"]["ax_chats"])->sum() }}</span></li>
                                                <li title="Call"><i class="las la-phone"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["dialmydata"]["ax_contacts"])->sum() }}</span></li>
                                                <li title="Other"><i class="las la-low-vision"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["dialmydata"]["other_counts"])->sum() }}</span></li>
                                            </ul>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">AX CHATS</h5>
                                        <div class="card-text">
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                <span>{{ $insightData["listing_overview_source_wise"]["ax_chat"] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                        {{-- <div class="card-text">
                                            <ul style="list-style-type: none; margin-left: -35px">
                                                <li title="Email"><i class="las la-envelope"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["dialmydata"]["dialmydatas"])->sum() }}</span></li>
                                                <li title="WhatsApp"><i class="lab la-whatsapp"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["axchat"]["ax_chats"])->sum() }}</span></li>
                                                <li title="Call"><i class="las la-phone"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["axchat"]["ax_contacts"])->sum() }}</span></li>
                                                <li title="Other"><i class="las la-low-vision"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["axchat"]["other_counts"])->sum() }}</span></li>
                                            </ul>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">AX CONTACTS</h5>
                                        <div class="card-text">
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                <span>{{ $insightData["listing_overview_source_wise"]["ax_contact"] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                        {{-- <div class="card-text">
                                            <ul style="list-style-type: none; margin-left: -35px">
                                                <li title="Email"><i class="las la-envelope"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["axcontact"]["dialmydatas"])->sum() }}</span></li>
                                                <li title="WhatsApp"><i class="lab la-whatsapp"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["axcontact"]["ax_chats"])->sum() }}</span></li>
                                                <li title="Call"><i class="las la-phone"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["axcontact"]["ax_contacts"])->sum() }}</span></li>
                                                <li title="Other"><i class="las la-low-vision"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["axcontact"]["other_counts"])->sum() }}</span></li>
                                            </ul>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Other</h5>
                                        <div class="card-text">
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                <span>{{ $insightData["listing_overview_source_wise"]["other"] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                        {{-- <div class="card-text">
                                            <ul style="list-style-type: none; margin-left: -35px">
                                                <li title="Email"><i class="las la-envelope"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["other"]["dialmydatas"])->sum() }}</span></li>
                                                <li title="WhatsApp"><i class="lab la-whatsapp"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["other"]["ax_chats"])->sum() }}</span></li>
                                                <li title="Call"><i class="las la-phone"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["other"]["ax_contacts"])->sum() }}</span></li>
                                                <li title="Other"><i class="las la-low-vision"></i> <span>{{ collect($insightData["listing_date_wise_source_wise_counts"]["other"]["other_counts"])->sum() }}</span></li>
                                            </ul>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade show active listing-lead-insights-lead-source-tab-pane" id="nav-listing-lead-insights-lead-source-pf" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-pf-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" style="height: 200px;" id="listing-lead-insights-lead-source-pf-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["listing_date_wise_source_wise_counts"]["dialmydata"] ?? []) }}"></div>
                    </div>
                    <div class="tab-pane fade show active listing-lead-insights-lead-source-tab-pane" style="height: 200px;" id="nav-listing-lead-insights-lead-source-bayut" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-bayut-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" style="height: 200px;" id="listing-lead-insights-lead-source-bayut-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["listing_date_wise_source_wise_counts"]["axchat"] ?? []) }}"></div>
                    </div>
                    <div class="tab-pane fade show active listing-lead-insights-lead-source-tab-pane" id="nav-listing-lead-insights-lead-source-dubizzle" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-dubizzle-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" style="height: 200px;" id="listing-lead-insights-lead-source-dubizzle-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["listing_date_wise_source_wise_counts"]["axcontact"] ?? []) }}"></div>
                    </div>
                    <div class="tab-pane fade show active listing-lead-insights-lead-source-tab-pane" id="nav-listing-lead-insights-lead-source-other" role="tabpanel"
                         aria-labelledby="nav-lead-insights-lead-source-other-tab">
                        <div class="graph-container lead-insights-lead-source-medium-chart" style="height: 200px;" id="listing-lead-insights-lead-source-other-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["listing_date_wise_source_wise_counts"]["other"] ?? []) }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
