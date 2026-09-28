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
                            <h5 class="card-title">Days without Deals</h5>
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
                        <canvas id="deal-days-{{ $count['group_id'] }}"
                            class="dealWiseGraph deal-days-status-{{ $count['group_id'] }}"
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
                            <h5 class="card-title">Days without Deals</h5>
                        </div>
                        <div class="flex-grow-1 text-end">
                            {{-- <a class=""
                                href="{{ route('admin.reports.leadsStatsOverviewBlade') }}?group_id={{ $count['group_id'] }}"
                                target="_blank"><i class="mdi-open-in-new mdi"></i></a> --}}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 350px; overflow:scroll">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Agent name</th>
                                    <th>Days without Deal</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($count['agentNames'] as $key => $value)
                                    <tr>
                                        <td>{{ $value }}</td>
                                        <td>{{ $count['daysCount'][$key] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div><!-- end card -->
            </div>
        </div>
    </div>

    @php
        $counter++;
    @endphp
@endforeach
