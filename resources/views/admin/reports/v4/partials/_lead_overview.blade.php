@php
    $counter = 1;
    $count = $groupdata;

    $set = [];

    foreach ($count as $key => $value) {
        $set = $set + [$key => $value];
    }
    $set = json_encode($set);
@endphp
{{-- @dd($groupdata) --}}
<div class="row mb-1">
    <div class="col-md-8">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Leads</h5>
                    </div>
                    <div class="flex-grow-1 text-end">
                        {{-- <a class="" href="{{ route('admin.reports.listingStatsOverviewBlade') }}?group_id={{ $count['group_id'] }}" target="_blank"><i class="mdi-open-in-new mdi"></i></a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="leads-monthly" class="leadOverviewWiseGraph leads-monthly"
                    data-dataset ="{{ $set }}"></canvas>
            </div><!-- end card -->


        </div>
    </div>

    <div class="col-md-4">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Leads</h5>
                    </div>
                    <div class="flex-grow-1 text-end">
                        {{-- <a class="" href="{{ route('admin.reports.listingStatsOverviewBlade') }}?group_id={{ $count['group_id'] }}" target="_blank"><i class="mdi-open-in-new mdi"></i></a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 300px; overflow:scroll">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>New</th>
                                <th>Qualified</th>
                                <th>Contacted</th>
                                <th>Successful</th>

                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($groupdata as $key=> $count )
                            <tr>
                                <td>{{ $key }}</td>
                                <td style="white-space: nowrap">{{ $count['New'] }} ( {{ getPercenatge($count['total'],$count['New']) }}% )</td>
                                <td style="white-space: nowrap">{{ $count['Qualified'] }} ( {{ getPercenatge($count['total'],$count['Qualified']) }}% )</td>
                                <td style="white-space: nowrap">{{ $count['Contacted'] }} ( {{ getPercenatge($count['total'],$count['Contacted']) }}% )</td>
                                <td style="white-space: nowrap">{{ $count['Successful'] }} ( {{ getPercenatge($count['total'],$count['Successful']) }}% )</td>
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
