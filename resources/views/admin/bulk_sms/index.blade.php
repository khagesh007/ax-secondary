@extends('admin.layouts.master')
@section('title')
    Bulk SMS
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
    <link href="{{ asset('bootstrap-tagsinput.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Bulk Sms</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">SMS</a></li>
                            <li class="breadcrumb-item active">Bulk SMS</li>
                        </ol>
                    </div>
                    <div class="flex-grow-2">
                        <button type="button" id="send-bulk-sms" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#add-kpi-modal">Send Sms</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.bulk_sms.includes.filterForm')

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
                                <th>ID</th>
                                <th>Contact Name</th>
                                <th>Agent Name</th>
                                <th>Contact Number</th>
                                <th>Status</th>
                                <th>Confirm</th>
                                <th>Message Time</th>
                                <th>Sender</th>

                            </tr>
                        </thead>
                        <tbody class="form-check-all">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>





    @include('admin.bulk_sms.includes.modals')
@endsection
@section('script')

    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>

    <script src="{{ URL::asset('/assets/libs/chart.js/chart.js.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('bootstrap-tagsinput.js') }}"></script>

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
    $('#search_confirmation').select2({
        placeholder: "Select confirmation status",
        allowClear: true
    });
    $('#search_status').select2({
        placeholder: "Select status",
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
            url: "{{ route('contacts.bulk.sms.datatable') }}",
            type: "post",
            data: function(d) {
                d.agent_id = filtersData?.search_agent_id;
                d.status = filtersData?.search_status;
                d.confirmation = filtersData?.search_confirmation;
                d.batch_no = filtersData?.search_batch_no;
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




        },
        drawCallback: function(oSettings, json) {


        },
        columns: [{
                data: 'id',
                name: 'id',
                searchable: true
            },
            {
                data: function(data) {

                    return `<a href="javascript:void(0)" class="text-primary"  title="Batch no:` + data
                        .batch_no + `" >` + data.full_name + `</a>`;

                },
                name: 'full_name',
                searchable: true
            },
            {
                data: function(data) {

                    if (data.id == null) {
                        return '-';
                    } else {
                        return data.agent.full_name
                    }
                },
                name: 'agent_id',
                searchable: true
            },
            {
                data: function(data) {

                    if (data.id == null) {
                        return '-';
                    } else {
                        return `<span title="${data.message}"">${data.mobile}</span>`
                    }
                },
                name: 'full_name',
                searchable: true
            },
            {
                data: function(data) {

                    if (data.id == null) {
                        return '-';
                    } else {
                        if (data.status == 1) {
                            return `<span class="badge bg-success" title="${moment(data.updated_at).format('DD/MM/YYYY hh:mm:ss')}">Sent</span>`;
                        }
                        if (data.status == 2) {
                            return `<span class="badge bg-info" title="${moment(data.updated_at).format('DD/MM/YYYY hh:mm:ss')}">Pending</span>`;
                        }
                        if (data.status == 0) {
                            return `<span class="badge bg-danger" title="${moment(data.updated_at).format('DD/MM/YYYY hh:mm:ss')}">Failed</span>`;
                        }
                    }
                },
                name: 'status',
                searchable: false
            },
            {
                data: function(data) {

                    if (data.id == null) {
                        return '-';
                    } else {
                        if (data.confirmed == 1) {
                            return `<span class="badge bg-success" title="${moment(data.link_opened_at).format('DD/MM/YYYY hh:mm:ss')}">Confirmed</span>`;
                        }

                        if (data.confirmed == 0) {
                            return `<span class="badge bg-danger">Not Confirmed</span>`;
                        }
                    }
                },
                name: 'full_name',
                searchable: true
            },
            {
                data: function(data) {

                    if (data.id == null) {
                        return '-';
                    } else {
                        return `${moment(data.created_at).format('DD/MM/YYYY hh:mm:ss')}`;
                    }
                },
                name: 'created_at',
                searchable: true
            }, {
                data: function(data) {

                    if (data.id == null) {
                        return '-';
                    } else {
                        return data.sender.full_name
                    }
                },
                name: 'created_by_id',
                searchable: true
            },

        ],
    }); // end datatable init





    function toggleAdvanceSearch() {
        $("#collapseFilterForm").toggleClass("show")
    }

    $('#agent_id').select2({
        placeholder: "Select Manager First",
        allowClear: true,
        dropdownParent: $('#add-kpi-modal')
    });
    $('#contact_id').select2({
        placeholder: "Select agent First",
        allowClear: true,
        dropdownParent: $('#add-kpi-modal')
    });


    $(document).on('click', '#send-bulk-sms', function() {
        $('#headers-div').html('');
        // if file has value then empty it
        $('#file').val('');

    });
    $(document).on('keyup', '.sms-counter', function() {

        var message = $('#message').val();
        $('#msg-preview').html(message);
        var custom_link = $('#custom_link').val();
        var link = `<a href="${custom_link}">${custom_link}</a>`;

        if ($('#send_checking_link').is(':checked')) {
            $('#link-preview').html('http://axc.ae/abcd');
        } else {
            $('#link-preview').html(link);
        }




        var messageLength = message.length;
        var remaining = 160 - messageLength;

        var msgpreview = $('#msg-preview').text();
        var linkpreview = $('#link-preview').text();


        var msgpreviewCount = msgpreview.length;
        var linkpreviewCount = linkpreview.length;
        var totalCharacter = msgpreviewCount + linkpreviewCount;

        $('#total-character').html(totalCharacter);
        $('#total-parts').html(Math.ceil(totalCharacter / 160));

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

    $(document).on('change', '#agent_id', function() {
        var agent_id = $(this).val();
        $.ajax({
            type: "GET",
            url: "{{ route('contacts.bulk.sms.get.contacts') }}",

            data: {
                agent_id: agent_id,

            },
            success: function(response) {
                // make the agent select box empty
                $('#contact_id').html('');
                $('#contact_id').html(response);

                $('#contact_id').select2({
                    placeholder: "Select Contact",
                    allowClear: true,
                    dropdownParent: $('#add-kpi-modal')

                });

            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });







    $(document).on("click", "#excel-input", function(e) {
        e.preventDefault();
        $('#import-excel').modal('show');
    })


    $(document).on("change", "#file", function(e) {
        $('#headers-div').html('');
        e.preventDefault();
        var formData = new FormData($("#save-parameters")[0]);
        $.ajax({
            url: "{{ route('contacts.bulk.sms.excelImport') }}",
            type: 'POST',
            dataType: 'html',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(result) {
            $('#headers-div').html(result);
            },
            error: function(jqXhr, json, errorThrown) {

            },
        });

    })

    $(document).on("submit", "#save-parameters", function(e) {
        e.preventDefault();
        var formData = new FormData($("#save-parameters")[0]);
        $.ajax({
            url: "{{ route('contacts.bulk.sms.save') }}",
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
                $('.inputs').val('');
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


    })

    $(document).on("click", "#refreshTableButton", function() {
        $("#searchSearchBtn").trigger("click")

        $('.uuuuukpi-dataset').each(function() {
            var data = $(this).data('dataset');
            var average = $(this).data('average');
            var id = $(this).data('id');
            var chartClass = 'kpi-data-' + id;
            var target = $(this).data('target');
            var achieved = $(this).data('achieved');
            $average = $(this).data('average');
            var dates = Object.keys(data);
            var counts = Object.values(data);
            var average = Array(counts.length).fill(average);
            createGaugeChart(chartClass, target, achieved);
        });
    })



    function decodedFilterData(filtersData) {
        return {
            agent_id: filtersData?.search_agent_id,
            status: filtersData?.search_status,
            confirmation: filtersData?.search_confirmation,
            batch_no: filtersData?.search_batch_no,
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
    // Function to create a gauge chart using ECharts
    function createGaugeChart(element, target, achieved) {

        var chart = echarts.init(document.getElementById(element));

        //check if already initialized then destroy it and reinitialize
        if (chart) {
            chart.dispose();
            chart = echarts.init(document.getElementById(element));
        }

        let value = achieved;
        var option = {
            series: [{
                type: 'gauge',
                startAngle: 180,
                endAngle: 0,
                min: 0,
                max: target,
                splitNumber: 12,
                itemStyle: {
                    color: '#58D9F9',
                    shadowColor: 'rgba(0,138,255,0.45)',
                    shadowBlur: 10,
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                },
                progress: {
                    show: true,
                    roundCap: true,
                    width: 6 // Reduced width for smaller chart
                },
                pointer: {
                    icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
                    length: '75%',
                    width: 4, // Reduced width for smaller chart
                    offsetCenter: [0, '5%']
                },
                axisLine: {
                    lineStyle: {
                        width: 30,
                        color: [
                            [0.3, '#fd666d'],
                            [0.7, '#efe707'],
                            [1, '#07ef3f']
                        ]
                    }
                },
                axisTick: {
                    show: true // Hide the split lines
                },
                splitLine: {
                    show: true // Hide the split lines
                },
                axisLabel: {
                    show: false // Hide the split lines
                },
                title: {
                    show: false
                },
                detail: {
                    backgroundColor: '#fff',
                    borderColor: '#999',
                    borderWidth: 1, // Reduced border width
                    width: '60%',
                    lineHeight: 15, // Adjusted for smaller chart
                    height: 15, // Adjusted for smaller chart
                    borderRadius: 3, // Adjusted for smaller chart
                    offsetCenter: [0, '45%'], // Adjusted position
                    valueAnimation: true,
                    formatter: function(achieved) {
                        return '{value|' + achieved.toFixed(0) + '/' + target + '}';
                    },
                    rich: {
                        value: {
                            fontSize: 10, // Smaller font size for value
                            fontWeight: 'bolder',
                            color: '#777'
                        },
                        unit: {
                            fontSize: 6, // Smaller font size for unit
                            color: '#999',
                            padding: [0, 0, -5, 5] // Adjusted padding for smaller chart
                        }
                    }
                },
                data: [{
                    value: achieved
                }]
            }]
        };

        chart.setOption(option);
    }
</script>
@endsection
