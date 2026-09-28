@extends('admin.layouts.master')
@section('title')
    Spot Recordings
@endsection
@section('style')
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/skin/blue.monday/css/jplayer.blue.monday.css"> --}}
    {{-- <link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet" /> --}}
@endsection
@section('content')
    @php
        use Carbon\Carbon;
    @endphp

    <div class="card spot">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h4> {{ $user->full_name }} </h4>
                    <h6>Device ID:{{ $user->id }}</h6>
                </div>
                <hr>
                <div class="col-lg-12">
                    <div>
                        <div class="timeline" id="timeline">
                            @php
                                $ite = 2;
                            @endphp

                            @foreach ($data as $index => $items)
                                <div class="timeline-item  {{ $ite % 2 == 0 ? 'left' : 'right' }}">
                                    <i class="icon ri-map-pin-time-line"></i>
                                    <div class="date">{{ date('d-M-Y', strtotime($index)) }}</div>
                                    @foreach ($items as $item)
                                    <div class="borderbg">
                                        <div class="content m-1 content_box">
                                            <div class="row">

                                                <div class="col-md-9">
                                                    <h6 data-bs-toggle="modal" data-bs-target="#locationModal"
                                                        data-lat="{{ $item?->coordinate?->lat }}"
                                                        data-lng="{{ $item->coordinate?->lng }}"
                                                        data-radius="{{ $item?->coordinate?->radius }}"
                                                        data-zoom="{{ $item?->coordinate?->zoom }}"
                                                        data-stay_time="{{ $item?->coordinate?->stay_time }}"
                                                        data-title="{{ $item?->coordinate?->title }}" class="showMap">
                                                        {{ $item?->coordinate?->title }}</h6>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <small
                                                        class="">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                            <div class="row g-2 ">
                                                <div class="col-sm-12" data-source="{{ $item?->path }}">
                                                    <div class="d-flex border border-dashed p-2 rounded position-relative ">
                                                        <audio style="width: 100%;height:20px" src="{{ $item->path }}"
                                                            type="audio/3gpp" data-id="{{ $item?->id }}" controls>
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
                                    $ite = $ite + 1;
                                @endphp
                                {{-- @break  --}}
                            @endforeach


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('admin.traccar_recording.include._modal')
@endsection
@section('script')
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=Function.prototype">
    </script>



    <script>
        $(document).ready(function() {

            $('.showMap').click(function() {

                let lat = $(this).data('lat');
                let lng = $(this).data('lng');
                let radius = $(this).data('radius');
                let zoom = $(this).data('zoom');
                let stay_time = $(this).data('stay_time');
                let title = $(this).data('title');
                $('#locationModalLabel').html(title);
                initMap(lat, lng, title, zoom);
            })
            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                    fetchData();
                }
            });

        });


        fetchData();

        function initMap(lat, long, title, zoom) {
            const myLatLng = {
                lat: lat,
                lng: long
            };
            const map = new google.maps.Map(document.getElementById("google-map"), {
                zoom: zoom,
                center: myLatLng,
            });

            new google.maps.Marker({
                position: myLatLng,
                map,
                title: title,
            });

            $("#mapModal").modal("show");
        }

        // make function that execute when i reach last of the page



        function fetchData() {
            let date = $('.timeline-item').last().find('.date').html();
            let user_id = "{{ $user->id }}";
            let last_id = $('.timeline-item').last().find('.content').last().find('audio').data('id');
            $.ajax({
                url: "{{ route('admin.traccar.recording.show', $user->id) }}",
                type: "GET",
                data: {
                    date: date,
                    user_id: user_id,
                    last_id: last_id
                },
                success: function(response) {

                    $('#timeline').append(response);

                }
            })
        }


        $('#sidebarSecurityActive').addClass('active');
        $('#sidebarSecurity').addClass('collapse show');
        $('#sidebarSecurityRecoding').addClass('active');
    </script>

    @yield('pagejs')
@endsection
