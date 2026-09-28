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
        .graph-container {
            overflow-x: auto;
            /* Horizontal scroll */
            overflow-y: hidden;
            /* Hide vertical scroll */
            max-width: 100%;
            /* Adjust the maximum width */
            height: 400px;
            /* Set the desired height */
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
    <div class="row">
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
                    <div class="flex-grow-2">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            <input type="checkbox" class="form-check-input" id="tab-switcher">
                            <label class="form-check" for="tab-switcher">Table View</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.reports.partials.groups._filter')

    <div class="row">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-justified" role="tablist">
                        @php
                            $counter = 1;
                        @endphp
                        @foreach ($data_filtered as $group)
                            <input type="hidden" name="group_id[]" value="{{ $group->id }}" class="formInput">

                            <li class="nav-item">
                                <a class="nav-link {{ $counter == 1 ? 'active' : '' }} select-tab" href="javascript:void(0)"
                                    data-group="group_{{ $group->id }}_div">
                                    {{ $group->name }}
                                </a>
                            </li>
                            @php
                                $counter++;
                            @endphp
                        @endforeach

                    </ul>
                    <!-- Tab panes -->

                </div><!-- end card-body -->
            </div><!-- end card -->

            <div class="tab-content  text-muted row" id="overalldata">

            </div>
        </div><!--end col-->
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






@yield('pageDT')
@stack('in_page_scripts')
<script>
    $(document).ready(function() {
        $('.select-tab').click(function() {
            let group = $(this).data('group');
            $('.hide-all').addClass('d-none');
            $('.' + group).removeClass('d-none');
        });
    });
</script>


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


        getMeetingEvent();
        $('#tab-switcher').change(function() {
            if ($(this).is(':checked')) {
                $('.tab-pane').removeClass('active');
                $('.nav-link').removeClass('active');

                $('.graph-nav-div').removeClass('active');
                $('.graph-nav').removeClass('active');

                $('.table-nav-div').addClass('active');
                $('.table-nav').addClass('active');

            } else {
                $('.table-nav-div').removeClass('active');
                $('.table-nav').removeClass('active');
                $('.graph-nav-div').addClass('active');
                $('.graph-nav').addClass('active');
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


    function fetchStats() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",

            data: {
                groupIds: groupIds,
                'type': 'stats',
                'duration': duration

            },
            success: function(response) {
                $('#overalldata').append(response);
                leadsFetch();

            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(error);
            }
        });
    }

    function leadsFetch() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",

            data: {
                groupIds: groupIds,
                'type': 'leads',
                'duration': duration

            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.leadWiseGraph').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'leads-status-' + data.group_id;
                    // from the data get values from sourceNames of Property Finder, Bayut and Dubizzle and remaining values sum and assign to others
                    var leads = data.leadCounts;
                    var sourceNames = data.statusNames;

                    var mainSources = ['Successful', 'New', 'Contacted',
                        'Unsuccessful'
                    ]; // Main sources
                    var otherSources = 'Others'; // Other sources label
                    var mainLeadsCount = [0, 0, 0,
                        0
                    ]; // Initialize leads count array for main sources
                    var otherLeadsCount = 0; // Initialize leads count for other sources

                    // Loop through sourceNames to extract names and leads count
                    sourceNames.forEach((source, index) => {
                        var parts = source.split(': ');
                        var sourceName = parts[0];
                        var leadCount = parseInt(parts[1]);

                        // Check if source is one of the main sources
                        var mainSourceIndex = mainSources.indexOf(sourceName);
                        if (mainSourceIndex !== -1) {
                            mainLeadsCount[mainSourceIndex] +=
                                leadCount; // Increment leads count for main sources
                        } else {
                            otherLeadsCount +=
                                leadCount; // Increment leads count for other sources
                        }
                    });

                    // Create array with names of main sources and 'Others'
                    var allSources = mainSources.concat([otherSources]);

                    // Create array with leads count for main sources and 'Others'
                    var allLeadsCount = mainLeadsCount.concat([otherLeadsCount]);



                    leadGraph(chartClass, allLeadsCount, allSources, 'Leads', 'bar');
                });
                console.log('leads fetched')

                getMeetingEvent();
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(error);
            }
        });
    }

    function getMeetingEvent() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",

            data: {
                groupIds: groupIds,
                'type': 'meetingsCountPerDate',
                'duration': duration

            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.meetingWiseGraph').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'meeting-days-' + data.group_id;
                    // from the data get values from sourceNames of Property Finder, Bayut and Dubizzle and remaining values sum and assign to others
                    var events = data.eventsPerDay;
                    var viewings = data.viewsPerDay;
                    var dates = Object.keys(events);
                    var counts = Object.values(events);
                    var views = Object.values(viewings);

                    meetingGraph(chartClass, counts,views, dates, 'Meetings', 'line');
                });
                daysWithoutDeal();
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(error);
            }
        });
    }

    function daysWithoutDeal() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'daysWithoutDeal',
                'duration': duration

            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.dealWiseGraph').each(function() {
                    var data = $(this).data('dataset');

                    var chartClass = 'deal-days-' + data.group_id;
                    var data = $(this).data('dataset');

                    var dayscount = data.daysCount;
                    var sourceNames = data.agentNames;

                    // remove the values that have zero count
                    var filteredSourceNames = [];
                    var filteredDaysCount = [];
                    for (var i = 0; i < dayscount.length; i++) {
                        if (dayscount[i] > 0) {
                            filteredSourceNames.push(sourceNames[i]);
                            filteredDaysCount.push(dayscount[i]);
                        }
                    }

                    // Combine filteredSourceNames and filteredDaysCount into an array of objects
                    var combinedData = [];
                    for (var i = 0; i < filteredDaysCount.length; i++) {
                        combinedData.push({
                            name: filteredSourceNames[i],
                            count: filteredDaysCount[i]
                        });
                    }

                    // Sort the combinedData array based on count in descending order
                    combinedData.sort(function(a, b) {
                        return b.count - a.count;
                    });

                    // Extract sorted sourceNames and dayscount arrays
                    var sortedSourceNames = combinedData.map(function(item) {
                        return item.name;
                    });
                    var sortedDaysCount = combinedData.map(function(item) {
                        return item.count;
                    });

                    dealGraph(chartClass, sortedDaysCount, sortedSourceNames, 'Days without Deal',
                        'bar');

                });
                console.log('leads fetched')

                listingFetch()
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(error);
            }
        });
    }

    function listingFetch() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'listings',
                'duration': duration

            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.listingWiseGraph').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'listings-monthly-' + data.group_id;
                    var data = $(this).data('dataset');

                    // from the data set just get the values of keys published,pocket, draft,unpublished
                    var listings = [data.published, data.pocket, data.draft, data.unpublished];
                    var types = ['Published', 'Pocket', 'Draft', 'Unpublished'];



                    listingStatsGraph(chartClass, listings, types);
                });
                console.log('leads fetched')
                fetchLocations();
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(error);
            }
        });
    }

    function fetchLocations() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",

            data: {
                groupIds: groupIds,
                'type': 'locations',
                'duration': duration

            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.locationWiseGraph').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'location-properties-' + data.group_id;
                    var listings = [12, 19, 3, 5, 2, 3, 4, 5, 6, 7, 8, 9];
                    locationGraph(chartClass, data.locationNames, data.propertyCounts);
                });
                console.log('leads fetched')
                fetchLeadSource();
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(error);
            }
        });
    }

    function fetchLeadSource() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",

            data: {
                groupIds: groupIds,
                'type': 'leadSource',
                'duration': duration

            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.leadSourceWiseGraph').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'leads-source-' + data.group_id;
                    var leads = data.leadCounts;
                    var sourceNames = data.sourceNames;

                    var mainSources = ['Dubizzle', 'Property Finder', 'Bayut']; // Main sources
                    var otherSources = 'Others'; // Other sources label
                    var mainLeadsCount = [0, 0, 0]; // Initialize leads count array for main sources
                    var otherLeadsCount = 0; // Initialize leads count for other sources

                    // Loop through sourceNames to extract names and leads count
                    sourceNames.forEach((source, index) => {
                        var parts = source.split(': ');
                        var sourceName = parts[0];
                        var leadCount = parseInt(parts[1]);

                        // Check if source is one of the main sources
                        var mainSourceIndex = mainSources.indexOf(sourceName);
                        if (mainSourceIndex !== -1) {
                            mainLeadsCount[mainSourceIndex] +=
                                leadCount; // Increment leads count for main sources
                        } else {
                            otherLeadsCount +=
                                leadCount; // Increment leads count for other sources
                        }
                    });

                    var allSources = mainSources.concat([otherSources]);
                    var allLeadsCount = mainLeadsCount.concat([otherLeadsCount]);
                    leadSource(chartClass, allLeadsCount, allSources, 'Lead Source');
                });
                console.log('leads fetched')
                propertyQuality();
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(error);
            }
        });
    }

    function fetchListingComparison() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",

            data: {
                groupIds: groupIds,
                'type': 'listingComparison',
                'duration': duration

            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.listingComparisonWiseGraph').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'listing-comparison-' + data.group_id;
                    listingComparisonGraph(chartClass);
                });
                console.log('leads fetched')

            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(error);
            }
        });
    }

    function propertyQuality() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",
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
                eventsStats();

            },
            error: function(xhr, status, error) {

                console.error(error);
            }
        });
    }

    function eventsStats() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'eventsStats',
                'duration': duration
            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.eventWiseStatus').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'events-count-' + data.group_id;
                    leadSource(chartClass, data.eventCounts, data.eventNames, 'Events');
                });

                listingLeadsSource();
            },
            error: function(xhr, status, error) {

                console.error(error);
            }
        });
    }

    function listingLeadsSource() {
        $.ajax({
            type: "POST",
            url: "{{ route('reports.group.v3.processData') }}",
            data: {
                groupIds: groupIds,
                'type': 'ListingLeadSource',
                'duration': duration
            },
            success: function(response) {
                $('#overalldata').append(response);

                $('.listingLeadWiseGraph').each(function() {
                    var data = $(this).data('dataset');
                    var chartClass = 'listing-leads-source-' + data.group_id;
                    var leads = data.leadCounts;
                    var sourceNames = data.statusNames;

                    var mainSources = ['Dubizzle', 'Property Finder', 'Bayut',
                        'Dialmydata'
                    ]; // Main sources
                    var otherSources = 'Others'; // Other sources label
                    var mainLeadsCount = [0, 0, 0,
                        0
                    ]; // Initialize leads count array for main sources
                    var otherLeadsCount = 0; // Initialize leads count for other sources

                    // Loop through sourceNames to extract names and leads count
                    sourceNames.forEach((source, index) => {
                        var parts = source.split(': ');
                        var sourceName = parts[0];
                        var leadCount = parseInt(parts[1]);

                        // Check if source is one of the main sources
                        var mainSourceIndex = mainSources.indexOf(sourceName);
                        if (mainSourceIndex !== -1) {
                            mainLeadsCount[mainSourceIndex] +=
                                leadCount; // Increment leads count for main sources
                        } else {
                            otherLeadsCount +=
                                leadCount; // Increment leads count for other sources
                        }
                    });

                    var allSources = mainSources.concat([otherSources]);
                    var allLeadsCount = mainLeadsCount.concat([otherLeadsCount]);
                    leadSource(chartClass, allLeadsCount, allSources, 'Listing Leads Source');
                });


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



            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }


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
            url: "{{ route('reports.group.v3.processData') }}",
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

            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

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


    let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October',
        'November', 'December'
    ]


    function leadGraph(element, leads, names, text, type) {
        var ctx = document.getElementById(element).getContext("2d");
        var gradientStroke = ctx.createLinearGradient(76, 142, 23, 1);
        gradientStroke.addColorStop(0, '#eea26f');
        gradientStroke.addColorStop(1, '#7BC8ED');
        gradient = ctx.createLinearGradient(0, 0, 0, 450);
        gradient.addColorStop(0, 'rgba(238, 162, 111, 1)');
        gradient.addColorStop(0.5, 'rgba(241, 196, 166, 0)');
        gradient.addColorStop(1, 'rgba(255, 0, 0, 0)');
        var myChart = new Chart(ctx, {
            type: type,
            data: {
                labels: names,
                datasets: [{
                    borderColor: gradientStroke,
                    pointBorderColor: gradientStroke,
                    pointBackgroundColor: gradientStroke,
                    pointHoverBackgroundColor: gradientStroke,
                    pointHoverBorderColor: gradientStroke,
                    borderWidth: 1,
                    backgroundColor: gradient,
                    pointBorderColor: "#fff",
                    fill: true,
                    label: text,
                    data: leads,
                    barThickness: 15, // Set the width of the bars
                }]
            },
            options: {
                indexAxis: 'y', // Display from left to right
                elements: {
                    point: {
                        display: true,
                        radius: 4,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: true
                        },
                        ticks: {
                            display: true
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        }
                    },
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            drawBorder: true
                        },
                        ticks: {
                            display: true
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        drawBorder: false
                    }
                }
            }
        });
    }
    function meetingGraph(element, leads,views, names, text, type) {
        var ctx = document.getElementById(element).getContext("2d");
        var gradientStroke = ctx.createLinearGradient(76, 142, 23, 1);
        gradientStroke.addColorStop(0, '#eea26f');
        gradientStroke.addColorStop(1, '#7BC8ED');

        var gradientStrokeTwo = ctx.createLinearGradient(76, 142, 23, 1);
        gradientStrokeTwo.addColorStop(0, '#646262');
        gradientStrokeTwo.addColorStop(1, '#646262');

        gradient = ctx.createLinearGradient(0, 0, 0, 450);
        gradient.addColorStop(0, 'rgba(238, 162, 111, 1)');
        gradient.addColorStop(0.5, 'rgba(241, 196, 166, 0)');
        gradient.addColorStop(1, 'rgba(255, 0, 0, 0)');

        gradientTwo = ctx.createLinearGradient(0, 0, 0, 450);
        gradientTwo.addColorStop(0, 'rgba(66, 65, 65, 1)');
        gradientTwo.addColorStop(0.5, 'rgba(66, 65, 65, 0)');
        gradientTwo.addColorStop(1, 'rgba(255, 0, 0, 0)');

        var myChart = new Chart(ctx, {
            type: type,
            data:{
            labels: names,
            datasets: [{
                borderColor: gradientStrokeTwo,
                pointBorderColor: gradientStrokeTwo,
                pointBackgroundColor: gradientStrokeTwo,
                pointHoverBackgroundColor: gradientStrokeTwo,
                pointHoverBorderColor: gradientStrokeTwo,
                borderWidth: 1,
                backgroundColor: gradientTwo,
                pointBorderColor: "#fff",
                fill: true,
                label: 'Meetings', // Label for the second dataset
                data: leads,
                barThickness: 5, // Set the width of the bars
            },
            {
                borderColor: gradientStroke,
                pointBorderColor: gradientStroke,
                pointBackgroundColor: gradientStroke,
                pointHoverBackgroundColor: gradientStroke,
                pointHoverBorderColor: gradientStroke,
                borderWidth: 1,
                backgroundColor: gradient,
                pointBorderColor: "#fff",
                fill: true,
                label: 'Viewings', // Label for the second dataset
                data: views,
                barThickness: 5, // Set the width of the bars
            }]
        },
            options: {
                indexAxis: 'x', // Display from left to right
                elements: {
                    point: {
                        display: true,
                        radius: 3,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: true
                        },
                        ticks: {
                            display: true
                        },
                        gridLines: {
                            display: true,
                            drawBorder: false,
                        }
                    },
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            drawBorder: true
                        },
                        ticks: {
                            display: true
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        drawBorder: false
                    }
                }
            }
        });
    }
    function dealGraph(element, leads, names, text, type) {
        var ctx = document.getElementById(element).getContext("2d");
        var gradientStroke = ctx.createLinearGradient(76, 142, 23, 1);
        gradientStroke.addColorStop(0, '#eea26f');
        gradientStroke.addColorStop(1, '#7BC8ED');
        gradient = ctx.createLinearGradient(0, 0, 0, 450);
        gradient.addColorStop(0, 'rgba(238, 162, 111, 1)');
        gradient.addColorStop(0.5, 'rgba(241, 196, 166, 0)');
        gradient.addColorStop(1, 'rgba(255, 0, 0, 0)');
        var myChart = new Chart(ctx, {
            type: type,
            data: {
                labels: names,
                datasets: [{
                    borderColor: gradientStroke,
                    pointBorderColor: gradientStroke,
                    pointBackgroundColor: gradientStroke,
                    pointHoverBackgroundColor: gradientStroke,
                    pointHoverBorderColor: gradientStroke,
                    borderWidth: 1,
                    backgroundColor: gradient,
                    pointBorderColor: "#fff",
                    fill: true,
                    label: text,
                    data: leads,
                    barThickness: 5, // Set the width of the bars
                }]
            },
            options: {
                indexAxis: 'y', // Display from left to right
                elements: {
                    point: {
                        display: true,
                        radius: 4,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: true
                        },
                        ticks: {
                            display: true
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        }
                    },
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            drawBorder: true
                        },
                        ticks: {
                            display: true
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        drawBorder: false
                    }
                }
            }
        });
    }


    function listingStatsGraph(element, listings, months) {
        var ctx = document.getElementById(element).getContext("2d");
        var gradientStroke = ctx.createLinearGradient(76, 142, 23, 1);
        gradientStroke.addColorStop(0, '#eea26f');
        gradientStroke.addColorStop(1, '#7BC8ED');
        gradient = ctx.createLinearGradient(0, 0, 0, 450);
        gradient.addColorStop(0, 'rgba(238, 162, 111, 1)');
        gradient.addColorStop(0.5, 'rgba(241, 196, 166, 0)');
        gradient.addColorStop(1, 'rgba(255, 0, 0, 0)');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    borderColor: gradientStroke,
                    pointBorderColor: gradientStroke,
                    pointBackgroundColor: gradientStroke,
                    pointHoverBackgroundColor: gradientStroke,
                    pointHoverBorderColor: gradientStroke,
                    borderWidth: 1,
                    backgroundColor: gradient,
                    pointBorderColor: "#fff",
                    fill: true,
                    label: 'listings',
                    data: listings,

                }]
            },
            options: {
                indexAxis: 'y', // Display from left to right
                elements: {
                    point: {
                        display: true,
                        radius: 4,
                    }
                },
                scales: {

                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: true

                        },
                        ticks: {
                            display: true
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        }

                    },
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: true
                        },

                        ticks: {
                            display: true
                        }

                    },

                },


                plugins: {
                    legend: {
                        display: true,
                        drawBorder: false
                    }
                },

            }
        });
    }

    function leadSource(element, leads, source, text) {
        var ctx = document.getElementById(element).getContext("2d");
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: source,
                datasets: [{
                    borderWidth: 1,
                    pointBorderColor: "#fff",
                    fill: true,
                    label: 'Sources',
                    data: leads,
                    backgroundColor: [
                        '#ebbfa1', // Background color for Bayut
                        '#ec9960', // Background color for Dubizzle
                        '#a05e31', // Background color for Property Finder
                        '#5d2803' // Background color for Property Finder

                    ]
                }]
            },
            options: {
                responsive: true,
                radius: '70%',
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: text
                    }
                }
            },
        });
    }



    function locationGraph(element, locations, properties) {

        var ctx = document.getElementById(element).getContext("2d");
        var gradientStroke = ctx.createLinearGradient(76, 42, 23, 1);
        gradientStroke.addColorStop(0, '#eea26f');
        gradientStroke.addColorStop(1, '#7BC8ED');
        gradient = ctx.createLinearGradient(0, 0, 0, 450);
        gradient.addColorStop(0, 'rgba(238, 162, 111, 1)');
        gradient.addColorStop(0.8, 'rgba(241, 196, 166, 0)');
        gradient.addColorStop(1, 'rgba(255, 0, 0, 0)');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: locations,
                datasets: [{
                    borderColor: gradientStroke,
                    pointBorderColor: gradientStroke,
                    pointBackgroundColor: gradientStroke,
                    pointHoverBackgroundColor: gradientStroke,
                    pointHoverBorderColor: gradientStroke,
                    borderWidth: 1,
                    backgroundColor: gradient,
                    pointBorderColor: "#fff",
                    fill: true,
                    label: 'Properties',
                    data: properties,

                }]
            },
            options: {
                indexAxis: 'y', // Display from left to right
                elements: {
                    point: {
                        display: true,
                        radius: 4,
                    }
                },
                scales: {

                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: true

                        },
                        ticks: {
                            display: true
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        }

                    },
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: true
                        },

                        ticks: {
                            display: true
                        }

                    },

                },


                plugins: {
                    legend: {
                        display: true,
                        drawBorder: false
                    }
                },

            }
        });
    }

    function listingComparisonGraph(element) {

        var ctx = document.getElementById(element).getContext("2d");
        var gradientStroke1 = ctx.createLinearGradient(76, 142, 23, 1);
        gradientStroke1.addColorStop(0, '#ebbfa1');
        gradientStroke1.addColorStop(1, '#ebbfa1');
        var gradient1 = ctx.createLinearGradient(0, 0, 0, 450);
        gradient1.addColorStop(0, 'rgba(235, 191, 161, 1)');
        gradient1.addColorStop(0.5, 'rgba(240, 222, 209, 0)');
        gradient1.addColorStop(1, 'rgba(255, 0, 0, 0)');
        var gradientStroke2 = ctx.createLinearGradient(76, 142, 23, 1);
        gradientStroke2.addColorStop(0, '#ec9960');
        gradientStroke2.addColorStop(1, '#ec9960');
        var gradient2 = ctx.createLinearGradient(0, 0, 0, 450);
        gradient2.addColorStop(0, 'rgba(236, 153, 96, 1)');
        gradient2.addColorStop(0.5, 'rgba(241, 205, 180, 0)');
        gradient2.addColorStop(1, 'rgba(255, 0, 0, 0)');
        var gradientStroke3 = ctx.createLinearGradient(76, 142, 23, 1);
        gradientStroke3.addColorStop(0, '#a05e31');
        gradientStroke3.addColorStop(1, '#a05e31');
        var gradient3 = ctx.createLinearGradient(0, 0, 0, 450);
        gradient3.addColorStop(0, 'rgba(160, 94, 49, 1)');
        gradient3.addColorStop(0.5, 'rgba(153, 125, 106, 0)');
        gradient3.addColorStop(1, 'rgba(255, 0, 0, 0)');

        var datasets = [{
                borderColor: gradientStroke1,
                pointBorderColor: gradientStroke1,
                pointBackgroundColor: gradientStroke1,
                pointHoverBackgroundColor: gradientStroke1,
                pointHoverBorderColor: gradientStroke1,
                borderWidth: 1,
                backgroundColor: gradient1,
                pointBorderColor: "#fff",
                fill: true,
                label: 'Listings 1',
                data: [12, 19, 3, 5, 2, 3, 4, 5, 6, 7, 8, 9]
            },
            {
                borderColor: gradientStroke2,
                pointBorderColor: gradientStroke2,
                pointBackgroundColor: gradientStroke2,
                pointHoverBackgroundColor: gradientStroke2,
                pointHoverBorderColor: gradientStroke2,
                borderWidth: 1,
                backgroundColor: gradient2,
                pointBorderColor: "#fff",
                fill: true,
                label: 'Listings 2',
                data: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
            },
            {
                borderColor: gradientStroke3,
                pointBorderColor: gradientStroke3,
                pointBackgroundColor: gradientStroke3,
                pointHoverBackgroundColor: gradientStroke3,
                pointHoverBorderColor: gradientStroke3,
                borderWidth: 1,
                backgroundColor: gradient3,
                pointBorderColor: "#fff",
                fill: true,
                label: 'Listings 3',
                data: [2, 3, 22, 3, 1, 5, 2, 20, 20, 21, 22, 13]
            }
        ];

        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: datasets
            },
            options: {
                elements: {
                    point: {
                        display: true,
                        radius: 4,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: true
                        },
                        ticks: {
                            display: true
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        }
                    },
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: true
                        },
                        ticks: {
                            display: true
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        drawBorder: false
                    }
                }
            }
        });
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
</script>
@endsection
