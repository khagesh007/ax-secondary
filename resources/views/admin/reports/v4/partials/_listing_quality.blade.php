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
    <div class="mb-1 col-md-6 group_{{ $count['group_id'] }}_div hide-all {{ $counter == 1 ? '' : 'd-none' }}">

        <div class="card  row-eq-height">
            <div class="card-header">
                <div class="d-flex">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Listings</h5>
                    </div>
                    <div class="flex-grow-1 text-center">
                        <button type="button" data-portal="PF" class="guageGraph btn btn-outline-danger btn-icon waves-effect waves-light"><img class="" src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644265820230215063058.png"></button>
                        <button type="button" data-portal="Dubizzle" class="guageGraph btn btn-outline-danger btn-icon waves-effect waves-light"><img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644264020230215063040.png"></button>
                        <button type="button" data-portal="Bayut" class="guageGraph btn btn-outline-success btn-icon waves-effect waves-light"><img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644259820230215062958.png"></button>

                    </div>
                    <div class="flex-grow-2 text-end">
                        <a class="" href="{{ route('admin.reports.listingStatsOverviewBlade') }}?group_id={{ $count['group_id'] }}" target="_blank"><i class="mdi-open-in-new mdi"></i></a>
                    </div>
                </div>
            </div>


            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link graph-nav active" data-bs-toggle="tab"
                            href="#listings-quality-graph-{{ $count['group_id'] }}" role="tab" aria-selected="false">
                            Graphic Design
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link table-nav" data-bs-toggle="tab"
                            href="#listings-quality-table-{{ $count['group_id'] }}" role="tab"
                            aria-selected="false">
                            Tabular Design
                        </a>
                    </li>

                </ul>
                <!-- Tab panes -->
                <div class="tab-content text-muted">
                    <div class="tab-pane graph-nav-div active" id="listings-quality-graph-{{ $count['group_id'] }}"
                        role="tabpanel"  style="max-height: 300px; overflow:scroll">

                        <div id="listingPortalQuality_{{ $count['group_id'] }}"
                                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                                    class="propertyQuality listingPortalQuality_{{ $count['group_id'] }}" dir="ltr"
                                    data-group_id="{{ $count['group_id'] }}"
                                    data-pf_avg_rank="{{ $count['pf_avg_rank'] }}"
                                    data-bayut_avg_rank="{{ $count['bayut_avg_rank'] }}"
                                    data-dubizzle_avg_rank="{{ $count['dubizzle_avg_rank'] }}">
                                </div>
                                {{-- <hr> --}}
                                {{-- <span><b>Property Finder:</b> {{ $count['pf_avg_rank'] }}% </span><br>
                                <span><b>Bayut:</b> {{ $count['bayut_avg_rank'] }}% </span><br>
                                <span><b>Dubizzle:</b> {{ $count['dubizzle_avg_rank'] }}% </span><br> --}}

                    </div>
                    <div class="tab-pane table-nav-div" id="listings-quality-table-{{ $count['group_id'] }}"
                        role="tabpanel">
                        <div class="table-responsive" style="max-height: 300px; overflow:scroll">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Portal Quality</th>
                                        <th>Score</th>
                                    </tr>
                                </thead>
                                <tbody>


                                     <tr>
                                        <td>Property Finder</td>
                                        <td>{{ $count['pf_avg_rank'] }}%</td>
                                    </tr>
                                    <tr>
                                        <td>Bayut</td>
                                        <td>{{ $count['bayut_avg_rank'] }}%</td>
                                    </tr>
                                    <tr>
                                        <td>Dubizzle</td>
                                        <td>{{ $count['dubizzle_avg_rank'] }}%</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->


        </div>
    </div>
    @php
        $counter++;
    @endphp
@endforeach










