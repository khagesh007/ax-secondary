<div class="row mt-2">
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    @php
                        $counter = 1;
                    @endphp
                    @foreach ($groupdata as $group)
                        <li class="nav-item">
                            <a class="nav-link {{ $counter == 1 ? 'active' : '' }} select-tab" href="javascript:void(0)"
                                data-group="group_{{ $group['group_id'] }}_div">
                                {{ $group['group_name'] }}
                            </a>
                        </li>
                        @php
                            $counter++;
                        @endphp
                    @endforeach

                </ul>
                <!-- Tab panes -->

            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!--end col-->
</div>


@php
    $counter = 1;
@endphp
@foreach ($groupdata as $count)
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
                            <h5 class="card-title">{{ $count['group_name'] }} : Locations</h5>
                        </div>
                        <div class="flex-grow-1 text-end">
                            <a class=""
                                href="{{ route('admin.reports.listingStatsOverviewBlade') }}?group_id={{ $count['group_id'] }}"
                                target="_blank"><i class="mdi-open-in-new mdi"></i></a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="location-properties-{{ $count['group_id'] }}"
                        class="locationWiseGraph location-properties-{{ $count['group_id'] }}"
                        data-dataset ="{{ $set }}"></canvas>
                </div><!-- end card -->
            </div>
        </div>
        <div class="col-md-3 group_{{ $count['group_id'] }}_div hide-all {{ $counter == 1 ? '' : 'd-none' }}">
            <div class="card row-eq-height">
                <div class="card-header">
                    <div class="d-flex">
                        <div class="flex-grow-0">
                            <h5 class="card-title">Locations</h5>
                        </div>
                        <div class="flex-grow-1 text-end">
                            <a class=""
                                href="{{ route('admin.reports.listingStatsOverviewBlade') }}?group_id={{ $count['group_id'] }}"
                                target="_blank"><i class="mdi-open-in-new mdi"></i></a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 300px; overflow:scroll">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Locations</th>
                                    <th>Listings</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($count['locationNames'] as $key => $value)
                                    <tr>
                                        <td>{{ $value }}</td>
                                        <td>{{ $count['propertyCounts'][$key] }}</td>
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
