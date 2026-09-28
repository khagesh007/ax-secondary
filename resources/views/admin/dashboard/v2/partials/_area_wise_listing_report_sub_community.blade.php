<div class="col-md-12">

    <div class="card">
        <div class="card-header align-items-center d-flex">
            <div class="flex-grow-1 oveflow-hidden">
            </div>
            <div class="flex-shrink-0 ms-2">
                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0"
                    role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#table-two" role="tab">
                            <i class=" bx bx-table"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#graph-two" role="tab">
                            <i class=" bx bx-bar-chart-square"></i>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
        <div class="card-body" style="padding: 0">
            <!-- Tab panes -->
            <div class="tab-content text-muted">
                <div class="tab-pane active" id="table-two" role="tabpanel">
                    @if ($locations->count() > 0)
                        <div class="table-responsive table-card m-3">
                            <table id="buttons-datatables-community" class="display table align-middle">
                                <div id="datatableSearch"></div>
                                <thead>
                                <tr>
                                    <th style="background-color: #f0e4dd!important;z-index:400"><small>Agent</small>
                                    </th>
                                    @foreach ($locations as $location)
                                        <th class="" data-location-name="{{ $location->name }}"
                                            style="background-color: #f0e4dd!important;"><small
                                                class="process_header location-names-sub" title="{{$location_name}}"
                                                data-title="{{$location->name}}">{{ $location->id }}
                                                _{{ $location->name }}</small>
                                        </th>
                                    @endforeach
                                    @foreach ($community_property as $location)
                                        <th class="" data-location-name="{{ $location->name }}"
                                            style="background-color: #f0e4dd!important;"><small
                                                class="process_header location-names-sub" title="{{$location_name}}"
                                                data-title="{{$location->name}}">{{ $location->id }}
                                                _{{ $location->name }}</small>
                                        </th>
                                    @endforeach
                                    <th style="background-color: #f0e4dd!important"><small>Total</small></th>
                                </tr>
                                </thead>
                                <tbody class="">
                                @foreach ($agents as $agent)
                                    <tr>
                                        <td style="background-color: rgb(242, 240, 240)!important">
                                            <div class="d-flex align-items-center" data-agent-id="{{ $agent->id }}"
                                                 title="{{ $agent->full_name }}">
                                                <div class="flex-shrink-0 me-2">
                                                    <img
                                                        src="{{ $agent->thumbnail ?? 'https://ui-avatars.com/api/' . $agent->full_name }}"
                                                        alt="" class="avatar-xs rounded-circle">
                                                </div>

                                                <div class="flex-grow-1">{{ $agent->full_name }}<p title="Created at"
                                                                                                   class="mb-0">
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        @php
                                            $count = 0;
                                        @endphp
                                        @foreach ($locations as $location)
                                            @php
                                                $location_name = $location->id.'_'. $location->name;
                                            @endphp
                                            <td>{{ $agentLocationCounts[$agent->name][$location_name] == 0 ? '-' : $agentLocationCounts[$agent->name][$location_name] ?? 0 }}
                                            </td>
                                            @php
                                                $count += $agentLocationCounts[$agent->name][$location_name];
                                            @endphp
                                        @endforeach
                                        @foreach ($community_property as $location)
                                            @php
                                                $location_name = $location->id.'_'. $location->name;
                                            @endphp
                                            <td>{{ $agentLocationCountsSub[$agent->name][$location_name] == 0 ? '-' : $agentLocationCountsSub[$agent->name][$location_name] ?? 0 }}
                                            </td>
                                            @php
                                                $count += $agentLocationCountsSub[$agent->name][$location_name];
                                            @endphp
                                        @endforeach


                                        <td style="background-color: rgb(225, 221, 221)!important">
                                            <b>{{ $count }}</b>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th style="background-color: #f0e4dd!important;z-index:400">Summary</th>
                                    @foreach ($locations as $location)
                                        <th class="showCountSubCommunity " location-counts-sub
                                            style="background-color: #f0e4dd!important">0
                                        </th>
                                    @endforeach
                                    @foreach ($community_property as $location)
                                        <th class="showCountSubCommunity location-counts-sub"
                                            style="background-color: #f0e4dd!important">0
                                        </th>
                                    @endforeach
                                    <th class="showCountSubCommunity"
                                        style="background-color: #f0e4dd!important;z-index:400">0
                                    </th>
                                </tr>

                                <tr>
                                    <th style="background-color: #f0e4dd!important;z-index:400">%</th>
                                    @foreach ($locations as $location)
                                        <th class="showPercentageSubCommunity location-percentage-sub"
                                            style="background-color: #f0e4dd!important">0
                                        </th>
                                    @endforeach
                                    @foreach ($community_property as $location)
                                        <th class="showPercentageSubCommunity location-percentage-sub"
                                            style="background-color: #f0e4dd!important">0
                                        </th>
                                    @endforeach
                                    <th class="showPercentageSubCommunity location-percentage-sub"
                                        style="background-color: #f0e4dd!important;z-index:400">
                                        0
                                    </th>
                                </tr>
                                </tfoot>
                            </table>

                        </div>
                    @else
                        <div class="alert alert-danger">No Locations Found</div>
                    @endif

                </div>
                <div class="tab-pane" id="graph-two" role="tabpanel">
                    <div id="tableAppendTwoGraphTwo" style="height:1000px; width:1100px"></div>
                </div>

            </div>
        </div><!-- end card-body -->
    </div>

</div>
