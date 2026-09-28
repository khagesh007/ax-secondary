{{--<style>--}}
{{--    .table-wrapper {--}}
{{--        max-height: 400px;--}}
{{--        overflow-y: auto;--}}
{{--        display: block;--}}
{{--    }--}}

{{--    .table-sticky thead th {--}}
{{--        position: sticky;--}}
{{--        top: 0;--}}
{{--        background: white;--}}
{{--        z-index: 100;--}}
{{--        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);--}}
{{--    }--}}
{{--</style>--}}

<style>
    .table-wrapper {
        max-height: 400px;
        overflow-y: auto;
        display: block;
    }

    .table-sticky thead th {
        position: sticky;
        top: 0;
        background: white;
        z-index: 100;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }

    .table-sticky thead tr:first-child th {
        top: 0;
    }

    .table-sticky thead tr:last-child th {
        top: 63px; /* Adjust this value based on the height of your first header row */
    }

    #listings-insights-table thead tr:last-child th {
        top: 43px !important;
    }
</style>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Insights</h5>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">

                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-all-insights-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-all-insights" type="button" role="tab"
                                        aria-controls="nav-all-insights" aria-selected="true">All Insights
                                </button>
                                <button class="nav-link" id="nav-listings-insights-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-listings-insights" type="button"
                                        role="tab"
                                        aria-controls="nav-listings-insights"
                                        aria-selected="false">Listings
                                </button>
                                <button class="nav-link" id="nav-leads-insights-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-leads-insights" type="button"
                                        role="tab"
                                        aria-controls="nav-leads-insights"
                                        aria-selected="false">Leads
                                </button>
                                <button class="nav-link" id="nav-responsiveness-insights-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-responsiveness-insights" type="button"
                                        role="tab"
                                        aria-controls="nav-responsiveness-insights"
                                        aria-selected="false">Responsiveness
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="nav-all-insights" role="tabpanel"
                         aria-labelledby="nav-all-insights-tab">
                        <div
                            style="max-height: 800px; overflow:scroll"
                            class="table-wrapper"
                        >
                            <table class="table table-bordered table-striped table-hover table-sticky"
                                   id="all-insights-table">
                                <thead>
                                <tr>
                                    <th rowspan="2">Agent</th>
                                    <th rowspan="2">Listings</th>
                                    <th colspan="3">% Quality Score</th>
                                    <th colspan="2">% Verified listings</th>
                                    <th rowspan="2">Avg Listing Duration</th>
                                    <th rowspan="2">Total leads</th>
                                    <th rowspan="2">WA leads</th>
                                    <th rowspan="2">Call leads</th>
                                    <th rowspan="2">Email leads</th>
                                    <th rowspan="2">WA Response Time</th>
                                    <th rowspan="2">% WA Response Rate</th>
                                    <th rowspan="2">Call Answer Rate</th>
                                </tr>
                                <tr>
                                    <th>PF</th>
                                    <th>Bayut</th>
                                    <th>Dubizzle</th>
                                    <th>PF</th>
                                    <th>Bayut</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($insights ?? [] as $insight)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img src="{{ $insight->user_thumbnail ?? $insight->thumb_url }}" alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold">{{ $insight->user_name }}</div>
                                                    {{ $insight->zone_name ? $insight->zone_name . " | " : "" }} {{ $insight->parent_name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $insight->listings_count }}</td>
                                        <td>{{ $insight->pf_score }}%</td>
                                        <td>{{ $insight->bayut_score }}%</td>
                                        <td>{{ $insight->dubizzle_score }}%</td>
                                        <td>{{ calculatePercentage($insight->pf_verified, $insight->listings_count) }}%</td>
                                        <td>
                                            Verified: {{ calculatePercentage($insight->bayut_verified, $insight->listings_count) }}%
                                            <br>
                                            Trucheck: {{ calculatePercentage($insight->bayut_truchecked, $insight->listings_count) }}%
                                            <br>
                                            Checked: {{ calculatePercentage($insight->bayut_checked, $insight->listings_count) }}%
                                        </td>
                                        <td>{{ $insight->average_listings_duration }} Day(s)</td>
                                        <td>{{ $insight->leads_count }}</td>
                                        <td>{{ $insight->wa_leads_count }}</td>
                                        <td>{{ $insight->call_leads_count }}</td>
                                        <td>{{ $insight->email_leads_count }}</td>
                                        <td>{{ $insight->wa_response_time ? $insight->wa_response_time . "Min": "NA" }}</td>
                                        <td>{{ $insight->wa_response_rate }}%</td>
                                        <td>{{ $insight->call_response_rate }}%</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-listings-insights"
                         role="tabpanel"
                         aria-labelledby="nav-listings-insights-tab">
                        <div
                            style="max-height: 800px; overflow:scroll"
                            class="table-wrapper"
                        >
                            <table class="table table-bordered table-striped table-hover table-sticky"
                                   id="listings-insights-table">
                                <thead>
                                <tr>
                                    <th rowspan="2">Agent</th>
                                    <th rowspan="2">Listings</th>
                                    <th colspan="3">% Quality Score</th>
                                    <th colspan="2">% Verified listings</th>
                                    <th rowspan="2">Avg Listing Duration</th>
                                </tr>
                                <tr>
                                    <th>PF</th>
                                    <th>Bayut</th>
                                    <th>Dubizzle</th>
                                    <th>PF</th>
                                    <th>Bayut</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($insights ?? [] as $insight)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img src="{{ $insight->user_thumbnail ?? $insight->thumb_url }}" alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold">{{ $insight->user_name }}</div>
                                                    {{ $insight->zone_name ? $insight->zone_name . " | " : "" }} {{ $insight->parent_name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $insight->listings_count }}</td>
                                        <td>{{ $insight->pf_score }}%</td>
                                        <td>{{ $insight->bayut_score }}%</td>
                                        <td>{{ $insight->dubizzle_score }}%</td>
                                        <td>{{ calculatePercentage($insight->pf_verified, $insight->listings_count) }}%</td>
                                        <td>
                                            Verified: {{ calculatePercentage($insight->bayut_verified, $insight->listings_count) }}%
                                            <br>
                                            Trucheck: {{ calculatePercentage($insight->bayut_truchecked, $insight->listings_count) }}%
                                            <br>
                                            Checked: {{ calculatePercentage($insight->bayut_checked, $insight->listings_count) }}%
                                        </td>
                                        <td>{{ $insight->average_listings_duration }} Day(s)</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-leads-insights"
                         role="tabpanel"
                         aria-labelledby="nav-leads-insights-tab">
                        <div style="max-height: 800px; overflow:scroll">
                            <table class="table table-bordered table-striped table-hover"
                                   id="leads-insights-table">
                                <thead>
                                <tr>
                                    <th>Agent</th>
                                    <th>Total leads</th>
                                    <th>WA leads</th>
                                    <th>Call leads</th>
                                    <th>Email leads</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($insights ?? [] as $insight)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img src="{{ $insight->user_thumbnail ?? $insight->thumb_url }}" alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold">{{ $insight->user_name }}</div>
                                                    {{ $insight->zone_name ? $insight->zone_name . " | " : "" }} {{ $insight->parent_name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $insight->leads_count }}</td>
                                        <td>{{ $insight->wa_leads_count }}</td>
                                        <td>{{ $insight->call_leads_count }}</td>
                                        <td>{{ $insight->email_leads_count }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="tab-pane fade" id="nav-responsiveness-insights" role="tabpanel"
                         aria-labelledby="nav-responsiveness-insights-tab">
                        <div style="max-height: 800px; overflow:scroll">
                            <table class="table table-bordered table-striped table-hover"
                                   id="responsiveness-insights-table">
                                <thead>
                                <tr>
                                    <th>Agent</th>
                                    <th>WA Response Time</th>
                                    <th>% WA Response Rate</th>
                                    <th>Call Answer Rate</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($insights ?? [] as $insight)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img src="{{ $insight->user_thumbnail ?? $insight->thumb_url }}" alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold">{{ $insight->user_name }}</div>
                                                    {{ $insight->zone_name ? $insight->zone_name . " | " : "" }} {{ $insight->parent_name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $insight->wa_response_time ? $insight->wa_response_time . "Min": "NA" }}</td>
                                        <td>{{ $insight->wa_response_rate }}%</td>
                                        <td>{{ $insight->call_response_rate }}%</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
