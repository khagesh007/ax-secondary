<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="card-title mb-0">Advanced Filters</h6>
                        </div>
                        <div class="col-md-3 text-right" style="min-width: 150px">
                            @if (auth()->user()->hasRole(['Super Admin']))
                                {{-- <select name="" id="open_filter" class="form-control select2">
                                <option value=""></option>
                                @foreach ($filters as $filter)
                                    <option value="{{ $filter->id }}">{{ $filter->name }}</option>
                                @endforeach
                            </select> --}}
                            @endif
                        </div>

                        <div class="col-md-3 text-end">
                            <div class="btn-group admin_approval_buttons " role="group">
                                <button id="btnGroupDropFilters" type="button"
                                    class="btn btn-sm action-clear waves-effect dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Filter Actions
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDropFilters">

                                    <li><a class="dropdown-item" id="saveMyFilter">Save my filter preferences</a></li>

                                    <li><a class="dropdown-item" id="clearSaved">Clear my filter preferences</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body bg-light">
                    <div class="row" id="append-here">

                        <div class="col-md-4 append-before">
                            <div class="row">
                                <div class="col-12">
                                    <select name="" id="applicator" class="form-control select2">
                                        <option value="">Select Filter to Apply</option>
                                        @foreach (config('configurations.filters.Contact') as $key => $value)
                                            <option value="{{ $value['name'] }}"
                                                data-operator="{{ $value['operator'] }}"
                                                data-name="{{ $value['name'] }}" data-label="{{ $value['label'] }}"
                                                data-type="{{ $value['type'] }}"
                                                data-variable="{{ $value['variable'] }}"
                                                data-template="{{ $value['template'] }}">
                                                {{ removeUnderScore($value['label']) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row mt-2">


                        @can('Bulk SMS')
                        <div class="col-3">
                            <!-- Buttons with Label -->
                        <button type="button" id="send-sms" class="btn btn-sm btn-primary btn-label waves-effect waves-light"><i class="ri-message-3-line label-icon align-middle fs-16 me-2"></i> Start SMS Campaign</button>
                        {{-- <button class="btn btn-sm btn-soft-primary action-primary" id="send-sms"><i
                            class=" ri-message-3-line"></i> Send SMS</button> --}}
                        </div>
                        @endcan
                        @if (auth()->user()->hasRole(['Admin', 'Super Admin']))
                            <div class="col-3">
                                <div class="form-check form-switch form-switch-success">
                                    <input class="form-check-input search-inputs" type="checkbox" name="search_deleted"
                                        role="switch" id="search_deleted">
                                    <label class="form-check-label" for="search_deleted">Show Deleted
                                        Also</label>
                                </div>
                            </div>
                        @endif


                    </div>

                    <div class="mt-4 align-items-center gap-2 text-end">
                        <button type="submit" class="btn btn-sm  btn-primary action-search me-3" id="searchSearchBtn">
                            <i class="ri-search-line align-bottom me-1"></i> Search
                        </button>
                        @if (auth()->user()->hasRole(['Super Admin']))
                            {{-- <button type="button" class="btn btn-sm  btn-warning action-clear me-3"
                            id="saveAndSearch">
                            <i class="ri-search-line align-bottom me-1"></i> Save and Search
                        </button>

                        <button type="button" class="btn btn-sm  btn-warning action-clear me-3"
                            id="saveMyFilter">
                            <i class="ri-save-line align-bottom me-1"></i> Save My Filter
                        </button>

                        <button type="button" class="btn btn-sm  btn-warning action-clear me-3" id="clearSaved">
                            <i class="ri-save-line align-bottom me-1"></i> Clear My Filter
                        </button> --}}
                        @endif
                        <a href="javascript:void(0)" class="btn-sm btn text-warning action-clear me-3"
                            id="clearSearchBtn">Clear</a>
                        <a href="javascript:void(0)" type="button" class="btn btn-sm text-danger action-close"
                            onclick="toggleAdvanceSearch();">Close</a>
                    </div>
                </div>


            </div>

        </form>
    </div>
</div>

@section('pagejs')
    <script>
        $(document).on('click', '#saveMyFilter', function() {
            localStorage.removeItem('contactsFilterData');
            var dataArray = [];
            $('#append-here .col-md-4').each(function(index) {
                var colMd4 = $(this);
                if (colMd4.hasClass('append-before')) {
                    return true;
                }
                if (colMd4.hasClass('d-none')) {
                    return true;
                }
                var dataName = colMd4.find('.col-1 span').data('name');
                var dataVariable = colMd4.find('.col-1 span').data('variable');
                var dataOperator = colMd4.find('.col-1 span').data('operator');
                var dataTemplateName = colMd4.find('.col-1 span').data('template');
                var dataLabel = colMd4.find('.col-1 span').data('label');
                var dataType = colMd4.find('.col-1 span').data('type');


                var dataObject = {
                    dataName: dataName,
                    dataVariable: dataVariable,
                    dataOperator: dataOperator,
                    dataTemplateName: dataTemplateName,
                    dataLabel: dataLabel,
                    dataType: dataType
                };
                console.log(dataObject);
                dataArray.push(dataObject);
            });
            console.log(JSON.stringify(dataArray));

            // Store dataArray in local storage
            localStorage.setItem('contactsFilterData', JSON.stringify(dataArray));
            showToastify('Personalized filters saved.', "success");

        });



        $(document).on('click', '#clearSaved', function() {
            localStorage.removeItem('contactsFilterData');
            showToastify('Personalized filters cleared.', "success");
        });

        $(document).ready(function() {
            var storedData = localStorage.getItem('contactsFilterData');
            if (storedData) {
                loadFilterData(storedData);
            } else {
                let autosave = [{
                    "dataName": "contact_type",
                    "dataVariable": "type",
                    "dataOperator": "is",
                    "dataTemplateName": "",
                    "dataLabel": "type",
                    "dataType": "select"
                }, {
                    "dataName": "mobile",
                    "dataVariable": "mobile",
                    "dataOperator": "is",
                    "dataTemplateName": "",
                    "dataLabel": "mobile",
                    "dataType": "text"
                }, {
                    "dataName": "contact_sources",
                    "dataVariable": "source",
                    "dataOperator": "is",
                    "dataTemplateName": "",
                    "dataLabel": "sources",
                    "dataType": "select"
                }, {
                    "dataName": "agents",
                    "dataVariable": "agents",
                    "dataOperator": "is",
                    "dataTemplateName": "agents",
                    "dataLabel": "agents",
                    "dataType": "select"
                }];


                localStorage.setItem('contactsFilterData', JSON.stringify(autosave));
                var storedData = localStorage.getItem('contactsFilterData');
                loadFilterData(storedData);
            }


        });

        function loadFilterData(storedData) {
            var listingLeadsFilterData = JSON.parse(storedData);
            var numItems = Object.keys(listingLeadsFilterData).length;
            var progressStep = 100 / numItems;
            var currentProgress = 0;
            $('#filterLoder').css('width', currentProgress + '%').attr('aria-valuenow', currentProgress);
            listingLeadsFilterData.forEach(function(item) {
                $('#applicator').val(item.dataName).trigger('change');
                currentProgress += progressStep;
                $('#filterLoder').css('width', currentProgress + '%').attr('aria-valuenow',
                    currentProgress);
            });
        }


        $(document).on('change', '.operator-select', function() {
            var operator = $(this).val();
            var input = $(this).closest('.row').find('.searchInput');
            var select2Element = input.next('.select2-container').find('select');
            var operators = ["null", "not_null", "today", "yesterday", "last_week", "last_month", "this_month",
                "this_week"
            ];
            if (operators.includes(operator)) {
                select2Element.prop('disabled', true);
                input.prop('disabled', true);
            } else {
                select2Element.prop('disabled', false);
                input.prop('disabled', false);
            }
        });

        $(document).on('change', '#applicator', function() {


            var operator = $(this).find(':selected').data('operator');
            var name = $(this).find(':selected').data('name');
            var type = $(this).find(':selected').data('type');
            var label = $(this).find(':selected').data('label');
            var variable = $(this).find(':selected').data('variable');
            var template = $(this).find(':selected').data('template');
            let input = '';
            var validTypes = ['communities', 'sub_communities', 'beds', 'baths', 'prices'];
            if (type == 'text' || type == 'number' || type == 'date' || type == 'email' || type == 'phone') {
                let extras = '';
                if (type == 'date') {
                    extras = 'data-provider="flatpickr" data-date-format="Y-m-d" data-range-date="true"';
                }

                input = ` <div class="col-md-4 mb-2">
                            <div class="row">
                                <div class="col-3">
                                    <label for="agent" class="form-label">${removeUnderScore(label)}</label>
                                    <select name='search_${name}_operator' id="search_${name}_operator"
                                        class="form-control operator-select">

                                    </select>
                                </div>
                                <div class="col-8">
                                    <input type="${type}" ${extras} class="form-control search-inputs searchInput" id="search_${name}"
                                        name="search_${name}" placeholder="Search ${removeUnderScore(label)}" value="" >
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span  data-template="${template}"  data-variable="${variable}" data-operator="${operator}" data-name="${name}" data-label="${label}" data-type="${type}" class="remove-filter waves-effect waves-light"><i class="ri-delete-bin-5-line"></i></span>
                                </div>
                            </div>
                        </div>`

                $.ajax({
                    url: "{{ route('admin.work.contacts.filterHelper') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        type: variable,
                    },
                    success: function(result) {

                        let operator_options = '';

                        Object.entries(result?.operators).forEach(function([key, value]) {
                            operator_options += `<option value="${key}">${value}</option>`;
                        });
                        $('#search_' + name + '_operator').html(operator_options);

                        if (type == 'date') {
                            $("#search_" + name).flatpickr({
                                altInput: true,
                                mode: "range",
                                dateFormat: "Y-m-d H:i",
                            });
                        }


                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            } else if (type == 'select') {
                let selectall = '';
                let agentsOptions = '';
                let customClass = ''
                if (name == 'agents') {
                    customClass = 'scrollable-div'
                    selectall =
                        ' <div class="form-check form-switch form-check-inline " dir="ltr"><small for="">All </small> <input type="checkbox" value="all" class="form-check-input" id="selectall"> </div>';
                    agentsOptions = ` <div class="mt-1">
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
        </div>`;
                }
                input = `<div class="col-md-4 mb-2 d-none" id="search_${name}_sleep">
                            <div class="row">
                                <div class="col-3">
                                    <label for="agent" class="form-label">${removeUnderScore(label)}</label>
                                    <select name='search_${name}_operator' id="search_${name}_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                    ${selectall}
                                </div>
                                <div class="col-8 ${customClass}">
                                    <select name="search_${name}" id="search_${name}" class="form-control search-inputs searchInput" multiple="multiple">

                                    </select>
                                    ${agentsOptions}
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span  data-template="${template}"  data-variable="${variable}" data-operator="${operator}" data-name="${name}" data-label="${label}" data-type="${type}" class="remove-filter waves-effect waves-light"><i class="ri-delete-bin-5-line"></i></span>

                                </div>
                            </div>
                        </div>`

                $.ajax({
                    url: "{{ route('admin.work.contacts.filterHelper') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        type: variable,
                    },
                    success: function(result) {
                        console.log(result);
                        let operator_options = '';

                        Object.entries(result?.operators).forEach(function([key, value]) {
                            operator_options +=
                                `<option value="${key}">${value}</option>`;
                        });
                        $('#search_' + name + '_operator').html(operator_options);
                        let options = "";

                        if (template == 'constants') {
                            Object.keys(result?.data).forEach(function(key) {
                                options +=
                                    `<option value="${key}">${result?.data[key]}</option>`;
                            });
                            $('#search_' + name).html(options);
                            let placeholder = 'Select ' + removeUnderScore(label);
                            $('#search_' + name).select2({
                                placeholder: placeholder,
                                allowClear: true,
                                focusOnOpen: false,
                                closeOnSelect: false,
                                minimumResultsForSearch: Infinity,
                            });
                            $('#search_' + name + '_sleep').removeClass('d-none');

                        } else if (template == 'telesales' || template == 'agents' || template ==
                            'previous_agents') {
                            result?.data.map(item => {
                                options +=
                                    `<option data-image="${item.thumbnail}" data-status="${item.status}" value="${ item.id }">${ item.first_name } ${ item.last_name }</option>`;

                            })
                            let placeholder = 'Select ' + removeUnderScore(label);
                            $('#search_' + name).html(options);

                            $('#search_' + name).select2({
                                placeholder: placeholder,
                                allowClear: true,
                                focusOnOpen: false,
                                closeOnSelect: false,
                                minimumResultsForSearch: Infinity,
                                templateResult: formatAgent,
                                templateSelection: formatAgentView
                            });
                            $('#search_' + name + '_sleep').removeClass('d-none');

                        } else {
                            if (name == 'communities') {
                                filter_locationSelect2();
                                $('#search_' + name + '_sleep').removeClass('d-none');
                            } else {
                                result?.data.map(item => {
                                    options +=
                                        `<option value="${item.id}">${item.name}</option>`;

                                })
                                $('#search_' + name).html(options);
                                $('#search_' + name).select2({
                                    placeholder: 'Select ' + label,
                                    allowClear: true,
                                    focusOnOpen: false,
                                    closeOnSelect: false,
                                    minimumResultsForSearch: Infinity,
                                });
                                $('#search_' + name + '_sleep').removeClass('d-none');
                            }

                        }


                    },
                    error: function(result) {
                        console.log(result);
                    }
                });

            }

            $(input).insertBefore('.append-before');
            $(this).find(':selected').remove();



        });

        $(document).on('click', '.remove-filter', function() {
            var operator = $(this).data('operator');
            var name = $(this).data('name');
            var type = $(this).data('type');
            var label = $(this).data('label');
            var variable = $(this).data('variable');
            var template = $(this).data('template');
            var option =
                `<option value="${name}" data-template="${template}" data-variable="${variable}" data-operator="${operator}" data-name="${name}" data-label="${label}" data-type="${type}">${removeUnderScore(label)}</option>`;
            $('#applicator').append(option);
            $(this).closest('.col-md-4').remove();
        })

        $(document).on('click', '.hide-section', function() {

            var operator = $(this).data('operator');
            var name = $(this).data('name');
            var type = $(this).data('type');
            var label = $(this).data('label');
            var variable = $(this).data('variable');
            var template = $(this).data('template');
            var option =
                `<option value="${name}" data-template="${template}" data-variable="${variable}" data-operator="${operator}" data-name="${name}" data-label="${label}" data-type="${type}">${removeUnderScore(label)}</option>`;
            $('#applicator').append(option);
            // find nearest col-md and reset its select2
            $(this).closest('.col-md-4').find('.searchInput').val(null).trigger('change');

            $(this).closest('.col-md-4').addClass('d-none');
        })


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

        function removeUnderScore(str) {
            return str.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

        }
    </script>
@endsection
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
