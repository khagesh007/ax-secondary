{{-- <div class="row">
    <div class="col-lg-4 col-md-4 mt-2">
        <label for="agent_id" class="form-label"> Agents*</label>
    </div>
    <div class="col-lg-8 col-md-8 mt-2">
        <select class="form-select select2 inputs" name="agent_id" id="agent_id" required>
            <option value=""></option>
            @foreach ($agents ?? [] as $agent)
                <option data-image="{{ $agent->thumbnail }}" data-status="{{ $agent->status }}" value="{{ $agent->id }}">
                    {{ $agent->first_name }}
                    {{ $agent->last_name }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback">
            Please choose an agent.
        </div>
        <div id="agent_container"></div>
    </div>

</div> --}}


<div class="row mt-3">
    <div class="col-md-12 ">

        <label for="agent_id"
            class="form-label required">Agent<span>*</span></label>
        <select name='agent_id' id="agent_id" class="form-control select2 inputs"
            required>
            <option></option>
            @foreach ($agents ?? [] as $agent)
                <option data-image="{{ $agent->thumbnail }}" data-status="{{ $agent->status }}" value="{{ $agent->id }}">
                    {{ $agent->first_name }}
                    {{ $agent->last_name }}</option>
            @endforeach
        </select>
        <div class="valid-feedback">
            Please Select any Agent.
        </div>
    </div>

</div>


@section('script-bottom')
<script>
    if ($("#agent_id").hasClass("select2")) {
        $('#agent_id').select2({
            placeholder: 'Select agent',
            allowClear: true,
            focusOnOpen: false,
            templateResult: formatAgent,
            templateSelection: formatAgent
        });
    }
    function formatAgent(e) {
        let img = $(e.element).data('image');
        let status = $(e.element).data('status');
        let onlineClass = "online"
        if (!status) {
            onlineClass = "away";
        }
        return e.id ? $('<div class="d-flex align-items-center"><div class="flex-shrink-0 chat-user-img ' +
            onlineClass + ' align-self-center me-2 ms-0"><div class="avatar-xxs mb-2"> <img src="' + img +
            '" class="rounded-circle img-fluid userprofile" alt=""><span class="user-status"></span> </div></div><div class="flex-grow-1 overflow-hidden"><p class="text-truncate mb-0">' +
            e.text + '</p></div></div>') : e.text;
    }

    function formatAgentView(e) {
        let img = $(e.element).data('image');
        let status = $(e.element).data('status');
        let onlineClass = "online"
        if (!status) {
            onlineClass = "away";
        }
        return (e.text ?? "No Name")
    }
</script>
@endsection

