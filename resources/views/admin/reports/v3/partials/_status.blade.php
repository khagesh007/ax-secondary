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
    <div class="col-md-12 group_{{ $count['group_id'] }}_div hide-all {{ $counter == 1 ? '' : 'd-none' }}">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Status</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="listing-comparison-{{ $count['group_id'] }}"
                            class="listingComparisonWiseGraph listing-comparison-{{ $count['group_id'] }}"
                            data-dataset ="{{ $set }}"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="flex-grow-0">
                                <h4 class="card-title">Property Score</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div id="listingPortalQuality_1" class="propertyQuality listingPortalQuality_1"
                                    dir="ltr" data-group_id="1" data-pf_avg_rank="87" data-bayut_avg_rank="23"
                                    data-dubizzle_avg_rank="34">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div id="listingPortalQuality_2" class="propertyQuality listingPortalQuality_2"
                                    dir="ltr" data-group_id="1" data-pf_avg_rank="87" data-bayut_avg_rank="23"
                                    data-dubizzle_avg_rank="34">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div id="listingPortalQuality_3" class="propertyQuality listingPortalQuality_3"
                                    dir="ltr" data-group_id="1" data-pf_avg_rank="87" data-bayut_avg_rank="23"
                                    data-dubizzle_avg_rank="34">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $counter++;
    @endphp
@endforeach
