@extends('admin.layouts.master')
@section('title')
    Group Report
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
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
            top: 80px;
            /* Adjust this value as needed */
            z-index: 99999;
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
        <div class="col-lg-12">
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
    <form action="" id="mainform">
        <div class="row" id="onscrollhide">
            <div class="col-md-2">
            </div>
            <div class="col-md-10 ">
                <div class="row">
                    @foreach ($data_filtered as $group)
                        <div class="col-xl-4">
                            <div class="card d-none">
                                <div class="card-body">
                                    <select name="" class="form-control" id="">
                                        <option value=""> S1</option>
                                        <option value=""> S2</option>
                                        <option value=""> S3</option>

                                    </select>
                                </div>
                            </div>

                            <input type="text" name="group_id[]" value="{{ $group->id }}" class="formInput">

                            <div class="card"
                                style="border:3px {{ request()->child ? 'dotted' : 'solid' }} {{ $group->color }}">

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
        <div class="col-md-2">
        </div>
        <div class="col-md-10 ">
            <div class="row">
                @foreach ($data_filtered as $group)
                    <div class="col-xl-4">
                        <div class="card p-1"
                            style="border:3px {{ request()->child ? 'dotted' : 'solid' }} {{ $group->color }}">

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



    <hr>

    @include('admin.reports.partials.groups._overall_stats')
    @include('admin.reports.partials.groups._portal_stats')
    @include('admin.reports.partials.groups._listing_stats')
    @include('admin.reports.partials.groups._events_stats')
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/echarts/echarts.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="{{ URL::asset('/assets/libs/chart.js/chart.js.min.js') }}"></script>
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/swiper/swiper.min.js') }}"></script>
    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>


    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>






    <script>
        $(document).ready(function() {
            $(document).ready(function() {
                var formData = {};
                $(':input[name="group_id[]"]').each(function() {
                    formData[$(this).attr('name')] = $(this).val();
                });

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.reports.processData') }}",
                    data: formData,
                    success: function(response) {

                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle errors here
                        console.error(error);
                    }
                });
            });

        });
    </script>




    @yield('pageDT')
    @stack('in_page_scripts')
@endsection
