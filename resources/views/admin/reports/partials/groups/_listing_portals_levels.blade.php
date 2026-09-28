<div class="row">


    <div class="col-md-12 text-center">
        <div class="card m-2 sticky-top-with-margin-bottom position-sticky">
            <div class="card-body">
                <b>Listing Product Levels</b>
            </div>
        </div>
        <div class="row text-end">
            <div class="col">
                <button type="button" data-portal="PF" class="donutGraph btn btn-outline-danger btn-icon waves-effect waves-light"><img class="" src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644265820230215063058.png"></button>
                <button type="button" data-portal="Dubizzle" class="donutGraph btn btn-outline-danger btn-icon waves-effect waves-light"><img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644264020230215063040.png"></button>
                <button type="button" data-portal="Bayut" class="donutGraph btn btn-outline-success btn-icon waves-effect waves-light"><img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644259820230215062958.png"></button>

                {{-- <button id="" class="guageGraph" data-portal="PF">PF</button>
                <button id="" class="guageGraph" data-portal="Dubizzle">Dubizzle</button>
                <button id="" class="guageGraph" data-portal="Bayut">Bayut</button> --}}

            </div>

        </div>
        <div class="row row-cols-md-3 row-cols-1">

            @foreach ($groupdata as $count)
           @php
            $property_finder = $count['property_finder'];
            $dubizzle = $count['dubizzle'];
            $bayut = $count['bayut'];


            $property_finder_total = $property_finder['portal_count'];
            $standard_pf = $property_finder['level']['Standard'];
            $featured_pf = $property_finder['level']['Featured'];
            $premium_pf = $property_finder['level']['Premium'];

            $bayut_total = $bayut['portal_count'];
            $standard_bauyt = $bayut['level']['Standard'];
            $signature_bauyt = $bayut['level']['Signature'];
            $hot_bayut = $bayut['level']['Hot'];


            $dubizzle_total = $dubizzle['portal_count'];
            $standard_dubizzle = $dubizzle['level']['Standard'];
            $featured_dubizzle = $dubizzle['level']['Featured'];

           @endphp
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



                                <div id="listingPortalLevel_{{ $count['group_id'] }}"
                                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                                    class="propertyLevel listingPortalLevel_{{ $count['group_id'] }}" dir="ltr"
                                    data-property_finder_total="{{ $property_finder_total }}"
                                    data-standard_pf="{{ $standard_pf }}"
                                    data-featured_pf="{{ $featured_pf }}"
                                    data-premium_pf="{{ $premium_pf }}"

                                    data-bayut_total="{{ $bayut_total }}"
                                    data-standard_bauyt="{{ $standard_bauyt }}"
                                    data-signature_bauyt="{{ $signature_bauyt }}"
                                    data-hot_bayut="{{ $hot_bayut }}"

                                    data-dubizzle_total="{{ $dubizzle_total }}"
                                    data-standard_dubizzle="{{ $standard_dubizzle }}"
                                    data-featured_dubizzle="{{ $featured_dubizzle }}"

                                    data-group_id="{{ $count['group_id'] }}">
                                </div>
                                <hr>
                                <div id="showdata_{{ $count['group_id'] }}">

                                </div>


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
