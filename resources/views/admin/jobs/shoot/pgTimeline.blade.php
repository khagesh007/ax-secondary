<!-- photographer timeline -->

<div class=" tasks-board mt-2 " id="photographerEventCard">

    <span class="position-absolute topbar-badge fs-14 translate-top badge rounded-pill ">

         <button type="button"
                 class="btn btn-default btn-sm bg-white"
                 id="btn_event_card_extend"
                 title="Full Screen"
                 aria-label="Full Screen"><i
                 class="note-icon-arrows-alt"></i></button>
    </span>
    @foreach($photographers as $photographer)
    <div class="tasks-list" id="photographerList{{$photographer->id}}">
        <div class="card">
            <a class="card-body photographer {{randomBadgeColors()}} d-flex align-items-center" data-bs-toggle="collapse" href="#leadDiscovered{{$photographer->id}}" role="button"
               aria-expanded="false" aria-controls="leadDiscovered">
                <div class="flex-shrink-0">
                    <img src="{{$photographer->thumb_url}}" alt=""
                         class="avatar-xs rounded shadow">
                </div>
                <div class="flex-grow-1 ms-2">
                    <h6 class="fs-12 mb-1">{{$photographer->full_name}}</h6>

                </div>
            </a>
        </div>
        <!--end card-->
        <div  id="leadDiscovered{{$photographer->id}}">
            @foreach($timeSlots as $time)

                @php
                    $collection = collect($photographer?->events ?? []);
                        $targetHour = date('H',strtotime($time)); // Example target hour

        // Filter the collection based on the condition
        $filtered = $collection->filter(function ($item) use ($targetHour) {
            $currentTime = \Carbon\Carbon::parse($item->start_time)->hour;
            return $currentTime == $targetHour;
        });
        if(count($filtered) == 0){
            @endphp
           <div class="card schedules mb-1" data-time="{{date('Y-m-d',strtotime($date))}} {{date('H:i',strtotime($time))}}">
                    <div class="card-body">
                        <a class="d-flex align-items-center" data-bs-toggle="collapse" href="#leadDiscovered" role="button"
                           aria-expanded="false" aria-controls="leadDiscovered">

                            <div class="flex-grow-1 ms-1">
                                <h6 class="fs-12 mb-1" style="color: #939393;"></h6>
                                <p class="text-muted fs-10 mb-0"> {{date('d F y',strtotime($date))}} {{date('h:i A',strtotime($time))}}</p>
                            </div>
                        </a>
                    </div>

                </div>
            @php
        }
        // Output the filtered results
        $filtered->each(function ($item) {
        @endphp

                <div class="card schedules booked mb-1" onclick="viewEvent('{{$item?->id}}')">
                    <div class="card-body">
                        <a class="d-flex align-items-center" data-bs-toggle="collapse" href="#leadDiscovered{{$item->id}}1" role="button"
                           aria-expanded="false" aria-controls="leadDiscovered{{$item->id}}1">

                            <div class="flex-grow-1 ms-1">
                                <p class="fs-10 mb-1" style="color: #6d6666;font-weight: 400;">{{ Str::limit($item?->title, 45) }}...</p>
                                <p class="text-muted fs-10 mb-0">{{date('d F y h:i A',strtotime($item?->start_time))}} - {{date('h:i A',strtotime($item?->end_time))}}</p>
                            </div>
                        </a>
                    </div>

                </div>

            @php

        });

                @endphp
            @endforeach

            <!--end card-->

        </div>
    </div>
    @endforeach
    <!--end col-->


</div>

<div>

</div>

<!-- photographer timeline end-->
