<div class="row mb-3">
    <div class="col-md-12">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Events</h5>
                    </div>
                </div>
                <div style="width: 100%">
                    <div class="d-flex justify-content-between">
                        <div style="width: 70%">
                            <select name='statuses[]' id="event-statistics-status-select"
                                    class="form-control select2 inputs"
                                    multiple>
                                @foreach ($groupData["statuses"] ?? [] as $status)
                                    <option value="{{ $status }}" selected>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-event-statistics-chart-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-event-statistics-chart" type="button" role="tab"
                                    aria-controls="nav-event-statistics-chart" aria-selected="true">Chart
                            </button>
                            <button class="nav-link" id="nav-event-statistics-percent-chart-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-event-statistics-percent-chart" type="button" role="tab"
                                    aria-controls="nav-event-statistics-percent-chart" aria-selected="false">Performance
                                Chart
                            </button>
                            <button class="nav-link" id="nav-event-statistics-table-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-event-statistics-table" type="button" role="tab"
                                    aria-controls="nav-event-statistics-table" aria-selected="false">Table
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="nav-event-statistics-chart" role="tabpanel"
                         aria-labelledby="nav-event-statistics-chart-tab">
                        <div class="graph-container" id="event-statistics-chart"
                             data-dataset="{{ json_encode($groupData) }}"></div>
                    </div>
                    <div class="tab-pane fade show active" id="nav-event-statistics-percent-chart" role="tabpanel"
                         aria-labelledby="nav-event-statistics-percent-chart-tab">
                        <div class="graph-container" id="event-statistics-percent-chart"
                             data-dataset="{{ json_encode($groupData) }}"></div>
                    </div>
                    <div class="tab-pane fade" id="nav-event-statistics-table" role="tabpanel"
                         aria-labelledby="nav-event-statistics-table-tab">
                        <div class="table-responsive" style="max-height: 350px; overflow:scroll">
                            <table class="table table-bordered table-striped table-hover" id="event-statistics-table">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    @foreach($groupData["group_names"] ?? [] as $groupName)
                                        <th>{{ $groupName }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($groupData["statuses"] ?? [] as $index => $status)
                                    <tr id="event-statistics-table-{{ preg_replace('/[^a-zA-Z0-9]/', '_', $status) }}">
                                        <td>{{ $status }}</td>
                                        @foreach($groupData["group_names"] ?? [] as $groupId)
                                            @php
                                                $statusSlug = str_replace(" ", "_", $groupData["data"][$groupId]["status_ids"][$index] . " ".  $status);
                                            @endphp
                                            <td>{{ $groupData["data"][$groupId]["status_wise_counts"][$statusSlug] ?? 0 }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>All</td>
                                    @foreach($groupData["group_names"] ?? [] as $groupId)
                                        <td>{{ $groupData["data"][$groupId]["total"] }}</td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
