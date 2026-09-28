@php
    $ite2 = 2;
@endphp

@foreach ($data as $index => $items)
    <div class="timeline-item  {{ $ite2 % 2 == 0 ? 'right' : 'left' }}">
        <i class="icon ri-map-pin-time-line"></i>
        <div class="date">{{ date('d-M-Y', strtotime($index)) }}</div>
        @foreach ($items as $item)
        <div class="borderbg mb-3">
            <div class="content m-1 content_box">
                <div class="row">

                    <div class="col-md-9">
                        <h6 data-bs-toggle="modal" data-bs-target="#locationModal"
                            data-lat="{{ $item?->coordinate?->lat }}" data-lng="{{ $item->coordinate?->lng }}"
                            data-radius="{{ $item?->coordinate?->radius }}" data-zoom="{{ $item?->coordinate?->zoom }}"
                            data-stay_time="{{ $item?->coordinate?->stay_time }}"
                            data-title="{{ $item?->coordinate?->title }}" class="showMap">
                            {{ $item?->coordinate?->title }}</h6>
                    </div>
                    <div class="col-md-3 text-end">
                        <small class="">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</small>
                    </div>
                </div>
                <div class="row g-2 ">
                    <div class="col-sm-12" data-source="{{ $item?->path }}">
                        <div class="d-flex border border-dashed p-2 rounded position-relative ">
                            <audio style="width: 100%;height:20px" src="{{ $item->path }}" type="audio/3gpp"
                                data-id="{{ $item?->id }}" controls>
                            </audio>
                        </div>
                    </div>

                </div>

            </div>
        </div>
            {{-- @break --}}
        @endforeach
    </div>

    @php
        $ite2 = $ite2 + 1;
    @endphp
    {{-- @break  --}}
@endforeach
