<div class="card-body text-center">
    <div class="d-flex mb-2 text-center">
        <div class="flex-grow-1">
            <lord-icon src="{{ asset('assets/json/icons/common-event.json') }}" trigger="loop"
                colors="primary:#405189,secondary:#0ab39c" style="width:125px;height:125px"> </lord-icon>
        </div>
    </div>

    <button class="mb-2 text-center btn btn-soft-primary btn-sm btn-sm-primary text-primary" onclick="getActivityLog($('#id').val())"
        type="button">
            Refresh Data</button>
</div>
