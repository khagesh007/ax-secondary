<div class="row">
    <div class="col-md-12 scroll-container text-center">
        <div class="card m-2 sticky-top-with-margin-bottom position-sticky">
            <div class="card-body">
                <b>Leads Status Overview</b>
            </div>
        </div>

         <div class="row row-cols-md-3 row-cols-1">
            @foreach ($groupdata as $count)
                @php
                $set = [];
                foreach ($count as $key => $value) {
                    $set = $set + [$key => $value];
                }
                $set = json_encode($set);
                @endphp
                {{-- @dd($locations) --}}
                <div class="col col-lg p-2" style="min-width: 400px; overflow-x: auto;">
                    <div class="card" style="border:2px {{ request()->child ? 'dotted' : 'solid' }} {{ $count['group_color'] }}" >
                        <div class="card-body" style="">
                            <div class="row">
                                <div class="col-md-6 text-start"><span class="group-name">{{ $count['group_name'] }}</span></div>
                                <div class="col-md-6 text-end">
                                    <a class="" href="{{route('admin.reports.leadsStatsOverviewBlade')}}?group_id={{$count['group_id']}}" target="_blank"><i class="mdi-open-in-new mdi"></i></a>
                                </div>
                            </div>
                                <div class="chart-container">
                                    <div id="leadsWise_{{ $count['group_id'] }}"
                                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                                    class="chart leadWiseStatus leadsWise_{{ $count['group_id'] }}" dir="ltr"
                                    data-dataset ="{{$set}}"></div>
                                </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>



@push('in_page_scripts')
    <script></script>
@endpush
