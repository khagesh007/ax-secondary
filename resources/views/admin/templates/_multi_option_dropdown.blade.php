<style>
    .scrollable-div {
        max-height: 229px;
        padding: 10px;
        resize: both;
        overflow: auto;
        /* width: 100%; */
    }
</style>

<div class="row">
    <div class="col-4"><label for="{{$id}}" class="form-label"> {{$label}}*</label>
    </div>


    <div class="col-8 scrollable-div">
        <select name='{{$id}}' id="{{$id}}" class="form-control search-inputs searchInput">
            <option value=""></option>
            @foreach ($all_agents ?? [] as $agent)
                <option data-image="{{ $agent->thumbnail }}" data-status="{{ $agent->status }}"
                    value="{{ $agent->id }}">
                    {{ $agent->first_name . ' ' . $agent->last_name }}</option>
            @endforeach
        </select>
        <div class="mt-1">
            <div class="row">
                <div class="col-md-6">
                    {{-- <div class="form-check form-switch form-check-inline" dir="ltr">
                        <small for="showInactiveAgents_v">Inactive </small> <input type="checkbox" value="inactive"
                            class="form-check-input" id="showInactiveAgents_v" name="showInactiveAgents_v">
                    </div> --}}
                </div>
                @if (auth()->user()->hasRole(['Super Admin', 'Line Manager', 'Team Leader', 'Sales Director', 'Head of Secondary','CEO']))
                    <div class="col-md-6">
                        <div class="form-check form-switch form-check-inline" dir="ltr">
                            <small for="showMyTeamAgents_v">Team </small> <input type="checkbox" value="myteam"
                                class="form-check-input" id="showMyTeamAgents_v" name="showMyTeamAgents_v">

                        </div>
                    </div>
                @endif

            </div>



        </div>

        <div class="mt-1">

        </div>
    </div>
</div>




@push('agent_filter')
    <script>
        $(document).on('change', '#showInactiveAgents_v', function(e) {
            getAgents();
        });
        $(document).on('change', '#showMyTeamAgents_v', function(e) {
            getAgents();
        });
        getAgents();



        function getAgents() {
            if ($('#showInactiveAgents_v').is(':checked')) {
                all_agents = 'true';
            } else {
                all_agents = 'false';
            }

            if ($('#showMyTeamAgents_v').is(':checked')) {
                my_team = 'true';
            } else {
                my_team = 'false';
            }
            $.ajax({
                url: "{{ route('ajax.getAgentsBasedOnStatus') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    all_agents: all_agents,
                    my_team: my_team,
                },
                success: function(result) {
                    $("#{{$id}}").html('');

                    // make option and append to select #{{$id}}
                    let options = "";
                    option = ' <option value=""></option>'
                    result?.data.map(item => {
                        // options +=  `<option value="${item.id}">${item.full_name}</option>`;

                        options +=
                            `<option data-image="${item.thumbnail??item.thumb_url}" data-status="${item.status}" value="${item.id}">${item.full_name}</option>`;
                    });
                    $("#{{$id}}").html(options);

                    $('#{{$id}}').select2({
                        width: '100%',
                        placeholder: 'Select Agent',
                        allowClear: true,
                        templateResult: formatAgent,
                        templateSelection: formatAgentView,
                        dropdownParent: $('#generalModal')
                    });
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log()
                },
            });
            $(document).on('change', '#selectall', function() {
                if ($(this).is(':checked')) {

                    var agents = $('#{{$id}} option').map(function() {
                        return $(this).val();
                    }).get();

                    $('#{{$id}}').val(agents).trigger('change');

                } else {
                    $('#{{$id}}').val(null).trigger('change');
                }
            });
        }
    </script>
@endpush
