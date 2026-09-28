@php
    $counter = 1;
    $count = $groupdata;
@endphp

@php
    $set = [];

    foreach ($count as $key => $value) {
        $set = $set + [$key => $value];
    }
    $set = json_encode($set);
@endphp


<div class="row mb-1">
    <div class="col-md-9">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Meetings Statistics</h5>
                    </div>
                    <div class="flex-grow-1 text-end">
                        {{-- <a class=""
                                href="{{ route('admin.reports.leadsStatsOverviewBlade') }}?group_id={{ $count['group_id'] }}"
                                target="_blank"><i class="mdi-open-in-new mdi"></i></a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="graph-container">
                    <canvas id="meeting-days" class="meetingWiseGraph meeting-days-status"
                        data-dataset ="{{ $set }}"></canvas>
                </div>
            </div><!-- end card -->
        </div>
    </div>
    <div class="col-md-3 group_div">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Meetings Statistics</h5>
                    </div>
                    <div class="flex-grow-1 text-end">
                        {{-- <a class=""
                                href="{{ route('admin.reports.leadsStatsOverviewBlade') }}?group_id"
                                target="_blank"><i class="mdi-open-in-new mdi"></i></a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                @php
                    $total = 0;
                    $totalViews = 0;
                @endphp
                @foreach ($count['meetings'] as $key => $value)
                    @php
                        $total += $value;
                        // $totalViews += $count['viewsPerDay'][$key];
                    @endphp
                @endforeach

                <div class="table-responsive" style="max-height: 350px; overflow:scroll">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                {{-- <th>Viewsings ({{ $totalViews }})</th> --}}
                                <th>Meetings</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($count['meetings'] as $key => $value)
                                <tr>

                                    <td>{{ $key }}</td>
                                    <td>{{ $value }}</td>
                                    {{-- <td>{{  $count['viewsPerDay'][$key] }}</td> --}}
                                </tr>
                            @endforeach
                            <tr>

                                <td><b>Total</b></td>
                                <td><b>{{ $total }}</b></td>
                                {{-- <td>{{  $count['viewsPerDay'][$key] }}</td> --}}
                            </tr>
                        </tbody>
                    </table>
                    {{-- {{ $total }} --}}
                </div>
            </div><!-- end card -->
        </div>
    </div>
</div>

<div class="row mb-1">
    <div class="col-md-9">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Viewings Statistics</h5>
                    </div>
                    <div class="flex-grow-1 text-end">
                        {{-- <a class=""
                                href="{{ route('admin.reports.leadsStatsOverviewBlade') }}?group_id={{ $count['group_id'] }}"
                                target="_blank"><i class="mdi-open-in-new mdi"></i></a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="graph-container">
                    <canvas id="viewings-days" class="viewingsWiseGraph viewings-days-status"
                        data-dataset ="{{ $set }}"></canvas>
                </div>
            </div><!-- end card -->
        </div>
    </div>
    <div class="col-md-3 group_div">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Viewings Statistics</h5>
                    </div>
                    <div class="flex-grow-1 text-end">
                        {{-- <a class=""
                                href="{{ route('admin.reports.leadsStatsOverviewBlade') }}?group_id"
                                target="_blank"><i class="mdi-open-in-new mdi"></i></a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                @php
                    $total = 0;
                    $totalViews = 0;
                @endphp
                @foreach ($count['viewings'] as $key => $value)
                    @php
                        $total += $value;
                        // $totalViews += $count['viewsPerDay'][$key];
                    @endphp
                @endforeach

                <div class="table-responsive" style="max-height: 350px; overflow:scroll">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                {{-- <th>Viewsings ({{ $totalViews }})</th> --}}
                                <th>Viewings</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($count['viewings'] as $key => $value)
                                <tr>

                                    <td>{{ $key }}</td>
                                    <td>{{ $value }}</td>
                                    {{-- <td>{{  $count['viewsPerDay'][$key] }}</td> --}}
                                </tr>
                            @endforeach
                            <tr>

                                <td><b>Total</b></td>
                                <td><b>{{ $total }}</b></td>
                                {{-- <td>{{  $count['viewsPerDay'][$key] }}</td> --}}
                            </tr>
                        </tbody>
                    </table>
                    {{-- {{ $total }} --}}
                </div>
            </div><!-- end card -->
        </div>
    </div>
</div>
@php
    $counter++;
@endphp
