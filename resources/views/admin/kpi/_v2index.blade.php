@extends('admin.layouts.master')
@section('title')
    Kpi
@endsection
@section('style')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />

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
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Key Performance Indicator</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kpi</a></li>
                            <li class="breadcrumb-item active">Setup</li>
                        </ol>
                    </div>
                    <div class="flex-grow-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#add-kpi-modal">Add KPI</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.kpi.includes.filterForm')

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <table id="categoryDatatable" class="table align-middle" style="width-100%">
                        <thead class="">
                            <tr class="text-uppercase table-light2">

                                {{-- <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                            data-com.bitwarden.browser.user-edited="yes">
                                    </div>
                                </th> --}}
                                {{-- <th>ID</th> --}}
                                <th>Agent Name</th>
                                {{-- <th>Module</th> --}}
                                <th>KPI</th>
                                <th>Target</th>
                                <th>Achieved</th>
                                <th>Graph</th>
                                <th>Result</th>
                                <th>Duration</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="form-check-all">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>





    @include('admin.kpi.includes.modals')
@endsection
@section('script')

    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>

    <script src="{{ URL::asset('/assets/libs/chart.js/chart.js.min.js') }}"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@section('global_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection






@yield('pageDT')
@stack('in_page_scripts')
<script>
    let filtersData = [];
    $('#search_agent_id').select2({
        placeholder: "Select Agent",
        allowClear: true
    });

    $('#search_kpi').select2({
        placeholder: "Select Kpi",
        allowClear: true
    });
    $("#search_start_date").flatpickr({
        altInput: true,
        mode: "range",
        dateFormat: "Y-m-d H:i",
    });

    $("#search_end_date").flatpickr({
        altInput: true,
        mode: "range",
        dateFormat: "Y-m-d H:i",
    });


    /*New Datatable*/
    dataTable = $('#categoryDatatable').DataTable({
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,

        dom: 'Bfrtip',
        buttons: [{
            extend: 'colvis',
            titleAttr: 'Show/Hide',
            text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

        }],

        order: [
            [0, "desc"]
        ],
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],
        dom: `<'row'<'col-sm-6 text-left'B> <'col-sm-6 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
        ajax: {
            url: "{{ route('reports.group.kpi.datatable') }}",
            type: "post",
            data: function(d) {
                d.agent_id = filtersData?.search_agent_id;
                d.start_date = filtersData?.search_start_date;
                d.end_date = filtersData?.search_end_date;
                d.kpi = filtersData?.search_kpi;
            },
            error: function() {
                $(".users-error").html("");
                $("#dataTable").append(
                    '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                );
                $("#dataTable_processing").css("display", "none");
            }
        },
        createdRow: function(row, data, index) {
            $('#categoryDatatable tbody').css('cursor', 'pointer');

        },
        fnInitComplete: function(oSettings, json) {
            let first = $("#dataTable_wrapper .row div:first");
            first.addClass("col-md-5").removeClass("col-md-6")
            $(first).next().addClass("col-md-7").removeClass("col-md-6")
            $(".dataTables_filter label").addClass(
                "d-flex justify-between-between align-items-center gap-2")
            $(`@includeIf('admin.kpi.includes.filterButtons')`).insertAfter(".dataTables_filter input")


            $('.kpi-dataset').each(function() {
                var data = $(this).data('dataset');
                var average = $(this).data('average');
                var id = $(this).data('id');
                var chartClass = 'kpi-data-' + id;

                $average = $(this).data('average');

                var dates = Object.keys(data);
                var counts = Object.values(data);

                var average = Array(counts.length).fill(average);


                meetingGraph(chartClass, counts, average, dates, 'Module', 'line');
            });

        },
        columns: [
            {
                data: function(data) {

                    return `<a href="javascript:void(0)" class="text-primary" onclick="viewKpi('` + data
                        .id + `')">` + data.full_name + `</a>`;

                },
                name: 'name',
                searchable: true
            },
            {
                data: function(data) {

                    if (data.kpis == null) {
                        return '-';
                    } else {
                        let models = data.kpis.map(kpi => {
                            if (kpi.stats) {
                                return `<span class="badge border border-primary  text-dark showGraph click-action-${kpi.stats.id}" data-kpi="${kpi.stats.id}">${kpi.kpi}</span>`;
                            }
                            return '';
                        });
                        return models.join(' ');
                    }
                },
                name: 'kpi',
                searchable: true
            },
            {
                data: function(data) {
                    //return data.updated_at;
                    if (data.kpis == null) {
                        return '-';
                    } else {

                        let totalAchieved = data.kpis.reduce((sum, kpi) => {
                            if (kpi.stats) {
                                return sum + parseInt(kpi.stats.target || 0);
                            }
                            return sum;
                        }, 0);

                        return totalAchieved.toString();
                    }

                },
                name: 'target',
                searchable: true
            },
            {
                data: function(data) {
                    //return data.updated_at;
                    if (data.kpis == null) {
                        return '-';
                    } else {


                        let totalAchieved = data.kpis.reduce((sum, kpi) => {
                            if (kpi.stats) {
                                return sum + parseInt(kpi.stats.achieved || 0);
                            }
                            return sum;
                        }, 0);

                        return totalAchieved.toString();
                    }

                },
                name: 'target',
                searchable: true
            },

            {
                data: function(data) {
                    //return data.updated_at;
                    if (data.kpis == null) {
                        return '-';
                    } else {
                        let canvasElements = '';
                        let count = 0;

                        for (let kpi of data.kpis) {
                            if (kpi.stats !== null) {
                                canvasElements += `<div class="hideGraph-${kpi.stats.id} ${count > 0 ? 'd-none' : ''}" id="div-id-${kpi.stats.id}">
                                <canvas id="kpi-data-${kpi.stats.id}" class="kpi-dataset kpi-data-${kpi.stats.id}" style="width: 40px; height: 40px;"
                                        data-dataset="${kpi.stats.data}" data-average="${kpi.stats.average_per_day}"
                                        data-id="${kpi.stats.id}"></canvas>
                           </div>`;
                            }
                            count++;
                        }

                        return canvasElements || '-';
                    }

                },
                name: 'target',
                searchable: true
            },
            {
                data: function(data) {

                    if (data.kpis == null) {
                        return '-';
                    } else {
                        let processedStatus = '';
                        let allProcessed = true;
                        let allPending = true;
                        let allInProgress = true;

                        for (let kpi of data.kpis) {
                            if (kpi.is_processed === 0) {
                                allProcessed = false;
                                allInProgress = false;
                            } else if (kpi.is_processed === 1) {
                                allPending = false;
                                allInProgress = false;
                            } else if (kpi.is_processed === 2) {
                                allPending = false;
                                allProcessed = false;
                            }
                        }

                        if (allProcessed) {
                            processedStatus = '<span class="badge bg-success">Processed</span>';
                        } else if (allPending) {
                            processedStatus = '<span class="badge bg-danger">Pending</span>';
                        } else if (allInProgress) {
                            processedStatus = '<span class="badge bg-info">INPROGRESS</span>';
                        } else {
                            processedStatus = '<span class="badge bg-dark">MIXED</span>';
                        }

                        return processedStatus;
                    }


                    if (data.is_processed == 1) {
                        return '<span class="badge bg-success">Processed</span>';
                    } else {
                        return '<span class="badge bg-danger">Pending</span>';
                    }

                },
                name: 'target',
                searchable: true
            },
            {
                data: function(data) {
                    if (data.kpis == null) {
                        return '-';
                    } else {
                        let models = data.kpis.map(kpi => {
                            if (kpi.stats) {
                                return `<span class="badge border border-primary  text-dark showGraph click-action-${kpi.stats.id}" data-kpi="${kpi.stats.id}">${kpi.start_date} - ${kpi.end_date}</span>`;
                            }
                            return ''; // Return an empty string if kpi.stats is null or undefined
                        });
                        return models.join(' ');
                    }

                },
                name: 'start_date',
                searchable: true
            },

            {
                data: function(data) {

                    if (data.kpis == null) {
                        return '-';
                    } else {
                        let models = data.kpis.map(kpi => {
                            if (kpi.stats) {
                                return `<span class="badge border border-primary  text-dark showGraph click-action-${kpi.stats.id}" title="${moment(data.created_at).format('DD/MM/YYYY hh:mm:ss')}"  data-kpi="${kpi.stats.id}">${moment(data.created_at).format('D MMMM')}</span>`;
                            }
                            return ''; // Return an empty string if kpi.stats is null or undefined
                        });
                        return models.join(' ');

                    }

                },
                name: 'created_at',
                searchable: true
            },
            {
                data: function(data) {
                    let ul = '';

                    let models = data.kpis.map(kpi => {
                        ul +=
                            `<li><a href="javascript:void(0)" data-id="${kpi.id}" onclick="viewData('${kpi.id}')" class="dropdown-item edit-item-btn edit-link"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit ${kpi.id}: ${kpi.kpi}</a></li>`;
                    });

                    return `<div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        ${ul}
                                    </ul>
                                </div>`;

                },
                name: 'action',
                searchable: false


            }
        ],
    }); // end datatable init

    function viewData(id) {
        $.ajax({
            type: "GET",
            url: "{{ route('reports.group.kpi.view') }}",
            data: {
                id: id
            },
            success: function(response) {
                $('#edit-kpi').html(response);
                $('#edit_duration').select2({
                    placeholder: "Select Duration",
                    allowClear: true
                });
                $('#edit_manager_id').select2({
                    placeholder: "Select Manager",
                    allowClear: true
                });
                $('#edit_agent_id').select2({
                    placeholder: "Select Agent",
                    allowClear: true
                });
                $('#edit_kpi').select2({
                    placeholder: "Select Kpi",
                    allowClear: true
                });
                $('#edit-kpi-modal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function viewKpi(id) {
        url = "{{ route('reports.group.kpi.viewPerformace', ':id') }}";
        url = url.replace(':id', id);
        window.open(url, '_blank').focus();
    }

    function toggleAdvanceSearch() {
        $("#collapseFilterForm").toggleClass("show")
    }

    $('#agent_id').select2({
        placeholder: "Select Manager First",
        allowClear: true,
        dropdownParent: $('#add-kpi-modal')
    });



    $(document).on('change', '#manager_id', function() {
        var agent_id = $(this).val();
        $.ajax({
            type: "GET",
            url: "{{ route('reports.group.kpi.users') }}",

            data: {
                agent_id: agent_id,

            },
            success: function(response) {
                // make the agent select box empty
                $('#agent_id').html('');
                $('#agent_id').html(response);

                $('#agent_id').select2({
                    placeholder: "Select Agent",
                    allowClear: true,
                    dropdownParent: $('#add-kpi-modal')

                });

            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });
    $(document).on('change', '#edit_manager_id', function() {
        var agent_id = $(this).val();
        $.ajax({
            type: "GET",
            url: "{{ route('reports.group.kpi.users') }}",

            data: {
                agent_id: agent_id,

            },
            success: function(response) {
                // make the agent select box empty
                $('#edit_agent_id').html('');
                $('#edit_agent_id').html(response);

                $('#edit_agent_id').select2({
                    placeholder: "Select Agent",
                    allowClear: true,
                    dropdownParent: $('#edit-kpi-modal')
                });

            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });
    $(document).on('change', '#model', function() {
        var model = $(this).val();
        $.ajax({
            type: "GET",
            url: "{{ route('reports.group.kpi.model') }}",

            data: {
                model: model,

            },
            success: function(response) {
                // make the agent select box empty
                $('#kpi').html('');
                $('#kpi').html(response);

                $('#kpi').select2({
                    placeholder: "Select KPI",
                    allowClear: true
                });

            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });



    $(document).on('click', '.get-kpi', function() {
        var agent_id = $(this).data('id');
        $.ajax({
            type: "GET",
            url: "{{ route('reports.group.kpi.user.kpi') }}",
            data: {
                agent_id: agent_id,
            },
            success: function(response) {
                $('#agent-kpi').html(response);
                $('#get-user-kpi-modal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });



    $(document).on('click', '.get-kpi-statistics', function() {
        var kpi = $(this).data('id');
        $.ajax({
            type: "GET",
            url: "{{ route('reports.group.kpi.kpi.stats') }}",
            data: {
                kpi: kpi,
            },
            success: function(response) {
                $('#kpi-details').html(response);
                $('#user-kpi-details').modal('show');
                var dataset = $('#kpi-status').data('dataset');
                var data = JSON.parse(dataset);

            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });


    $(document).on("submit", "#save-parameters", function(e) {
        e.preventDefault();
        var formData = new FormData($("#save-parameters")[0]);
        $.ajax({
            url: "{{ route('reports.group.kpi.save.parameters') }}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(result) {
                dataTable.ajax.reload();
                showToastify(result.msg, "success")
                $('#add-kpi-modal').modal('hide');


            },
            error: function(jqXhr, json, errorThrown) {

            },
        });

    })

    $(document).on("submit", "#update-parameters", function(e) {
        e.preventDefault();
        var formData = new FormData($("#update-parameters")[0]);
        $.ajax({
            url: "{{ route('reports.group.kpi.update.parameters') }}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(result) {
                dataTable.ajax.reload();
                showToastify(result.msg, "success")
                $('#edit-kpi-modal').modal('hide');


            },
            error: function(jqXhr, json, errorThrown) {

            },
        });

    })


    $(document).on("click", ".showGraph", function(e) {
        let kpi = $(this).data('kpi');
        $('.hideGraph-' + kpi).removeClass('d-none');
        $('.hideGraph-' + kpi).siblings().addClass('d-none');
        $('.showGraph').removeClass('bg-primary text-light');
        $('.showGraph').addClass('border border-primary text-dark');
        $('.click-action-' + kpi).removeClass('border border-primary text-dark');
        $('.click-action-' + kpi).addClass('bg-primary text-light');
    });

    $(document).on("submit", "#searchForm", function(e) {
        e.preventDefault();

        filtersData = $("#searchForm").serialize();
        filtersData = urldecode(filtersData);
        filtersData = QueryStringToJSON(filtersData);
        console.log(filtersData);
        dataTable.ajax.reload();
    });

    $(document).on("click", "#clearSearchBtn", function() {
        $(".search-inputs").val("").trigger("change");
        $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
        $("#searchSearchBtn").trigger("click")
        $('.statusFilter').removeClass('active');
        $('.statusFilterClear').addClass('active');
        dataTable.column(8).visible(false);
        dataTable.column(9).visible(false);

    })

    $(document).on("click", "#refreshTableButton", function() {
        $("#searchSearchBtn").trigger("click")



        $('.kpi-dataset').each(function() {
            var data = $(this).data('dataset');
            var average = $(this).data('average');
            var id = $(this).data('id');
            var chartClass = 'kpi-data-' + id;
            $average = $(this).data('average');
            var dates = Object.keys(data);
            var counts = Object.values(data);
            var average = Array(counts.length).fill(average);
            meetingGraph(chartClass, counts, average, dates, 'Module', 'line');
        });
    })



    function decodedFilterData(filtersData) {
        return {
            agent_id: filtersData?.search_agent_id,
            search_start_date: filtersData?.search_start_date,
            search_end_date: filtersData?.search_end_date,
            search_kpi: filtersData?.search_kpi,
        };
    }

    function meetingGraph(element, leads, average, names, text, type) {
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

        let myChart = new Chart(ctx, {
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
                        fill: false,
                        label: text, // Label for the second dataset
                        data: leads,
                        barThickness: 5, // Set the width of the bars
                    },

                ]
            },
            options: {
                indexAxis: 'x', // Display from left to right
                elements: {
                    point: {
                        display: false,
                        radius: 0,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            display: false
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
                            drawBorder: false
                        },
                        ticks: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        });
    }
</script>
@endsection
