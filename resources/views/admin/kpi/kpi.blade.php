<h6 class="fs-15">Agent Name: {{ $user->full_name }}</h6>
@foreach ($kpis as $kpi)
    <div class="row mb-2 pt-2">
        <div class="col-md-3">
             <b>Model:</b> <span>{{ $kpi->model }}</span>
        </div>
        <div class="col-md-3">
            <b>Key:</b> <span>New Listings</span>
        </div>
        <div class="col-md-3">
            <b>Target:</b> <span>{{ $kpi->target }}</span>
        </div>

        <div class="col-md-3">
            <b>Date:</b> <span>{{ $kpi->start_date }} - {{ $kpi->end_date }}</span>
        </div>

        <div class="col-md-3">
            <button type="button" class="btn btn-outline-primary btn-label waves-effect waves-light get-kpi-statistics" data-id="{{ $kpi->id }}"><i class="ri-bar-chart-line abel-icon align-middle fs-16 me-2"></i> View Statistics</button>
        </div>
    </div>
@endforeach
