@if (isset($suspensions))
    @if ($suspensions instanceof Illuminate\Support\Collection && $suspensions->count() > 0)


        <div class="card-body mt-0 form-steps">

            <h5 class="mt-2">User Warnings & Suspensions</h5>
            <div style=" max-height: 500px;overflow-y: auto;">
            <table class="table table-nowrap">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">No. of Leads</th>
                        <th scope="col">Manager</th>
                        <th scope="col">Tag</th>
                        <th scope="col">Date</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($suspensions as $suspension)
                        @php
                            $jsonData = json_decode($suspension->meta_value, true);
                        @endphp
                        <tr>
                            <th scope="row">{{ $suspension->id }}</th>
                            <td>{{ $jsonData['leads'] }}</td>
                            <td>{{ $jsonData['manager']}}</td>
                            <td><span class="badge badge-warning bg-{{$suspension->tag}}">{{ formatText($suspension->tag) }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($suspension->created_at) }} ({{ \Carbon\Carbon::parse($suspension->created_at)->diffForHumans() }})</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            </div>
        </div>
    @endif
@endif
