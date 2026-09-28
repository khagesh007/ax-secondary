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

        .chart-container {
            width: 100%;
            overflow-x: auto;
            /* Enable horizontal scrolling */
            white-space: nowrap;
            /* Prevent line breaks in the chart */
        }

        .chart {
            width: 400px;
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
    <input type="hidden" name="group_id" id="group_id" value="{{ request()->group_id }}">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Group Reporting</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Locations</a></li>
                            <li class="breadcrumb-item active">Location Overview</li>
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
    @include('admin.reports.partials.groups._filter')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-11">

                            <div class="content">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 ">
                                        <img src="{{ $group->user->thumb_url }}" alt=""
                                            class="avatar-sm rounded-circle">
                                    </div>
                                    <div class="flex-grow-1  ms-3 my-auto">
                                        <h4 class="">{{ $group->name }} |<small
                                                class="text-muted fs-15 fw-normal">{{ $group->user->full_name }}</small>
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="avatar-md">
                                <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_169877580020231031221000.jpg" alt="" class="img-fluid rounded-circle">
                            </div> --}}

                        </div>
                        <div class="col-md-1">
                            <div class=" text-start" id="exportBTNs"></div>
                        </div>

                        @include('admin.reports.partials.groups._group_member', ['agents' => $agents])


                        {{--  <div class="col-md-3">
                          <select name="portal_id" id="portal_id" class="form-control select2 inputs">
                                <option value="">Select Portal</option>
                                @foreach ($portals as $portal)
                                    <option value="{{ $portal->id }}">{{ $portal->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}


                    </div>


                </div>
                <div class="card-body">
                    <div class="row" id="divLoader">
                        <div class="col-md-12 text-center">
                            <div class="spinner-grow text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div id="tableAppend"></div>
                </div>
            </div>
        </div>
    </div>
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

    @section('global_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection
    <script>
        var duration = window.location.href.indexOf('duration') > -1 ? window.location.href.split('duration=')[1] : 'all';



        $(document).ready(function() {
            areaWiseListing()
            $('.dateFilter').click(function() {
                var duration = $(this).val();
                var url = new URL(window.location.href);
                url.searchParams.set('duration', duration);

                window.location.href = url.toString();
            });
            $('#datefilters').daterangepicker({
            maxDate: moment(),
            locale: {
                format: 'DD/MM/YYYY'
            }

        });
        })
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

        $(document).on("click", ".listingStatus", function() {
            $('.listingStatus').removeClass('active');
            $(this).addClass('active');
            areaWiseListing();
        })

        $(document).on("change", "#portal_id", function() {
            areaWiseListing();
        })


        function areaWiseListing() {
            let group_id = $('#group_id').val();
            let status = $('.listingStatus.active').data('status');
            let is_live = $('.listingStatus.active').data('is_live');
            let portal = $('#portal_id').val();
            if (status == undefined) {
                status = null;
            }
            if (is_live == undefined) {
                is_live = null;
            }

            $('#tableAppend').html('');
            $('#divLoader').removeClass('d-none')
            $.ajax({
                url: "{{ route('admin.reports.locationOverviewFetch') }}",
                type: "POST",
                dataType: 'html',
                data: {

                    'group_id': group_id,
                    'status': status,
                    'is_live': is_live,
                    'portal': portal,
                    'duration': duration,
                },
                success: function(result) {
                    $('#divLoader').addClass('d-none')
                    $('#tableAppend').html(result);

                    $('.showCount').each(function() {
                        let sum = 0;
                        $('#buttons-datatables').find('td:nth-child(' + ($(this).index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(sum);
                    });

                    // at .showPercentage show percentage of each column as total is in last column
                    $('.showPercentage').each(function() {
                        let sum = 0;
                        $('#buttons-datatables').find('td:nth-child(' + ($(this).index() + 1) + ')')
                            .each(function() {
                                sum += parseInt($(this).text()) || 0;
                            });
                        $(this).text(((sum / $('.showCount').last().text()) * 100).toFixed(2) + '%');
                    });

                    const table = new DataTable('#buttons-datatables', {
                        dom: 'Bfrtip',
                        buttons: [
                            'csv',
                        ],
                        fixedColumns: {
                            left: 1,
                            right: 1
                        },
                        "scrollY": "500px",
                        "scrollX": true,
                        "scrollCollapse": false,
                        "paging": false,
                        searching: true

                    });

                    $('#exportBTNs').html('');
                    table.buttons().container().appendTo('#exportBTNs');

                    table.page.len(1000).draw();
                    $('.dataTables_length').hide();
                    $('.dataTables_info').hide();

                    table.on('mouseenter', 'td', function() {
                        let colIdx = table.cell(this).index().column;
                        table
                            .cells()
                            .nodes()
                            .each((el) => el.classList.remove('highlight'));

                        table
                            .column(colIdx)
                            .nodes()
                            .each((el) => el.classList.add('highlight'));
                    });
                    $('#portal_id').select2({
                        placeholder: 'Select Portal',
                        allowClear: true,
                        focusOnOpen: false,
                    });
                }
            });
        }
        $("#sidebarReports").addClass('collapse show');
        $('#sidebarGroupReports').addClass('active');
        $("#reportsActive").addClass('active');
    </script>

    @yield('pageDT')
    @stack('in_page_scripts')
@endsection
