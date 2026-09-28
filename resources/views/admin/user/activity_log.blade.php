@extends('admin.layouts.master')
@section('title')
    User Management - Users
@endsection

@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />




    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <style type="text/css">
        div.dataTables_wrapper div.dataTables_filter input {
            border-color: var(--vz-input-border);
            background-color: var(--vz-input-bg);
            color: var(--vz-body-color);
            line-height: 1.5;
            padding: 0.5rem 0.9rem 0.5rem 1.025rem;
            border-radius: 0.25rem;
            font-size: .8125rem;
        }

        .scrollable-div {
            max-height: 229px;
            padding: 10px;
            resize: both;
            overflow: auto;
            /* width: 100%; */
        }

        #ppImageDiv img {
            max-width: 50px;
        }

        #map {
            height: 400px;
        }

        .pac-container {
            z-index: 10000 !important;
        }

        .btn-sm-loc {
            border-radius: .2rem !important;
            font-size: .6rem !important;
            padding: 5px !important;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">User Activity</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">User Activity</a></li>
                            <li class="breadcrumb-item active">User Activity</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-0  me-2">Recent Activity</h4>

                </div>
                <div class="card-body">

                    <div class="tab-content text-muted">
                        <div class="tab-pane active" id="today" role="tabpanel">
                            @foreach ($activities as $key => $act)
                                {{ $key }}
                                @foreach ($act as $y => $items)
                                    @php
                                        $class = $y;
                                        $lastWord = substr($class, strrpos($class, '\\') + 1);
                                    @endphp

                                    <div class="profile-timeline">
                                        <div class="accordion accordion-flush" id="todayExample">
                                            <div class="accordion-item border-0">
                                                @foreach ($items as $item)
                                                    <div class="accordion-header" id="headingOne_{{ $item->id }}">
                                                        <a class="accordion-button p-2 shadow-none"
                                                            data-bs-toggle="collapse"
                                                            href="#collapseOne_{{ $item->id }}" aria-expanded="true">
                                                            <div class="d-flex">
                                                                <div class="flex-shrink-0">
                                                                    <img src="{{ getAvatar($item->causer->id) }}"
                                                                        alt="{{ $item->causer->id }}"
                                                                        class="avatar-xs rounded-circle shadow" />
                                                                </div>
                                                                <div class="flex-grow-1 ms-3">
                                                                    <h6 class="fs-14 mb-1">
                                                                        {{ $item->causer->name }} made some changes in
                                                                        {{ $lastWord }}
                                                                    </h6>
                                                                    <small class="text-muted">{{ $item->description }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <div id="collapseOne_{{ $item->id }}"
                                                        class="accordion-collapse collapse"
                                                        aria-labelledby="headingOne_{{ $item->id }}"
                                                        data-bs-parent="#accordionExample">
                                                        <div class="accordion-body ms-2 ps-5">

                                                            @php
                                                                $jsonData = json_decode($item->properties , true);
                                                            @endphp

                                                            @if (isset($jsonData['attributes']))
                                                                Attributes:
                                                                @foreach ($jsonData['attributes'] as $key => $value)
                                                                @if (is_array($value))
                                                                <strong>{{ $key }}:</strong>
                                                                <ul>
                                                                    @foreach ($value as $subKey => $subValue)
                                                                        <li>{{ formatText($subKey) }}: {{ $subValue ?? '' }} </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <li><strong>{{ formatText($key) }}</strong>: {{ $value ?? '' }}</li>
                                                            @endif
                                                                @endforeach
                                                            @endif

                                                            @if (isset($jsonData['old']))

                                                                Old:
                                                                @foreach ($jsonData['old'] as $key => $value)

                                                                    @if (is_array($value))
                                                                        <strong>{{ $key }}:</strong>
                                                                        <ul>
                                                                            @foreach ($value as $subKey => $subValue)
                                                                                <li>{{ formatText($subKey) }}: {{ $subValue ?? '' }} </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        <li><strong>{{ formatText($key) }}</strong>: {{ $value ?? '' }}</li>
                                                                    @endif
                                                                @endforeach
                                                            @endif

                                                            <br>

                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <!--end accordion-->
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>



                    {{-- @foreach ($activities as $key => $act)
                        {{$key}}

                        @foreach ($act as $y => $items)
                            @php
                                $class = $y;
                                $lastWord = substr($class, strrpos($class, '\\') + 1);
                            @endphp

                            {{ $lastWord }}


                            @foreach ($items as $item)
                                {{ $item->log_name }}
                                <br>
                                {{ $item->description }}
                                <br>
                                {{ $item->subject_type }} <br>
                                {{ $item->event }} <br>
                                {{ $item->subject_id }} <br>
                                {{ $item->causer_type }} <br>
                                {{ $item->causer_id }} <br>
                                {{ $item->properties }} <br>
                            @endforeach
                            <hr>
                        @endforeach
                    @endforeach --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    {{-- <script src=" "></script> --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="{{ asset('assets/js/traccar.js') }}"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAcZNvJFINzsKGTuGyABZ6WcVJWriqZbqk&callback=initTraccarMap&v=weekly&libraries=places"
        defer></script>
    <script>
        $('#userActive').addClass('active');
    </script>
@endsection
