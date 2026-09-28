<div class="row mb-3">
    <div class="col-md-12">
        <div class="card row-eq-height">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Listing Verified</h5>
                    </div>
                    <div class="flex-grow-0 text-end justify-content-end">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            @foreach($groupData["data"] ?? [] as $portal)
                                <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="nav-listing-verified-statistics-{{$portal["unique_key"]}}-chart-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-listing-verified-statistics-{{$portal["unique_key"]}}" type="button"
                                        role="tab"
                                        aria-controls="nav-listing-verified-statistics-{{$portal["unique_key"]}}" aria-selected="true">
                                    {{ $portal["name"] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    @foreach($groupData["data"] ?? [] as $portal)
                        <div class="tab-pane fade show active" id="nav-listing-verified-statistics-{{$portal["unique_key"]}}"
                             role="tabpanel"
                             aria-labelledby="nav-listing-verified-statistics-{{$portal["unique_key"]}}-tab">
                            <div class="graph-container listing-verified-statistics-chart" id="listing-verified-statistics-{{$portal["unique_key"]}}-chart" data-dataset="{{ json_encode($portal["groups"]) }}"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
