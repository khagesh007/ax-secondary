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
    <div class="col-4"><label for="search_unit_no" class="form-label"> Agents</label>
        <select name='search_agents_operator' id="search_agents_operator" class="form-control operator-select">
            <option value="in" selected>is</option>
            <option value="not_in">is not</option>

            <option value="null">is empty</option>
            <option value="not_null">is not empty</option>
        </select>
        @can('Select All Agents')
            <div class="form-check form-switch form-check-inline " dir="ltr">
                <small for="">Select all </small> <input type="checkbox" value="all" class="form-check-input"
                    id="selectall">
            </div>
        @endcan


    </div>


    <div class="col-8 scrollable-div">
        <select name='search_agents' id="search_agents" class="form-control search-inputs searchInput" multiple>
            @foreach ($all_agents ?? [] as $agent)
                <option data-image="{{ $agent->thumbnail }}" data-status="{{ $agent->status }}"
                    value="{{ $agent->id }}">
                    {{ $agent->first_name . ' ' . $agent->last_name }}</option>
            @endforeach
        </select>
        <div class="mt-1">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check form-switch form-check-inline" dir="ltr">
                        <small for="showInactiveAgents">Inactive </small> <input type="checkbox" value="inactive"
                            class="form-check-input" id="showInactiveAgents" name="showInactiveAgents">
                    </div>
                </div>
                @if (auth()->user()->hasRole(['Super Admin', 'Line Manager', 'Team Leader', 'Sales Director', 'Head of Secondary', 'CEO']))
                    <div class="col-md-6">
                        <div class="form-check form-switch form-check-inline" dir="ltr">
                            <small for="showMyTeamAgents">Team </small> <input type="checkbox" value="myteam"
                                class="form-check-input" id="showMyTeamAgents" name="showMyTeamAgents">

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
        $(document).on('change', '#showInactiveAgents', function(e) {
            getAgents();
        });
        $(document).on('change', '#showMyTeamAgents', function(e) {
            getAgents();
        });

        getAgents();


        function getAgents() {
            if ($('#showInactiveAgents').is(':checked')) {
                all_agents = 'true';
            } else {
                all_agents = 'false';
            }

            if ($('#showMyTeamAgents').is(':checked')) {
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
                    $("#search_agents").html('');

                    // make option and append to select #search_agents
                    let options = "";
                    result?.data.map(item => {
                        // options +=  `<option value="${item.id}">${item.full_name}</option>`;

                        options +=
                            `<option data-image="${item.thumbnail??item.thumb_url}" data-status="${item.status}" value="${item.id}">${item.full_name}</option>`;
                    });
                    $("#search_agents").html(options);

                    $('#search_agents').select2({
                        width: '100%',
                        placeholder: 'Select Agent',
                        allowClear: true,
                        templateResult: formatAgent,
                        templateSelection: formatAgentView
                    });
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log()
                },
            });
            $(document).on('change', '#selectall', function() {
                if ($(this).is(':checked')) {

                    var agents = $('#search_agents option').map(function() {
                        return $(this).val();
                    }).get();

                    $('#search_agents').val(agents).trigger('change');

                } else {
                    $('#search_agents').val(null).trigger('change');
                }
            });
        }
    </script>
@endpush
