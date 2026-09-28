@php
    $counter = 1;
@endphp
@foreach ($groupdata as $count)
    {{-- @dd($count) --}}
    @php
        $set = [];
        foreach ($count as $key => $value) {
            $set = $set + [$key => $value];
        }
        $set = json_encode($set);
    @endphp

    <div class="row mb-1">
        <div class="col-md-9 group_{{ $count['group_id'] }}_div hide-all {{ $counter == 1 ? '' : 'd-none' }}">
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
                        <canvas id="meeting-days-{{ $count['group_id'] }}"
                            class="meetingWiseGraph meeting-days-status-{{ $count['group_id'] }}"
                            data-dataset ="{{ $set }}"></canvas>
                    </div>
                </div><!-- end card -->
            </div>
        </div>
        <div class="col-md-3 group_{{ $count['group_id'] }}_div hide-all {{ $counter == 1 ? '' : 'd-none' }}">

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
                    @php
                        $total = 0;
                        $totalViews = 0;
                    @endphp
                    @foreach ($count['eventsPerDay'] as $key => $value)
                        @php
                            $total += $value;
                            $totalViews += $count['viewsPerDay'][$key];

                        @endphp
                    @endforeach

                    <div class="table-responsive" style="max-height: 350px; overflow:scroll">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Viewsings ({{ $totalViews }})</th>
                                    <th>Meetings ({{ $total }})</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($count['eventsPerDay'] as $key => $value)
                                    <tr>

                                        <td>{{ $key }}</td>
                                        <td>{{ $value }}</td>
                                        <td>{{  $count['viewsPerDay'][$key] }}</td>
                                    </tr>

                                @endforeach
                            </tbody>
                        </table>
                        {{ $total }}
                    </div>
                </div><!-- end card -->
            </div>
        </div>
    </div>

    @php
        $counter++;
    @endphp
@endforeach
