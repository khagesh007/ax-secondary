<div class="row mb-4">
 <div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h4>Most Active Agents</h4>
        </div>
        <div class="card-body row">

            @foreach ($topAgents->take(6) as $agent)
                <div class="col-lg-2 col-sm-6">

                    <div class="avatar-md mb-4 position-relative">
                        <img src="{{ $agent->thumb_url ?? $agent->thumbnail }}" alt=""
                            class="img-fluid rounded-circle img-thumbnail">
                    </div>
                    <!-- end card body -->
                    <p class="mb-1"><a href="#"
                            class="text-body">{{ $agent->full_name }}</a></p>

                    <!-- end card -->
                </div>
            @endforeach


        </div>
    </div>
 </div>
 <div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <h4>Least Active Agents</h4>
        </div>
        <div class="card-body row">
            @foreach ($leadsAgents->take(6) as $agent)
                {{-- @foreach ($leadsAgents->take(6) as $agent) --}}
                <div class="col-lg-2 col-sm-6">

                    <div class="avatar-md mb-4 position-relative">
                        <img src="{{ $agent->thumb_url ?? $agent->thumbnail }}" alt=""
                            class="img-fluid rounded-circle img-thumbnail">
                    </div>
                    <!-- end card body -->
                    <p class="mb-1"><a href="#" class="text-body">{{ $agent->full_name }}</a></p>
                </div>
            @endforeach
        </div>
    </div>
 </div>
</div>





    <div class="col-md-12 d-none" id="userActivityReport">
        <div class="card">
            <div class="card-header">
                <h4>User Activity</h4>
            </div>

            @php
                $set = [];
                foreach ($systemUsers as $key => $value) {
                    $set = $set + [$key => $value];
                }
                $set = json_encode($set);
            @endphp

            <div class="card-body">
                <div id="useActivityData" class="apex-charts useActivityData" dir="ltr"
                    data-dataset ="{{ $set }}"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Meta Report</h4>
                </div>
                <div class="card-body">
                    <div id="userMetaSplit" class="apex-charts userMetaSplit" dir="ltr"
                        data-warnings="{{ $warnings }}" data-suspensions="{{ $suspensions }}"
                        data-warnings_percentage="{{ $warningsPercentage }}"
                        data-suspensions_percentage="{{ $suspensionsPercentage }}"></div>
                </div>
            </div>
        </div>

</div>
