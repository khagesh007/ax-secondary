
@can('See Agents Reports')


<div class="col-md-12 pb-3">
    <div class="card">
       <div class="card-body text-center p-3">
       <h5>Performance Report</h5>
       </div>
    </div>
</div>
<div class="col-xl-4">
    <div class="card card-height-100">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">⚠️ Leads In NEW Status</h4>
            <div class="flex-shrink-0">
                <button class="btn btn-sm btn-primary expand-report" data-title="Leads In NEW Status"><i class=" bx bx-expand"></i></button>
            </div>
        </div><!-- end card header -->

        <div class="card-body">
            <div data-simplebar style="max-height: 250px;" class="mb-4">
                <ul class="list-group">
                    @foreach ($new_status_lead as $user)
                    @if ($user['leads_count']>2)
                            <li class="list-group-item">
                                <div class="d-flex align-items-center list-content">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $user['thumb_url']   }}" alt=""
                                                    class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0">{{ $user['name'] }}</h6>
                                                <p class="text-muted">{{ teamFinder($user['bitrix_role']) }} - {{ $user->parent->full_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text">{{ $user['leads_count'] }} Leads</span>
                                    </div>
                                </div>
                            </li>
                            @endif
                        {{-- @endif --}}
                    @endforeach


                </ul>
            </div>
        </div>
    </div> <!-- .card-->
</div> <!-- .col-->
<div class="col-xl-4">
    <div class="card card-height-100">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">⚠️ Incomplete Leads</h4>
            <div class="flex-shrink-0">
                <button class="btn btn-sm btn-primary expand-report" data-title="Incomplete Leads"><i class=" bx bx-expand"></i></button>

            </div>
        </div><!-- end card header -->

        <div class="card-body">
            <div data-simplebar style="max-height: 250px;" class="mb-4">
                <ul class="list-group">
                    @foreach ($incomplete_lead_agents as $user)
                    {{-- @dd($user->parent->full_name); --}}
                    @if ($user['leads_count']>2)
                            <li class="list-group-item">
                                <div class="d-flex align-items-center list-content">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $user['thumb_url'] }}" alt=""
                                                    class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0">{{ $user['name'] }}</h6>
                                                <p class="text-muted">{{ teamFinder($user['bitrix_role']) }} - {{ $user->parent->full_name }} </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text">{{ $user['leads_count'] }} Leads</span>
                                    </div>
                                </div>
                            </li>
                            @endif
                        {{-- @endif --}}
                    @endforeach


                </ul>
            </div>
        </div>
    </div> <!-- .card-->
</div> <!-- .col-->
<div class="col-xl-4">
    <div class="card card-height-100">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">🗓 Days since their last event</h4>
            <div class="flex-shrink-0">
                <button class="btn btn-sm btn-primary expand-report"  data-title="Days since their last event"><i class=" bx bx-expand"></i></button>
            </div>
        </div><!-- end card header -->

        <div class="card-body">
            <div data-simplebar style="max-height: 250px;" class="mb-4">
                <ul class="list-group">
                    @foreach ($agents as $user)
                            <li class="list-group-item">
                                <div class="d-flex align-items-center list-content">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $user['image'] }}" alt=""
                                                    class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0">{{ $user['name'] }}</h6>
                                                <p class="text-muted">{{ teamFinder($user['bitrix_role']) }} - {{ $user['manager']}}</p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text">{{ $user['days'] }} Days</span>
                                    </div>
                                </div>
                            </li>
                        {{-- @endif --}}
                    @endforeach


                </ul>
            </div>
        </div>
    </div> <!-- .card-->
</div> <!-- .col-->
<div class="col-xl-4">
    <div class="card card-height-100">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">⚠️ Leads by Source in {{ $hours }} hours</h4>
            <div class="flex-shrink-0">
                <button class="btn btn-sm btn-primary expand-report"  data-title="Leads by Source in {{ $hours }} hours"><i class=" bx bx-expand"></i></button>

            </div>
        </div><!-- end card header -->

        <div class="card-body">
            <div data-simplebar style="max-height: 250px;" class="mb-4">
                <ul class="list-group">
                    @foreach ($sourceWithLeadsCount as $source)
                    @if ($source['leads_count']>2)
                            <li class="list-group-item">
                                <div class="d-flex align-items-center list-content">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $source['thumbnail']??'https://secondary.axcrm.ae/assets/images/logo/favicon-32x32.png' }}" alt=""
                                                    class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0">{{ $source['name'] }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text">{{ $source['leads_count'] }} Leads</span>
                                    </div>
                                </div>
                            </li>
                            @endif
                        {{-- @endif --}}
                    @endforeach


                </ul>
            </div>
        </div>
    </div> <!-- .card-->
