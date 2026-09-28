@php
    $counter = 1;
@endphp
@foreach ($groupdata as $count)
    @php
        $set = [];

        foreach ($count as $key => $value) {
            $set = $set + [$key => $value];
        }
        $set = json_encode($set);
    @endphp
    <div class="mb-1 col-md-6 group_{{$count['group_id']}}_div hide-all {{ $counter == 1 ? '' : 'd-none' }}">
        <div class="card  row-eq-height">
            <div class="card-header">
                <h4 class="card-title">Lead Source</h4>
            </div>
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link graph-nav active" data-bs-toggle="tab"
                            href="#lead-source-data-graph-{{ $count['group_id'] }}" role="tab" aria-selected="false">
                            Graphic Design
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link table-nav" data-bs-toggle="tab"
                            href="#lead-source-data-table-{{ $count['group_id'] }}" role="tab" aria-selected="false">
                            Tabular Design
                        </a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content  text-muted">
                    <div class="tab-pane graph-nav-div active" id="lead-source-data-graph-{{ $count['group_id'] }}"
                        role="tabpanel">
                        <canvas id="leads-source-{{ $count['group_id'] }}" class="leadSourceWiseGraph leads-source-{{ $count['group_id'] }}" data-dataset ="{{$set}}"></canvas>
                    </div>
                    <div class="tab-pane table-nav-div" id="lead-source-data-table-{{ $count['group_id'] }}"
                        role="tabpanel">
                        <div class="table-responsive" style="max-height: 300px; overflow:scroll">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Source</th>
                                        <th>Leads</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($count['sourceNames'] as $key => $value)
                                        @php
                                            $value = explode(':', $value);
                                        @endphp
                                        <tr>
                                            <td>{{ $value[0] }}</td>
                                            <td>{{ $value[1] }}</td>
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
