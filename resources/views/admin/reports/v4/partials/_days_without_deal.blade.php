<div class="row mb-1">

    <div class="col-md-12">

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
                    <table class="table table-bordered table-striped table-hover" id="table-day-without-deal">
                        <thead>
                            <tr>
                                <th>Agent name</th>
                                <th>Group</th>
                                <th>Days without Deal</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupdata as $count)
                                @foreach ($count['agentNames'] as $key => $value)
                                    <tr>
                                        <td>{{ $value }}</td>
                                        <td>{{ $count['group_name'] }}</td>
                                        <td>{{ $count['daysCount'][$key] }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div><!-- end card -->
        </div>
    </div>
</div>