</div> <!-- .col-->
<div class="col-xl-4">
    <div class="card card-height-100">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">🚨 Suspension Details</h4>
            <div class="flex-shrink-0">
                <button class="btn btn-sm btn-primary expand-report" data-title="Suspension Details"><i class=" bx bx-expand"></i></button>

            </div>
        </div><!-- end card header -->
        <div class="card-body">
            <div data-simplebar style="max-height: 250px;" class="mb-4">
                <ul class="list-group">
                    @foreach ($users as $user)
                        @php
                            $suspensions = $user->suspensions->count();
                            $warnings = $user->warnings->count();
                        @endphp
                        @if ($suspensions > 2)

                            @php
                                    $ratio = $suspensions/$warnings;
                                    $ratio = number_format((float)$ratio, 2, '.', '');
                            @endphp

                            <li class="list-group-item">
                                <div class="d-flex align-items-center list-content">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $user->thumb_url }}" alt=""
                                                    class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0">{{ $user->full_name }}</h6>
                                                <p class="fs-12 mb-0 text-muted">W: {{ $warnings }} - Ratio {{ $ratio }}   </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text">{{ $suspensions }}</span>
                                    </div>
                                </div>
                            </li>
                        @endif
                    @endforeach


                </ul>
            </div>
        </div>
    </div> <!-- .card-->
</div> <!-- .col-->

<div class="col-xl-4">
    <div class="card card-height-100">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">🗓 0 DEALS SINCE JOINING (MORE THAN 2 MONTH)</h4>
            <div class="flex-shrink-0">
                <button class="btn btn-sm btn-primary expand-report" data-title="0 DEALS SINCE JOINING (MORE THAN 2 MONTH)"><i class=" bx bx-expand"></i></button>
            </div>
        </div><!-- end card header -->

        <div class="card-body">
            <div data-simplebar style="max-height: 250px;" class="mb-4">
                <ul class="list-group">
                    @foreach ($no_deal_agents as $user)
                    <li class="list-group-item">
                                <div class="d-flex align-items-center list-content">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $user['thumb_url']   }}" alt=""
                                                    class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0">{{ $user['name'] }}</h6>
                                                <p class="text-muted">{{ teamFinder($user['bitrix_role']) }} - {{ $user->parent->full_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text">{{ date('M d, Y',strtotime($user['joining_date'])) }}</span>
                                    </div>
                                </div>
                            </li>
                        {{-- @endif --}}
                    @endforeach


                </ul>
            </div>
        </div>
    </div> <!-- .card-->
</div> <!-- .col-->

<div class="col-xl-4">
    <div class="card card-height-100">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">🗓 Days Since Last Deal</h4>
            <div class="flex-shrink-0">
                <button class="btn btn-sm btn-primary expand-report" data-title="Days Since Last Deal"><i class=" bx bx-expand"></i></button>
            </div>
        </div><!-- end card header -->

        <div class="card-body">
            <div data-simplebar style="max-height: 250px;" class="mb-4">
                <ul class="list-group">
                    @foreach ($sorted_deals_days_count as $user)
                    <li class="list-group-item">
                                <div class="d-flex align-items-center list-content">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $user['thumb_url']   }}" alt=""
                                                    class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0">{{ $user['name'] }}</h6>
                                                <p class="text-muted">{{ teamFinder($user['bitrix_role']) }} - {{ $user->parent->full_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text">{{ $user['days_since_last_deal'] }}</span>
                                    </div>
                                </div>
                            </li>
                        {{-- @endif --}}
                    @endforeach


                </ul>
            </div>
        </div>
    </div> <!-- .card-->
</div> <!-- .col-->
@endcan



