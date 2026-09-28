<div class="card">
    <div class="card-body form-steps">
        <div class="row">
            <div class="col-4 mt-2">Lead Status</div>
            <div class="col-8 mt-2">
                <select class="form-control select2 inputs" name="lead_status_id"
                    id="lead_status_id">
                    <option value="" selected disabled>Select lead status</option>
                    @foreach ($statuses ?? [] as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    Please choose lead status.
                </div>
            </div>
            <div class="col-4 mt-2">Lead Sub Status</div>
            <div class="col-8 mt-2">
                <select class="form-control select2 inputs" name="lead_sub_status_id"
                    id="lead_sub_status_id">
                    <option value="" selected disabled>Select lead sub status</option>
                </select>
                <div class="invalid-feedback">
                    Please choose lead sub status.
                </div>
            </div>
            <div class="col-4 mt-2">Priority</div>
            <div class="col-8 mt-2">
                <select class="form-control select2 inputs" name="priority" id="priority">
                    @foreach (\App\Models\Lead::PRIORITY_SELECT ?? [] as $priority)
                        <option value="{{ $priority }}"
                            {{ $priority == 'Normal' ? 'selected' : '' }}>{{ $priority }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    Please choose priority.
                </div>
            </div>

        </div>
    </div>
</div>