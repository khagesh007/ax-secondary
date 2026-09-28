@extends('admin.layouts.master')
@section('title')
    Group Report
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        .flex-container {
            display: flex;
            flex-wrap: wrap;
        }

        .flex-item {
            flex: 1;
        }

        .group-name {
            font-size: 14px;
            font-weight: 600;
        }

        #pf_rank_meter,
        #bayut_rank_meter,
        #dubbizle_rank_meter {
            max-width: 600px;
            max-height: 400px;
            /* Add other styles as needed */
        }
    </style>
    <style>
        .sticky-top-with-margin {
            top: 40px;
            z-index: 99999;
        }

        .sticky-top-with-margin-bottom {
            top: 95px;
            z-index: 9;
            background-color: #fff !important;
        }

        .sticky-top-duration {
            top: 10px;
            z-index: 99999;
        }

        .sticky-top-with-margin-bottom .card-body {
            padding: 6px !important;
        }

        .chart-container {
            width: 100%;
            overflow-x: auto;
            /* Enable horizontal scrolling */
            white-space: nowrap;
            /* Prevent line breaks in the chart */
        }

        .chart {
            /* width: 400px; */
            overflow: scroll;
            /* Set a fixed width for the chart container */
        }
    </style>

    <style>
        .morris-chart {
            font-family: Arial, sans-serif;
        }

        .morris-chart .morris-label {
            font-size: 14px;
            fill: #333333;
        }

        .selection .select2-selection--single {
            width: 300px !important;
        }
    </style>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
@endsection
@section('content')
    <div class="row d-none">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Group Reporting</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reporting</a></li>
                            <li class="breadcrumb-item active">Group Reporting</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-lg-12 d-none">
            <div class="card">
                <div class="row">
                    <div class="col-md-4 m-3 offset-md-3">
                        <div class="row mt-2">
                            <div class="col-lg-12">
                                <input type="date" class="form-control search-inputs searchInput"
                                    data-provider="flatpickr" data-date-format="Y-m-d" id="search_updated_at"
                                    name="search_updated_at" data-range-date="true" placeholder="Duration">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @include('admin.reports.partials.groups._filter')

    <form action="" id="mainform">
        <div class="row" id="onscrollhide">
            <div class="col-md-2">
            </div>
            <div class="col-md-12 ">
                {{-- <div class="flex-container"> --}}
                <div class="row row-cols-md-3 row-cols-1">

                    @foreach ($data_filtered as $group)
                        {{-- <div class="flex-item p-2"> --}}
                        <div class="col col-lg border-end p-2">
                            <input type="hidden" name="group_id[]" value="{{ $group->id }}" class="formInput">
                            <div class="card"
                                style="border:2px {{ request()->child ? 'dotted' : 'solid' }} {{ $group->color }}">
                                <div class="card-body p-4 text-center">
                                    <h1 class=" mb-1">{{ $group->name }}</h1>
                                    <hr>
                                    <div class="mx-auto avatar-md mb-3">
                                        <img src="{{ $group->user->thumb_url }}" alt=""
                                            class="img-fluid rounded-circle">
                                    </div>
                                    <h6 class=" mb-1">{{ $group->user->full_name }} ({{ childcount($group->user->id) }})
                                    </h6>
                                </div>
                                <div class="card-footer">
                                    <div class="avatar-group">
                                        <a href="#" class="avatar-group-item " style="visibility:hidden"
                                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                            title=""
                                            data-bs-original-title="{{ $group->name }} | {{ $group->user->full_name }}">
                                            <img src="{{ $group->user->thumb_url }}" alt=""
                                                class="rounded-circle avatar-xs">
                                        </a>
                                        @foreach ($group->children as $child)
                                            <a href="{{ route('admin.reports.group') }}?child={{ $group->id }}"
                                                class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                data-bs-placement="top" title=""
                                                data-bs-original-title="{{ $child->name }} | {{ $child->user->full_name }}">
                                                <img src="{{ $child->user->thumb_url }}" alt=""
                                                    class="rounded-circle avatar-xs">
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>

    <div class="row d-nne mt-2 sticky-top-with-margin position-sticky">
        <div class="col-md-12 ">
            <div class="row row-cols-md-3 row-cols-1">
                @foreach ($data_filtered as $group)
                    <div class="col col-lg  p-2">
                        <div class="card p-1"
                            style="border:2px {{ request()->child ? 'dotted' : 'solid' }} {{ $group->color }}">

                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-group">
                                        <div class="avatar-group-item">
                                            <div class="avatar-xs">
                                                <img src="{{ $group->user->thumb_url }}" alt=""
                                                    class="rounded-circle img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="fs-12 text-muted mb-0"> {{ $group->name }} |
                                            {{ $group->user->full_name }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>



    <div id="overalldata">

    </div>
    @include('admin.reports.partials.groups._loading')
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/echarts/echarts.min.js') }}"></script>

    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>


    <script src="{{ URL::asset('/assets/libs/chart.js/chart.js.min.js') }}"></script>
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#datefilters').daterangepicker({
                maxDate: moment(),
                locale: {
                    format: 'DD/MM/YYYY'
                }

            });
            $('.dateFilter').click(function() {
                var duration = $(this).val();
                var url = new URL(window.location.href);
                // remove all params
                url.searchParams.delete('duration');
                url.searchParams.delete('from');
                url.searchParams.delete('to');
                url.searchParams.set('duration', duration);

                window.location.href = url.toString();
            })

        });

        $(document).on('click', '.applyBtn', function() {
            var dates = $('#datefilters').val();
            dates = dates.split(' - ');
            dates[0] = dates[0].replace(/\//g, '-');
            dates[1] = dates[1].replace(/\//g, '-');
            from_date = dates[0];
            to_date = dates[1];
            var url = new URL(window.location.href);
            url.searchParams.set('duration', 'custom');
            url.searchParams.set('from', from_date);
            url.searchParams.set('to', to_date);
            window.location.href = url.toString();
        });


    </script>

@section('global_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection







@yield('pageDT')
@stack('in_page_scripts')
@endsection
