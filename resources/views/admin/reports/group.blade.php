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


    {{-- @include('admin.reports.partials.groups._overall_stats') --}}


    {{-- @include('admin.reports.partials.groups._portal_stats') --}}
    {{-- @include('admin.reports.partials.groups._listing_stats') --}}
    {{-- @include('admin.reports.partials.groups._events_stats') --}}
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

@section('global_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection


<script>
    var duration = window.location.href.indexOf('duration') > -1 ? window.location.href.split('duration=')[1] : 'all';
    var groupIds = [];
    $('input[name="group_id[]"]').each(function() {
        groupIds.push($(this).val());
    });

    $(document).ready(function() {
        $('#datefilters').daterangepicker({
            maxDate: moment(),
            locale: {
                format: 'DD/MM/YYYY'
            }

        });

        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'stats',
                'duration': duration

            },
            success: function(response) {
                $('#overalldata').html(response);
                userMetaData();
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(error);
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




    $(document).on('click', '.donutGraph', function() {
        let portal = $(this).data('portal');
        if (portal == 'PF') {
            pflevelChart()
        }
        if (portal == 'Bayut') {
            bayutlevelChart()
        }
        if (portal == 'Dubizzle') {
            dubizzlelevelChart()
        }
    });


    $(document).on('click', '.guageGraph', function() {
        let portal = $(this).data('portal');
        if (portal == 'PF') {
            pfRankMeterChart()
        }
        if (portal == 'Bayut') {
            bayutRankMeterChart()
        }
        if (portal == 'Dubizzle') {
            dubizzleRankMeterChart()
        }
    });



    function pflevelChart() {
        $('.propertyLevel').each(function() {
            var group_id = $(this).data('group_id');
            let element = 'listingPortalLevel_' + group_id;
            let total = $(this).data('property_finder_total');
            let standard_pf = $(this).data('standard_pf');
            let featured_pf = $(this).data('featured_pf');
            let premium_pf = $(this).data('premium_pf');

            let value = [$(this).data('standard_pf'), $(this).data('featured_pf'), $(this).data('premium_pf')]
            let label = ['Standard', 'Featured', 'Premium']
            portalLevelChart(element, value, label, total, 'PF');

            let htmldata = `<span><b>Premium:</b> ${premium_pf} </span><br>
                <span><b>Featured:</b> ${featured_pf} </span><br>
                <span><b>Standard:</b> ${standard_pf} </span><br>

                                `
            $('#showdata_' + group_id).html('');
            $('#showdata_' + group_id).html(htmldata);

        });
    }

    function bayutlevelChart() {
        $('.propertyLevel').each(function() {
            var group_id = $(this).data('group_id');
            let element = 'listingPortalLevel_' + group_id;
            let total = $(this).data('bayut_total');
            let hot_bayut = $(this).data('hot_bayut');
            let standard_bauyt = $(this).data('standard_bauyt');
            let signature_bauyt = $(this).data('signature_bauyt');

            let value = [$(this).data('hot_bayut'), $(this).data('standard_bauyt'), $(this).data(
                'signature_bauyt')]
            let label = ['Hot', 'Standard', 'Signature']
            portalLevelChart(element, value, label, total, 'Bayut');

            let htmldata = `<span><b>Signature:</b> ${signature_bauyt} </span><br>
                                 <span><b>Hot:</b> ${hot_bayut} </span><br>
                                <span><b>Standard:</b> ${standard_bauyt} </span><br>
                                `
            $('#showdata_' + group_id).html('');
            $('#showdata_' + group_id).html(htmldata);
        });
    }

    function dubizzlelevelChart() {
        $('.propertyLevel').each(function() {
            var group_id = $(this).data('group_id');
            let element = 'listingPortalLevel_' + group_id;
            let total = $(this).data('dubizzle_total');
            let standard_dubizzle = $(this).data('standard_dubizzle');
            let featured_dubizzle = $(this).data('featured_dubizzle');

            let value = [$(this).data('standard_dubizzle'), $(this).data('featured_dubizzle')]
            let label = ['Standard', 'Featured']
            portalLevelChart(element, value, label, total, 'Dubizzle');

            let htmldata = ` <span><b>Featured:</b> ${featured_dubizzle} </span><br>
                <span><b>Standard:</b> ${standard_dubizzle} </span><br>`

            $('#showdata_' + group_id).html('');
            $('#showdata_' + group_id).html(htmldata);

        });
    }



    function pfRankMeterChart() {
        $('.propertyQuality').each(function() {
            var group_id = $(this).data('group_id');
            let element = 'listingPortalQuality_' + group_id;
            let value = $(this).data('pf_avg_rank');
            rankMeterChart(element, value, 'Property Finder', 'ef5d4f');
        });
    }

    function bayutRankMeterChart() {
        $('.propertyQuality').each(function() {
            var group_id = $(this).data('group_id');
            let element = 'listingPortalQuality_' + group_id;
            let value = $(this).data('bayut_avg_rank');
            rankMeterChart(element, value, 'Bayut', '29b56f');
        });
    }

    function dubizzleRankMeterChart() {
        $('.propertyQuality').each(function() {
            var group_id = $(this).data('group_id');
            let element = 'listingPortalQuality_' + group_id;
            let value = $(this).data('dubizzle_avg_rank');

            rankMeterChart(element, value, 'Dubizzle', 'f30300');
        });
    }


    function userMetaData() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'userMetaData',
                'duration': duration
            },
            success: function(response) {
                $('#overalldata').append(response);
                $(document).ready(function() {
                    $('.userMetaStatus').each(function() {
                        let group_id = $(this).data('group_id');
                        var chartClass = 'agentsMetaReport_' + group_id;
                        let warnings = $(this).data('warnings');
                        let suspensions = $(this).data('suspensions');
                        getUserMetaData(chartClass, warnings, suspensions);
                    });

                });
                propertyStats()
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function propertyStats() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'propertyStats',
                'duration': duration
            },
            success: function(response) {
                // $('#loading-row').addClass('d-none');
                $('#overalldata').append(response);

                $(document).ready(function() {
                    $('.propertyStatus').each(function() {
                        var data = $(this).data('dataset');
                        var chartClass = 'agentsOfficeReport_' + data.group_id;
                        getGroupPropertyStatus(chartClass, data);
                        // getPolarAreaChart(chartClass, data);
                    });

                });

                propertyQuality()
            },
            error: function(xhr, status, error) {

                console.error(error);
            }
        });
    }

    function propertyQuality() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'propertyQuality',
                'duration': duration
            },
            success: function(response) {
                $('#overalldata').append(response);

                $(document).ready(function() {
                    $('.propertyQuality').each(function() {
                        var group_id = $(this).data('group_id');
                        let element = 'listingPortalQuality_' + group_id;
                        let value = $(this).data('pf_avg_rank');
                        rankMeterChart(element, value, 'Property Finder', 'ef5d4f');
                    });

                });

                propertyLevels()
            },
            error: function(xhr, status, error) {

                console.error(error);
            }
        });
    }




    function propertyLevels() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'propertyLevels',
                'duration': duration
            },
            success: function(response) {
                $('#overalldata').append(response);
                $('.propertyLevel').each(function() {
                    var group_id = $(this).data('group_id');
                    let element = 'listingPortalLevel_' + group_id;
                    let total = $(this).data('property_finder_total');
                    let value = [$(this).data('standard_pf'), $(this).data('featured_pf'), $(this)
                        .data('premium_pf')
                    ]
                    let label = ['Standard', 'Featured', 'Premium']
                    pflevelChart();

                });


                leadsStats()
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }


    function portalLevelChart(element, value, label, total, portal) {
        $("#" + element).empty();
        $("#" + element).html('');

        var options = {
            series: value,
            chart: {
                type: 'pie',
            },
            plotOptions: {
                pie: {
                    startAngle: -90,
                    endAngle: 270
                }
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: 'gradient',
            },
            legend: {
                show: false // Hide the legend
            },
            title: {
                text: portal + ' - Total: ' + total
            },
            labels: label,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 120
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#" + element), options);
        chart.render();
    }

    function leadsStats() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'LeadStatus',
                'duration': duration
            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.leadWiseStatus').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'leadsWise_' + data.group_id;
                    getLeadsStatusChart(chartClass, data.statusNames, data.leadCounts);
                });
                leadsSources()
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function leadsSources() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'leadsSources',
                'duration': duration
            },
            success: function(response) {
                $('#overalldata').append(response);
                $('.leadWiseSource').each(function() {
                    var data = $(this).data('dataset');

                    var chartClass = 'leadsSourceWise_' + data.group_id;
                    getLeadsSourceChart(chartClass, data.sourceNames, data.leadCounts);
                });
                ListingLeadStatus()
            },
            error: function(xhr, status, error) {

                console.error(error);
            }
        });
    }


    function ListingLeadStatus() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'ListingLeadStatus',
                'duration': duration
            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.listingLeadWiseStatus').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'listingLeadsWise_' + data.group_id;
                    getListingLeadsStatusChart(chartClass, data.statusNames, data
                        .leadCounts);
                });
                ListingLeadSource();

            },
            error: function(xhr, status, error) {

                console.error(error);
            }
        });
    }

    function ListingLeadSource() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'ListingLeadSource',
                'duration': duration
            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.listingLeadWiseSource').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'listingLeadsSourceWise_' + data.group_id;
                    getListingLeadsStatusChart(chartClass, data.statusNames, data
                        .leadCounts);
                });
                areaWiseStats();

            },
            error: function(xhr, status, error) {

                console.error(error);
            }
        });
    }




    function areaWiseStats() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'locationStats',
                'duration': duration
            },
            success: function(response) {

                $('#overalldata').append(response);
                $('.areaWiseStatus').each(function() {


                    var data = $(this).data('dataset');

                    var chartClass = 'locationWise_' + data.group_id;
                    getAreaWiseStatus(chartClass, data.locationNames, data
                        .propertyCounts);
                });
                eventssStats();
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function eventssStats() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'eventsStats',
                'duration': duration
            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.eventWiseStatus').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'eventsWise_' + data.group_id;

                    getEventsWiseStatus(chartClass, data.eventNames, data.eventCounts);

                });
                portalsStats()


            },
            error: function(xhr, status, error) {

                console.error(error);
            }
        });
    }

    function portalsStats() {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reports.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'portalStats',
                'duration': duration
            },
            success: function(response) {

                $('#overalldata').append(response);
                $('#loading-row').addClass('d-none');
            },
            error: function(xhr, status, error) {

                console.error(error);
            }
        });
    }





    function _getGroupPropertyStatus(chartClass, data) {
        var colors = ['#45cb85', '#3478f1', '#f06547', '#7f6def']; // Define colors for each bar
        var seriesData = [data.published, data.pocket, data.unpublished, data.draft];

        var labels = seriesData.map(function(value, index) {
            return ['Published', 'Pocket', 'Unpublished', 'Draft'][index] + ': ' + value;
        });


        var series = seriesData.map(function(value, index) {
            return {
                data: [value],
                name: ['Published', 'Pocket', 'Unpublished', 'Draft'][index],
                color: colors[index]
            };
        });
        var options = {
            series: series,
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 1,
                    horizontal: false,
                    barWidth: '7%'
                }
            },
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: labels,
            },
            colors: colors
        };
        var chart = new ApexCharts(document.querySelector("." + chartClass), options);
        chart.render();
    }


    function getGroupPropertyStatus(chartClass, data) {
        var colors = ['#45cb85', '#3478f1', '#f06547', '#7f6def']; // Define colors for each bar
        var seriesData = [data.published, data.pocket, data.unpublished, data.draft];

        var series = seriesData.map(function(value, index) {
            return {
                data: [value],
                name: ['Published', 'Pocket', 'Unpublished', 'Draft'][index] + ': ' +
                    value, // Updated label format
                color: colors[index]
            };
        });

        var options = {
            series: series,
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    horizontal: true,
                    barWidth: '7%'
                }
            },
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: ['Published', 'Pocket', 'Unpublished', 'Draft'],
            },
            colors: colors,
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val; // Customize the tooltip format
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("." + chartClass), options);
        chart.render();
    }

    function getUserMetaData(chartClass, warnings, suspensions) {

        var colors = ['#ffbe0c', '#f06547']; // Define colors for each bar
        var categories = ['Warnings', 'Suspensions'];
        var seriesData = [warnings, suspensions];

        var series = [{
            name: 'Warnings',
            data: [warnings],
            color: colors[0]
        }, {
            name: 'Suspensions',
            data: [suspensions],
            color: colors[1]
        }];

        var options = {
            series: series,
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: !1
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    horizontal: true,
                    barWidth: '7%'
                }
            },
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: ['Warnings', 'Suspensions'],
            },
            legend: {
                show: !1
            },
            grid: {
                show: !1
            },

            colors: colors,
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val; // Customize the tooltip format
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("." + chartClass), options);
        chart.render();
    }

    
    function getLeadsSourceChart(chartClass, names, counts) {
        var colors = ['#45cb85', '#3478f1', '#f06547', '#7f6def'];
        var seriesData = counts;

        var options = {
            series: seriesData,
            chart: {
                type: 'pie',
                height: 350
            },

            labels: names,
            colors: colors
        };

        var chart = new ApexCharts(document.querySelector("." + chartClass), options);
        chart.render();
    }




    function getLeadsStatusChart(chartClass, names, counts) {
        var colors = ['#45cb85', '#3478f1', '#f06547', '#7f6def'];
        var seriesData = counts;

        var options = {
            series: seriesData,
            chart: {
                type: 'polarArea',
                height: 350
            },

            labels: names,
            colors: colors
        };

        var chart = new ApexCharts(document.querySelector("." + chartClass), options);
        chart.render();
    }

    function getListingLeadsStatusChart(chartClass, names, counts) {


        var colors = ['#45cb85', '#3478f1', '#f06547', '#7f6def'];
        var seriesData = counts;

        var options = {
            series: seriesData,
            chart: {
                type: 'polarArea',
                height: 350
            },

            labels: names,
            colors: colors
        };

        var chart = new ApexCharts(document.querySelector("." + chartClass), options);
        chart.render();
    }



    function getAreaWiseStatus(chartClass, locations, properties) {
        var colors = ['#f1604f', '#29b770', '#f40000', '#f0d4c1', '#868686', '#00deb3', '#0f0f0f', '#d83b78', '#e3113d',
            '#fd3f1e'
        ]
        var options = {
            series: [{
                data: properties
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 1,
                    horizontal: false,
                    barWidth: '10%'
                }
            },
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: locations,
            },

        };
        var chart = new ApexCharts(document.querySelector("." + chartClass), options);
        chart.render();
    }
    const customColors = ["#7E36AF", "#D9534F", "#4CAF50", "#FF9800", "#2196F3", "#E91E63"];

    function getEventsWiseStatus(chartClass, names, counts) {
        var colors = ['#f1604f', '#29b770', '#f40000', '#f0d4c1', '#868686', '#00deb3', '#0f0f0f', '#d83b78', '#e3113d',
            '#fd3f1e'
        ]
        var options = {
            series: [{
                data: counts
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 1,
                    horizontal: false,
                    barWidth: '10%'
                }
            },
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: names,
            },
            colors: [
                function({
                    value,
                    seriesIndex,
                    dataPointIndex,
                    w
                }) {
                    return customColors[dataPointIndex % customColors.length];
                }
            ]

        };
        var chart = new ApexCharts(document.querySelector("." + chartClass), options);
        chart.render();
    }

    function _getGroupEventsStatus(chartClass, data) {
        var options = {
            series: [{
                name: 'series1',
                data: [31, 40, 28, 51, 42, 109, 100]
            }, {
                name: 'series2',
                data: [11, 32, 45, 32, 34, 52, 41]
            }],
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z",
                    "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z",
                    "2018-09-19T06:30:00.000Z"
                ]
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("." + chartClass), options);
        chart.render();

    }


    function getGroupEventsStatus(chartClass, data, labels) {
        const series = [];

        for (const event in data[labelss[0]]) {
            if (data[labelss[0]].hasOwnProperty(event)) {
                const eventSeries = {
                    name: event,
                    data: labelss.map((label) => data[label][event] || 0)
                };
                series.push(eventSeries);
            }
        }

        series.reverse();

        var options = {
            series: series,
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'date',
                categories: labelss
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        const chart = new ApexCharts(document.querySelector('.' + chartClass), options);
        chart.render();
    }


    function rankMeterChart(element, value, label, color) {
        $("#" + element).empty();
        let col = color ? '#' + color : '#45cb85';
        var options = {
            series: [value],
            chart: {
                height: 200,
                type: 'radialBar',
                offsetY: -10
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    dataLabels: {
                        name: {
                            fontSize: '16px',
                            color: col,
                            offsetY: 120
                        },
                        value: {
                            offsetY: 76,
                            fontSize: '16px',
                            color: col,
                            formatter: function(val) {
                                return val + "%";
                            }
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                colors: [col],
                gradient: {
                    shade: 'dark',
                    shadeIntensity: 0.15,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,

                    stops: [0, 50, 65, 91]
                },
            },
            stroke: {
                dashArray: 4
            },
            labels: [label],
        };

        var chart = new ApexCharts(document.querySelector("#" + element), options);
        chart.render();
    }

    $("#sidebarReports").addClass('collapse show');
    $('#sidebarGroupReports').addClass('active');
    $("#reportsActive").addClass('active');
</script>




@yield('pageDT')
@stack('in_page_scripts')
@endsection
