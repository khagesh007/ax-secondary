<div data-simplebar id="scrolltobottom" class="scrolltobottom scrollable-notes px-3 mb-2 ">
    @if ($notes->count() > 0)
        @foreach ($notes as $index => $item)
            {{-- @dd($item->lead?->agent_id) --}}
            <div class="d-flex mb-0">
                <div class="flex-shrink-0">
                    <img src="{{ $item?->createdBy?->thumb_url ?? URL::asset('assets/images/users/multi-user.jpg') }}"
                        alt="" class="avatar-xs rounded-circle" />
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="fs-13"><a
                            href="javascript:void(0)">{{ $item->createdBy ? $item->createdBy?->full_name : 'System' }}</a>
                        <small
                            class="text-muted">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</small>
                    </h5>
                    @if (auth()->user()->hasRole('Agent'))
                        @if ($item->lead && $item->lead?->agent_id == null)
                            <p class="text-muted">{!! replaceNumbersWithAsterisks($item->note) !!}</p>
                        @else
                            <p class="text-muted">{!! $item->note !!} </p>
                        @endif
                    @else
                        <p class="text-muted">{!! $item->note !!} </p>
                    @endif

                </div>
            </div>
        @endforeach
    @else
        @include('admin.lead.include.partials.notes._empty_notes')
    @endif
</div>


<script>
    // var objDiv3 = document.getElementById("scrollable-notes");
    // objDiv3.scrollTop = objDiv3.scrollHeight;
</script>
