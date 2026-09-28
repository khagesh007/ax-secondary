<div class="row">


    <div class="col-md-12 text-center">
        <div class="card m-2 sticky-top-with-margin-bottom position-sticky">
            <div class="card-body">
                <b>Portal Statistics</b>
            </div>
        </div>
        <div class="row row-cols-md-3 row-cols-1">

            @foreach ($groupdata as $data)
            <div class="col col-lg border-end p-2">
                    <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Listed on Portals</h4>
                        </div>
                        <div class="card-body">
                            @php
                                // get total number of properties and then find percentage of each portal
                                $total = 0;
                                foreach ($data as $key => $value) {
                                    $total = $total + $value->properties_count;
                                }
                                $total = $total / 100;

                                // save in array for use in php
                                $set = [];
                                foreach ($data as $key => $value) {
                                    $set = $set + [$key => $value->properties_count];
                                }
                                $set = json_encode($set);

                            @endphp


                            <div class="mt-3 pt-2">
                                <div class="progress progress-lg rounded-pill">



                                    @php
                                        $colors = ['#f1604f', '#29b770', '#f40000', '#f0d4c1', '#868686', '#00deb3', '#0f0f0f', '#d83b78', '#e3113d', '#fd3f1e'];
                                    @endphp

                                    @foreach ($data as $key => $value)
                                        @php
                                            $index = $key % count($colors);
                                            $color = $colors[$index];
                                        @endphp

                                        <div class="progress-bar" data-toggle="tooltip" data-placement="top"
                                            title="{{ $value->name }}: {{ $total > 0 ?round($value->properties_count / $total): 0 }}% "
                                            data-for="{{ $value->name }}" role="progressbar"
                                            style="width: {{  $total > 0 ? round($value->properties_count / $total):0 }}%; background-color: {{ $color }}"
                                            aria-valuenow="{{  $total > 0 ? round($value->properties_count / $total):0 }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    @endforeach

                                </div>
                            </div>
                            <ul class="px-0" style="list-style-type: none;">

                                @foreach ($data as $key => $value)
                                    <li class="py-2">
                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                            <div class="d-flex justify-content-start align-items-center gap-2">
                                                <img src="{{ $value->thumbnail }}" alt="" width="20px">
                                                <strong>{{ $value->name }}:</strong>
                                            </div>
                                            <div>
                                                <span
                                                    class="badge badge-soft-dark">{{ $value->properties_count }}</span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

