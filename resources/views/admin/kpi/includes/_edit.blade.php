    <div class="row">
        <input type="hidden" name="id" value="{{ $kpi->id }}" id="kpi_id">
        <div class="col-6">
            <div class="mb-3">
                <label for="ForminputState" class="form-label">Manager*</label>
                <select id="edit_manager_id" name="manager_id" disabled class="form-select select2">
                    <option value="">Select Manager</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ $kpi->manager_id == $user->id ? 'selected' : '' }}>
                            {{ $user->full_name }}</option>
                    @endforeach
                </select>
            </div>
        </div><!--end col-->
        <div class="col-6">
            <div class="mb-3">
                <label for="ForminputState" class="form-label">Agent*</label>
                <select name="agent_id[]" class="form-control select2" disabled id="edit_agent_id">
                    <option value="">Select Agents</option>
                    <option value="{{ $kpi->agent->id }}" selected>{{ $kpi->agent->full_name }}</option>
                </select>
            </div>
        </div><!--end col-->
        {{-- <div class="col-6">
            <div class="mb-3">
                <label for="ForminputState" class="form-label">Module</label>
                <select name="model" class="form-control select2" id="edit_model">
                    <option value="Lead">Leads</option>
                    <option value="Property">Properties</option>
                    <option value="ListingLead">Listing Leads</option>
                    <option value="Event">Events</option>
                </select>
            </div>
        </div><!--end col--> --}}
        <div class="col-12">
            <div class="mb-3">
                <label for="ForminputState" class="form-label">Key Performance Indicator (KPI)*</label>
                <select name="kpi" class="form-control select2 search_input" id="edit_kpi" disabled>
                    @include('admin.kpi.includes._kpi_options',['selected' => $kpi->kpi])

                </select>
            </div>
        </div><!--end col-->
        <div class="col-6">
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date*</label>
                <input type="date" name="start_date" id="start_date search_input" required class="form-control"
                    value="{{ $kpi->start_date }}">
            </div>
        </div><!--end col-->
        <div class="col-6">
            <div class="mb-3">
                <label for="end_date" class="form-label">End Date*</label>
                <input type="date" name="end_date" id="end_date" required class="form-control search_input" value="{{ $kpi->end_date }}">
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <label for="ForminputState" class="form-label">Target*</label>
                <input type="text" name="target" id="edit_target" required class="form-control search_input" value="{{ $kpi->target }}">
            </div>
        </div><!--end col-->
        <div class="col-lg-12">
            <div class="text-end">
                {{-- <button type="submit" class="btn btn-primary">Submit</button> --}}
            </div>
        </div><!--end col-->
    </div><!--end row-->
