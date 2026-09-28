<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Agent Warning/Suspension Breakdown</h5>
        </div>
        <div class="card-body">
            <table id="agentTerminationReport"
                class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                <thead>
                    <tr>

                        <th>Name</th>
                        <th>Warnings</th>
                        <th>Suspensions</th>

                    </tr>
                </thead>
                <tbody>
                    {{-- @dd($datasets) --}}
                    @foreach ($datasets as $key)
                        <tr>
                            <td><span class="">{{ $key['name'] }}</span></td>
                            <td><span class="">{!! getIntensityOfNumber($key['warnings']) !!}</span></td>
                            <td><span class="">{!! getIntensityOfNumber($key['suspensions']) !!}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
