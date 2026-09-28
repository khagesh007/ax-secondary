<div class="">
    <div class="card-body" style="padding: 0">
        <div class="tab-content text-muted">
            <div class="tab-pane active" id="table-one" role="tabpanel">
                @if ($locations->count() > 0)
                    <div class=" table-card m-3">
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1 no-border d-inline-flex"
                            role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link All  {{$status==''?'active':''}}  statusFilterClear margin-minus-4 listingStatus"
                                   data-bs-toggle="tab"
                                   id="All" href="#" role="tab" aria-selected="false" tabindex="-1">
                                    All
                                </a>
                            </li>

                            @foreach (\App\Models\Property::PUBLISH_STATUS_SELECT ?? [] as $key => $value)
                                @if ($key == 'ARCHIVED')
                                    @if (auth()->user()->hasRole(['Admin', 'Super Admin']))
                                        <li class="nav-item" role="presentation" id="status_id_{{ $key }}_placement">
                                            <a class="nav-link All  statusFilter margin-minus-4 text-uppercase listingStatus {{$status==$key?'active':''}}"
                                               data-bs-toggle="tab" id="status_id_{{ $key }}" href="javascript:void(0)"
                                               data-status="{{ $key }}" role="tab" aria-selected="false"
                                               tabindex="-1">
                                                {{ $value }}
                                            </a>
                                        </li>
                                    @endif
                                @else
                                    <li class="nav-item" role="presentation" id="status_id_{{ $key }}_placement">
                                        <a class="nav-link All  statusFilter margin-minus-4 text-uppercase listingStatus  {{$status==$key?'active':''}}"
                                           data-bs-toggle="tab"
                                           id="status_id_{{ $key }}" href="javascript:void(0)"
                                           data-status="{{ $key }}" role="tab" aria-selected="false"
                                           tabindex="-1">
                                            {{ $value }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach

                            <li class="nav-item" role="presentation" id="status_id_Live_placement">
                                <a class="nav-link All  statusFilterLive listingStatus" data-bs-toggle="tab" id="All"
                                   href="#"
                                   role="tab" aria-selected="false" tabindex="-1" data-status="PUBLISHED"
                                   data-is_live="1">
                                    <i title="Live Listing" class="bx bx-check-circle text-success"
                                       style="font-size: 20px"></i>
                                </a>
                            </li>
                            <li>
                                <select name="portal_id" id="portal_id" class="form-control select2 inputs">
                                    <option value="">Select Portal</option>
                                    @foreach ($portals as $portal)
                                        <option
                                            value="{{ $portal->id }}" {{$selected_portal==$portal->id?'selected':''}}>{{ $portal->name }}</option>
                                    @endforeach
                                </select>
                            </li>
                        </ul>


                        <div class="table-responsive">
                            <table id="buttons-datatables" class="display table align-middle">
                                <div id="datatableSearch"></div>
                                <thead>
                                <tr>
                                    <th style="background-color: #f0e4dd!important;z-index:400"><small>Agent</small>
                                    </th>
                                    @foreach ($locations as $location)
                                        @php
                                            $location_name = $location->id.'_'. $location->name;
                                        @endphp
                                        <th class=""
                                            style="background-color: #f0e4dd!important;"><small
                                                title="{{$location_name}}" data-title="{{$location->name}}"
                                                data-location-name="{{$location->name}}"
                                                class="location-names getlocationdetails">{{ shortNameGenerator($location->name) }}</small>
                                        </th>
                                    @endforeach
                                    <th style="background-color: #f0e4dd!important"><small title="Others"
                                                                                           data-title="Others"
                                                                                           class="location-names">Others</small>
                                    </th>
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
                                                        src="{{ $agent->thumbnail ?? "https://ui-avatars.com/api/".$agent->full_name}}"
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
                                        <td>{{ $agentLocationCounts[$agent->name]['Others'] == 0 ? '-' : $agentLocationCounts[$agent->name]['Others'] ?? 0 }}
                                        </td>
                                        @php
                                            $count += $agentLocationCounts[$agent->name]['Others'];
                                        @endphp

                                        <td style="background-color: rgb(225, 221, 221)!important"><b>{{ $count }}</b>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th style="background-color: #f0e4dd!important;z-index:400">Summary</th>
                                    @foreach ($locations as $location)
                                        <th class="showCount location-counts"
                                            style="background-color: #f0e4dd!important">0
                                        </th>
                                    @endforeach

                                    <th class="showCount location-counts" style="background-color: #f0e4dd!important">
                                        0
                                    </th>
                                    <th class="showCount" style="background-color: #f0e4dd!important;z-index:400">0</th>
                                </tr>

                                <tr>
                                    <th class="dtfc-fixed-left" style="background-color: #f0e4dd!important;z-index:400">%</th>
                                    @foreach ($locations as $location)
                                        <th class="showPercentage location-percentage"
                                            style="background-color: #f0e4dd!important">0
                                        </th>
                                    @endforeach

                                    <th class="showPercentage location-percentage"
                                        style="background-color: #f0e4dd!important">0
                                    </th>
                                    <th class="showPercentage" style="background-color: #f0e4dd!important;z-index:400">
                                        0
                                    </th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-danger">No Locations Found</div>
                @endif
            </div>
            <div class="tab-pane" id="graph-one" role="tabpanel">
                <div id="tableAppendTwoGraph" style="height:800px; width:1100px"></div>
            </div>
        </div>
    </div>
</div>

