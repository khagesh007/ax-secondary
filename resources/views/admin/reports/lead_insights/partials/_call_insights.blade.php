<h4>Call Insights</h4>
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card row-eq-height">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Total Calls</h5>
                        <h1>{{ formatNumberMetricPrefixes($insightData["overview"]->total ?? 0) }}</h1>
                    </div>
                    <div>
                        <h5>Response Rate</h5>
                        <h1>{{ $insightData["overview"]->response_rate ?? 0 }}%</h1>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="graph-container" id="call-insights-call-overview-chart"
                         data-dataset="{{ json_encode($insightData["overview"]) }}"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">WhatsApp leads over time</h5>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div style="width: 60%">
                        <select name='statuses[]' id="call-insights-call-date-wise-select" class="form-control select2 inputs"
                                multiple>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source["value"] }}">{{ $source["name"] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist" style="width: auto">
                        <button class="nav-link active" id="nav-call-insights-call-count-date-wise-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-call-insights-call-count-date-wise" type="button" role="tab"
                                aria-controls="nav-call-insights-call-count-date-wise" aria-selected="true">All Calls
                        </button>
                        <button class="nav-link" id="nav-call-insights-call-response-rate-date-wise-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-call-insights-call-response-rate-date-wise" type="button" role="tab"
                                aria-controls="nav-call-insights-call-response-rate-date-wise" aria-selected="false">Response Rate
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="nav-call-insights-call-count-date-wise" role="tabpanel"
                         aria-labelledby="nav-call-insights-call-count-date-wise-tab">
                        <div class="graph-container" id="call-insights-call-count-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["date_wise_counts"]) }}"></div>
                    </div>
                    <div class="tab-pane fade show active" id="nav-call-insights-call-response-rate-date-wise" role="tabpanel"
                         aria-labelledby="nav-call-insights-call-response-rate-date-wise-tab">
                        <div class="graph-container" id="call-call-insights-call-response-rate-date-wise-chart"
                             data-dataset="{{ json_encode($insightData["date_wise_response_rates"]) }}"></div>
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
                        <h5 class="card-title">Calls per day of the week</h5>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div style="width: 60%">
                        <select name='statuses[]' id="call-insights-call-week-day-wise-select" class="form-control select2 inputs"
                                multiple>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source["value"] }}">{{ $source["name"] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="graph-container" id="call-insights-call-count-week-day-wise-chart"
                     data-dataset="{{ json_encode($insightData["week_day_wise_counts"]) }}"></div>
            </div>
        </div>
    </div>
    <div class="col-md-8 mt-2">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Calls per hour</h5>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div style="width: 60%">
                        <select name='statuses[]' id="call-insights-call-hour-wise-select" class="form-control select2 inputs"
                                multiple>
                            @foreach ($sources ?? [] as $source)
                                <option value="{{ $source["value"] }}">{{ $source["name"] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="graph-container" id="call-insights-call-count-hour-wise-chart"
                     data-dataset="{{ json_encode($insightData["hour_wise_counts"]) }}"></div>
            </div>
        </div>
    </div>
</div>
