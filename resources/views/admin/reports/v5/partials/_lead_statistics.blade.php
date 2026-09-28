<div class="row mb-3">
    <div class="col-md-12">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Leads - Status Wise</h5>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between">
                        <div style="width: 75%">
                            <select name='statuses[]' id="lead-statistics-status-select" class="form-control select2 inputs"
                                    multiple required>
                                @foreach ($groupData["statuses"] ?? [] as $status)
                                    <option value="{{ $status }}" selected>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist" style="width: auto">
                            <button class="nav-link active" id="nav-lead-statistics-chart-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-lead-statistics-chart" type="button" role="tab"
                                    aria-controls="nav-lead-statistics-chart" aria-selected="true">Chart
                            </button>
                            <button class="nav-link" id="nav-lead-statistics-percent-chart-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-lead-statistics-percent-chart" type="button" role="tab"
                                    aria-controls="nav-lead-statistics-percent-chart" aria-selected="false">Performance Chart
                            </button>
                            <button class="nav-link" id="nav-lead-statistics-table-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-lead-statistics-table" type="button" role="tab"
                                    aria-controls="nav-lead-statistics-table" aria-selected="false">Table
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="nav-lead-statistics-chart" role="tabpanel"
                         aria-labelledby="nav-lead-statistics-chart-tab">
                        <div class="graph-container" id="lead-statistics-chart"
                             data-dataset="{{ json_encode($groupData) }}"></div>
                    </div>
                    <div class="tab-pane fade show active" id="nav-lead-statistics-percent-chart" role="tabpanel"
                         aria-labelledby="nav-lead-statistics-percent-chart-tab">
                        <div class="graph-container" id="lead-statistics-percent-chart"
                             data-dataset="{{ json_encode($groupData) }}"></div>
                    </div>
                    <div class="tab-pane fade" id="nav-lead-statistics-table" role="tabpanel"
                         aria-labelledby="nav-lead-statistics-table-tab">
                        <div class="table-responsive" style="max-height: 350px; overflow:scroll">
                            <table class="table table-bordered table-striped table-hover" id="lead-statistics-table">
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
                                    <tr id="lead-statistics-table-{{ preg_replace('/[^a-zA-Z0-9]/', '_', $status) }}">
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
