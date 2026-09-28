<div class="row mb-3">
    <div class="col-md-12">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Deals</h5>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between">
                        <div style="width: 75%">
                            <select name='statuses[]' id="deal-statistics-status-select" class="form-control select2 inputs"
                                    multiple required>
                                @foreach ($groupData["statuses"] ?? [] as $status)
                                    <option value="{{ $status }}" selected>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist" style="width: auto">
                            <button class="nav-link active" id="nav-deal-statistics-chart-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-deal-statistics-chart" type="button" role="tab"
                                    aria-controls="nav-deal-statistics-chart" aria-selected="true">Chart
                            </button>
                            <button class="nav-link" id="nav-deal-statistics-percent-chart-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-deal-statistics-percent-chart" type="button" role="tab"
                                    aria-controls="nav-deal-statistics-percent-chart" aria-selected="false">Performance Chart
                            </button>
                            <button class="nav-link" id="nav-deal-statistics-table-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-deal-statistics-table" type="button" role="tab"
                                    aria-controls="nav-deal-statistics-table" aria-selected="false">Table
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="nav-deal-statistics-chart" role="tabpanel"
                         aria-labelledby="nav-deal-statistics-chart-tab">
                        <div class="graph-container" id="deal-statistics-chart" data-dataset="{{ json_encode($groupData) }}"></div>
                    </div>
                    <div class="tab-pane fade show active" id="nav-deal-statistics-percent-chart" role="tabpanel"
                         aria-labelledby="nav-deal-statistics-percent-chart-tab">
                        <div class="graph-container" id="deal-statistics-percent-chart" data-dataset="{{ json_encode($groupData) }}"></div>
                    </div>
                    <div class="tab-pane fade" id="nav-deal-statistics-table" role="tabpanel"
                         aria-labelledby="nav-deal-statistics-table-tab">
                        <div class="table-responsive" style="max-height: 350px; overflow:scroll">
                            <table class="table table-bordered table-striped table-hover" id="deal-statistics-table">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    @foreach($groupData["group_names"] ?? [] as $groupName)
                                        <th>{{ $groupName }}</th>
                                    @endforeach

                                </tr>
                                </thead>
                                <tbody>

                                @foreach($groupData["statuses"] ?? [] as $status)
                                    <tr id="deal-statistics-table-{{ preg_replace('/[^a-zA-Z0-9]/', '_', $status) }}">
                                        <td>{{ $status }}</td>
                                        @foreach($groupData["group_names"] ?? [] as $groupId)
                                            <td>{{ $groupData["data"][$groupId]["status_wise_counts"][str_replace(" ", "_", $status)] ?? 0 }}</td>
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
