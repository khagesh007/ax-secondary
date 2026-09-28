<div class="row mb-3">
    <div class="col-md-12">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Number of Days Without Deals</h5>
                    </div>
                    <div class="flex-grow-0 text-end justify-content-end">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-days-without-deals-overall-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-days-without-deals-overall" type="button" role="tab"
                                    aria-controls="nav-days-without-deals-overall" aria-selected="true">Overall Chart
                            </button>
                            <button class="nav-link" id="nav-days-without-deals-group-charts-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-days-without-deals-group-charts" type="button" role="tab"
                                    aria-controls="nav-days-without-deals-group-charts" aria-selected="true">Group Chart
                            </button>
                            <button class="nav-link" id="nav-days-without-deals-table-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-days-without-deals-table" type="button" role="tab"
                                    aria-controls="nav-days-without-deals-table" aria-selected="false">Table
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="nav-days-without-deals-overall" role="tabpanel"
                         aria-labelledby="nav-days-without-deals-overall-tab">
                        <div class="graph-container" id="days-without-deals-overall-chart"
                             data-dataset="{{ json_encode($groupData) }}"></div>
                    </div>
                    <div class="tab-pane fade show active" id="nav-days-without-deals-group-charts" role="tabpanel"
                         aria-labelledby="nav-days-without-deals-group-charts-tab">
                        <div id="days-without-deals-group-charts"
                             data-dataset="{{ json_encode($groupData) }}">

                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-days-without-deals-table" role="tabpanel"
                         aria-labelledby="nav-days-without-deals-table-tab">
                        <div class="table-responsive" style="max-height: 350px; overflow:scroll">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    @foreach($groupData["data"] ?? [] as $index => $group)
                                        @php
                                            $userCount = count($group["users"])
                                        @endphp
                                        @if($userCount)
                                            <th colspan="{{ $userCount }}">{{ $group["name"] }}</th>
                                        @endif

                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    @foreach($groupData["data"] ?? [] as $group)
                                        @foreach($group["users"] ?? [] as $userName)
                                            <td>{{ $userName }}</td>
                                        @endforeach
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($groupData["data"] ?? [] as $group)
                                        @foreach($group["counts"] ?? [] as $count)
                                            <td>{{ $count }}</td>
                                        @endforeach
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
