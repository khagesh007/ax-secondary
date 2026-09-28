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
                        <h5 class="card-title">Listings</h5>
                    </div>
                    <div class="flex-grow-1 text-end">
                        {{-- <a class="" href="{{ route('admin.reports.listingStatsOverviewBlade') }}?group_id={{ $count['group_id'] }}" target="_blank"><i class="mdi-open-in-new mdi"></i></a> --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="listings-monthly" class="listingWiseGraph listings-monthly"
                    data-dataset ="{{ $set }}"></canvas>
            </div><!-- end card -->


        </div>
    </div>

    <div class="col-md-4">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Listings</h5>
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
                                <th>Published</th>
                                <th>Pocket</th>
                                <th>Unpublished</th>
                                <th>Draft</th>

                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($groupdata as $key=> $count )
                            <tr>
                                <td>{{ $key }}</td>
                                <td style="white-space: nowrap">{{ $count['published'] }} ( {{ getPercenatge($count['total'],$count['published']) }}% )</td>
                                <td style="white-space: nowrap">{{ $count['pocket'] }} ( {{ getPercenatge($count['total'],$count['pocket']) }}% )</td>
                                <td style="white-space: nowrap">{{ $count['unpublished'] }} ( {{ getPercenatge($count['total'],$count['unpublished']) }}% )</td>
                                <td style="white-space: nowrap">{{ $count['draft'] }} ( {{ getPercenatge($count['total'],$count['draft']) }}% )</td>
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
