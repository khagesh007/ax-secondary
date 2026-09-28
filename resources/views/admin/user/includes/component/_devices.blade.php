@if (isset($devices))
@if ($devices instanceof Illuminate\Support\Collection && $devices->count() > 0)
        <div class="card-body mt-0 form-steps">

            <h5 class="mt-2">User Devices</h5>
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col" style="max-width: 140px;">Location</th>
                        <th scope="col">App Version</th>
                        <th scope="col">Device Name</th>
                        <th scope="col">Device OS</th>
                        <th scope="col">Login IP</th>
                        <th scope="col">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($devices as $device)
                        <tr>
                            <th scope="row"><a href="#" class="fw-semibold">{{ $device->id }}</a></th>
                            <td><small>{{ $device->location_name }}</small></td>
                            <td>{{ $device->app_version }}</td>
                            <td>{{ $device->device_name }}</td>
                            <td> {{$device->device_version}}</td>
                            <td>{{ $device?->ip_address }}</td>
                            <td>{{ $device->created_at->format('d M Y h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
@endif
@endif
