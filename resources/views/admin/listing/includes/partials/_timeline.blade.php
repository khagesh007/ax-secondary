


<div class="profile-timeline">
    <div class="accordion accordion-flush scrollable-notes" id="accordionFlushExample">

        @if ($cyclesGrouped->count() > 0)
        @foreach ($cyclesGrouped as $refNo => $cycles)
              <span class="p-1 border badge badge-warning text-warning rounded-pill fs-15">{{$refNo}}</span>
            @foreach ($cycles as $index => $cycle)

                <div class="accordion-item border-0">
                    <div class="accordion-header" id="headingOne">
                        <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse"
                            href="#collapseOne_{{ $index }}" aria-expanded="true"
                            aria-controls="collapseOne_{{ $index }}">
                            <div class="d-flex align-items-center">
                                {!! statusUi($cycle->status) !!}
                                <div class="flex-grow-1 ms-3">
                                    <h6 class=" mb-0 fw-semibold">{{ $cycle->status }} <span class="fw-normal"
                                            title="{{ $cycle->created_at }}">
                                            {{ \Carbon\Carbon::parse($cycle->created_at)->diffForHumans() }}
                                        </span></h6>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div id="collapseOne_{{ $index }}"
                        class="accordion-collapse collapse {{ $loop->last ? 'show' : '' }}" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body ms-1 ps-5 pt-0">

                            @php
                                $previousCycle = $index > 0 ? $cycles[$index - 1] : null;
                            @endphp


                            @if ($index == 0)
                                <p class="mb-1"> {{ $cycle?->createdBy?->full_name??'SYSTEM' }} changed status to
                                    <b>{{ $cycle->status }}</b>.
                                <h6>
                                @else
                                    <p class="mb-1"> {{ $cycle?->createdBy?->full_name ??'SYSTEM' }} changed status to
                                        <b>{{ $cycle->status }}</b> from
                                        <b>{{ optional($previousCycle)->status ?? '' }}</b>.  <small>{{ date('d-M-y',strtotime($cycle->created_at)) }}</small>
                                    </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                @endforeach
                @else
            @include('admin.listing.includes.timeline._empty_timeline')
        @endif



    </div>

</div>





<script>
    var objDiv = document.getElementById("accordionFlushExample");
    objDiv.scrollTop = objDiv.scrollHeight;
</script>
