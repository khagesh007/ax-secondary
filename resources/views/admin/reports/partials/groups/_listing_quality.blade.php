<div class="row">


    <div class="col-md-12 text-center">
        <div class="card m-2 sticky-top-with-margin-bottom position-sticky">
            <div class="card-body">
                <b>Listing Quality</b>
            </div>
        </div>
        <div class="row text-end">
            <div class="col">
                <button type="button" data-portal="PF" class="guageGraph btn btn-outline-danger btn-icon waves-effect waves-light"><img class="" src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644265820230215063058.png"></button>
                <button type="button" data-portal="Dubizzle" class="guageGraph btn btn-outline-danger btn-icon waves-effect waves-light"><img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644264020230215063040.png"></button>
                <button type="button" data-portal="Bayut" class="guageGraph btn btn-outline-success btn-icon waves-effect waves-light"><img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644259820230215062958.png"></button>

                {{-- <button id="" class="guageGraph" data-portal="PF">PF</button>
                <button id="" class="guageGraph" data-portal="Dubizzle">Dubizzle</button>
                <button id="" class="guageGraph" data-portal="Bayut">Bayut</button> --}}

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



                                <div id="listingPortalQuality_{{ $count['group_id'] }}"
                                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                                    class="propertyQuality listingPortalQuality_{{ $count['group_id'] }}" dir="ltr"
                                    data-group_id="{{ $count['group_id'] }}"
                                    data-pf_avg_rank="{{ $count['pf_avg_rank'] }}"
                                    data-bayut_avg_rank="{{ $count['bayut_avg_rank'] }}"
                                    data-dubizzle_avg_rank="{{ $count['dubizzle_avg_rank'] }}">
                                </div>
                                <hr>
                                <span><b>Property Finder:</b> {{ $count['pf_avg_rank'] }}% </span><br>
                                <span><b>Bayut:</b> {{ $count['bayut_avg_rank'] }}% </span><br>
                                <span><b>Dubizzle:</b> {{ $count['dubizzle_avg_rank'] }}% </span><br>

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
