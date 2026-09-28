@php
    $counter = 1;
@endphp
@foreach ($groupdata as $count)
    {{-- @dd($count) --}}
    @php
        $set = [];
        foreach ($count as $key => $value) {
            $set = $set + [$key => $value];
        }
        $set = json_encode($set);
    @endphp

    <div class="mb-1 col-md-6 group_{{ $count['group_id'] }}_div hide-all {{ $counter == 1 ? '' : 'd-none' }}">
        <div class="card  row-eq-height">
            <div class="card-header">
                <div class="d-flex">
                    <div class="flex-grow-0">
                        <h5 class="card-title">Events</h5>
                    </div>
                    <div class="flex-grow-1 text-end">
                        <a class="" href="{{route('admin.reports.group.eventsOverview')}}?group_id={{$count['group_id']}}" target="_blank"><i class="mdi-open-in-new mdi"></i></a>
                    </div>
                </div>


            </div>
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link graph-nav active" data-bs-toggle="tab"
                            href="#events-count-graph-{{ $count['group_id'] }}" role="tab" aria-selected="false">
                            Graphic Design
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link table-nav" data-bs-toggle="tab"
                            href="#events-count-table-{{ $count['group_id'] }}" role="tab" aria-selected="false">
                            Tabular Design
                        </a>
                    </li>

                </ul>
                <!-- Tab panes -->
                <div class="tab-content  text-muted">
                    <div class="tab-pane graph-nav-div active" id="events-count-graph-{{ $count['group_id'] }}"
                        role="tabpanel">
                        <canvas id="events-count-{{ $count['group_id'] }}"
                            class="eventWiseStatus events-count-{{ $count['group_id'] }}"
                            data-dataset ="{{ $set }}"></canvas>

                    </div>
                    <div class="tab-pane table-nav-div" id="events-count-table-{{ $count['group_id'] }}"
                        role="tabpanel">


                        <div class="table-responsive" style="max-height: 300px; overflow:scroll">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Count</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($count['eventNames'] as $key => $value)

                                        <tr>
                                            <td>{{ $value }}</td>
                                            <td>{{ $count['eventCounts'][$key] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
    </div>
    @php
        $counter++;
    @endphp
@endforeach
