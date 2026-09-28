{{-- @foreach ($users as $user)
    <div class="row mb-2">
        <div class="col-md-3">
            <input type="hidden" name="agent_id[]" value="{{ $user->id }}">
            <input type="text"  class="form-control" value="{{ $user->full_name }}" readonly>
        </div>



        <div class="col-md-4">
            <input type="number" name="target[]" class="form-control" id="target" placeholder="Target Value">
        </div>
        <div class="col-md-2">
           @if ($user->kpis->count() > 0)
           <button type="button" class="btn btn-outline-primary btn-label waves-effect waves-light get-kpi" data-id="{{ $user->id }}"><i
            class="ri-bar-chart-line abel-icon align-middle fs-16 me-2"></i> View KPIs</button>
           @endif

        </div>
    </div>
@endforeach --}}

@foreach ($users as $user)
   <option value="{{ $user->id }}">{{ $user->full_name }}</option>
@endforeach
