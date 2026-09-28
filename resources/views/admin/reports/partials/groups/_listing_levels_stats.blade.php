<div class="row">


    <div class="col-md-12 text-center">
        <div class="card m-2 sticky-top-with-margin-bottom position-sticky">
            <div class="card-body">
                <b>Listing Quality</b>
            </div>
        </div>

        <div class="row row-cols-md-3 row-cols-1">
            @foreach ($groupdata as $count)


                <div class="col col-lg p-2">
                    <div class="card"
                        style="border:2px {{ request()->child ? 'dotted' : 'solid' }} {{ $count['group_color'] }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <a class=""
                                        href="{{ route('admin.reports.listingStatsOverviewBlade') }}?group_id={{ $count['group_id'] }}"
                                        target="_blank"><i class="mdi-open-in-new mdi"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="listingPortalLevels_{{ $count['group_id'] }}"
                                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                                    class="propertyStatus listingPortalLevels_{{ $count['group_id'] }}" dir="ltr"
                                    data-dataset =""></div>
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
