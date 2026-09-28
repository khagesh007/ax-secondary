@extends('admin.layouts.master')
@section('title')
    Portal Listing Sync Logs
@endsection
@section('style')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet"
          type="text/css"/>

    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
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

        td {
            font-size: 12px;
        }

        #appLogTable_info {
            position: absolute;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/json-viewer/jquery.json-viewer.css') }}"  type="text/css"></link>
@endsection
@section('content')

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <div class="flex-grow-1">
                    <h4 class="card-title mb-0 ">Portal Sync Log</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Listing</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Portal Sync Log</a></li>
                    </ol>
                </div>
                <div class="flex-shrink-0">

                </div>
            </div>
        </div>
    </div>


    <div class="collapse slideIn" id="collapseFilterForm">
        <div class="row">
            <form id="searchForm" class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Advanced Filters</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row">
                            <div class="col-xl-4 col-md-6 col-sm-12">
                                <div class="row">
                                    <div class="col-4">
                                        <label for="search_user" class="form-label"> User</label>
                                        <select name='search_user_operator' id="search_user_operator"
                                                class="form-control   operator-select">
                                            <option value="in" selected>is</option>
                                            <option value="not_in">is not</option>
                                            <option value="null">is empty</option>
                                            <option value="not_null">is not empty</option>
                                        </select>

                                    </div>
                                    <div class="col-8">
                                        <select name='search_user' id="search_user"
                                                class="form-control  search-inputs searchInput" multiple>

                                        </select>
                                        <div class="mt-1">
                                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                                <small for="">Show Inactive Users </small> <input type="checkbox"
                                                                                                  value="inactive"
                                                                                                  class="form-check-input"
                                                                                                  id="showInactiveAgents">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-2">
                                    <div class="col-lg-4">
                                        <label for="search_type" class="form-label"> Type</label>

                                    </div>
                                    <div class="col-lg-8">
                                        <select class="form-control select2 search-inputs searchInput" name="slug"
                                                id="search_type" multiple>
                                            <option value="failed_login_attempt">Failed Login Attempts</option>
                                            <option value="request_limit_exceeded">Request Limit Exceeded</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-4">
                                        <label for="search_ip_address" class="form-label"> IP Address</label>

                                    </div>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control search-input searchInput"
                                               id="search_ip_address" name="ip_address"
                                               autocomplete="off"
                                               placeholder="Ex. 127.0.0.1">

                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6 col-sm-12">
                                <div class="row mt-2">
                                    <div class="col-lg-4">
                                        <label for="search_url" class="form-label"> URL</label>

                                    </div>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control search-input searchInput"
                                               autocomplete="off" id="search_url" name="url" placeholder="">

                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-4">
                                        <label for="search_method" class="form-label"> Method</label>

                                    </div>
                                    <div class="col-lg-8">
                                        <select class="form-control select2 search-inputs searchInput"
                                                name="search_method" id="search_method" multiple>
                                            <option value="GET">GET</option>
                                            <option value="POST">POST</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg-4">
                                        <label for="response_code" class="form-label"> Response Code</label>

                                    </div>
                                    <div class="col-lg-8">
                                        <select class="form-control select2 search-inputs searchInput"
                                                name="response_code"
                                                id="response_code" multiple>
                                            <option value="200">200</option>
                                            <option value="401">401</option>
                                            <option value="403">403</option>
                                            <option value="404">404</option>
                                            <option value="429">429</option>
                                        </select>
                                    </div>
                                </div>


                            </div>
                            <div class="col-xl-4 col-md-6 col-sm-12">
                                <div class="row mt-2">
                                    <div class="col-lg-4">
                                        <label for="search_created_date" class="form-label">Created Date</label>
                                        <select name='search_created_date_operator' id="search_created_date_operator"
                                                class="form-control operator-select">
                                            <option value="between_dates" selected>On</option>
                                            <option value="before">Before</option>
                                            <option value="after">After</option>
                                            <option value="between_dates">Between Dates</option>
                                            <option value="today">Today</option>
                                            <option value="yesterday">Yesterday</option>
                                            <option value="last_week">Last Week</option>
                                            <option value="last_month">Last Month</option>
                                            <option value="this_month">This Month</option>
                                            <option value="this_week">This Week</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-8">
                                        <input type="date" class="form-control search-inputs searchInput"
                                               data-provider="flatpickr" data-range-date="true" data-date-format="Y-m-d"
                                               id="search_created_date" name="search_created_date"
                                               placeholder="Enter created date range">
                                    </div>
                                </div>


                                <hr>

                                <div class="mt-4  gap-2 text-end">
                                    <button type="submit" class="btn btn-sm  btn-primary action-search"
                                            id="searchSearchBtn">
                                        <i class="ri-search-line align-bottom me-1"></i> Search
                                    </button>
                                    <a href="javascript:void(0)" class="btn btn-sm text-warning action-clear"
                                       id="clearSearchBtn">Clear</a>
                                    <a href="javascript:void(0)" type="button"
                                       class="btn btn-sm text-danger action-close" onclick="toggleAdvanceSearch();">Close</a>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 col-sm-12">

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="table-sticky-header-container" id="table-sticky-header">
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1 no-border d-inline-flex" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link All  statusFilterClear active margin-minus-4" data-bs-toggle="tab"
                           id="All" href="#" role="tab" aria-selected="false" tabindex="-1">
                            All
                        </a>
                    </li>

                    @foreach ($statuses ?? [] as $key => $value)
                        <li class="nav-item" role="presentation" id="status-filter-{{ $key }}">
                            <a class="nav-link All statusFilter margin-minus-4 text-uppercase" data-bs-toggle="tab"
                               id="status-filter-{{ $key }}" href="javascript:void(0)"
                               data-status="{{ $value }}" role="tab" aria-selected="false"
                               tabindex="-1">
                                {{ $value }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-12">
            <div class="card" id="contactList">
                <div class="card-body">

                    <table id="appLogTable" class="table align-middle" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Listing</th>
                            <th>Portal</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th>User</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Modals -->

    <div id="viewModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">View Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-4">User:</div>
                                <div class="col-8 log_data" id="log_user"></div>

                                <div class="col-4">Created at:</div>
                                <div class="col-8 log_data" id="log_created_at"></div>
                                <div class="col-4">Status:</div>
                                <div class="col-8 log_data" id="log_status"></div>

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                            <div class="col-4">Title:</div>
                            <div class="col-8 log_data" id="log_title"></div>
                            <div class="col-4">Description:</div>
                            <div class="col-8 log_data" id="log_description"></div>
                            </div>
                        </div>

                        <div class="col-12">Response Details:</div>
                        <div class="col-12">
                           <pre class="language-markup" style="height: 275px;" tabindex="0">


                                       <p class="log_data" id="log_response">

                                       </p>


                            </pre>
                        </div>
                        <div class="col-12 mt-2">Payload:</div>
                        <div class="col-12">
                           <pre class="language-markup" style="height: 275px;" tabindex="0">


                                       <p class="log_data" id="log_payload">

                                       </p>


                            </pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    {{-- <script src=" "></script> --}}



    <script src="{{ URL::asset('/assets/js/plugins/common.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/json-viewer/jquery.json-viewer.js') }}"></script>
    <script>

        let filtersData = [];
        $(document).on("submit", "#searchForm", function (e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);

            dataTable.ajax.reload();
        })
        $("#search_user").getAgentSelect2('#collapseFilterForm', true, true, 'Select User');
        $(document).on('change', '.operator-select', function () {
            var operator = $(this).val();
            var input = $(this).closest('.row').find('.searchInput');
            var select2Element = input.next('.select2-container').find('select');
            var operators = ["null", "not_null", "today", "yesterday", "last_week", "last_month", "this_month", "this_week"];
            if (operators.includes(operator)) {
                select2Element.prop('disabled', true);
                input.prop('disabled', true);
            } else {
                select2Element.prop('disabled', false);
                input.prop('disabled', false);
            }
        });


        $(document).on("click", "#clearSearchBtn", function () {
            $(".search-inputs").val("").trigger("change");
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", "#refreshTableButton", function () {
            $("#searchSearchBtn").trigger("click")
        })


        $(document).on("click", "#closeBtn", function () {
            $(".edit-assignment").addClass("d-none")
            toggleForm(false)
        })


        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }

        function responseCodeBadge(code) {
            if (code == '200') {
                return '<span class="badge border border-success text-success">' + code + '</span>';
            }
            if (code == '401') {
                return '<span class="badge border border-warning text-warning">' + code + '</span>';
            } else if (code == '403') {
                return '<span class="badge border border-danger text-danger">' + code + '</span>';
            } else if (code == '404') {
                return '<span class="badge border border-info text-info">' + code + '</span>';
            } else if (code == '429') {
                return '<span class="badge border border-danger text-danger">' + code + '</span>';
            } else if (code == '500') {
                return '<span class="badge border border-warning text-warning">' + code + '</span>';
            }
        }

        let tableData = [];
        let property_id = "{{ request()->get('property_id') }}";
        let dataTable = $('#appLogTable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search"
            },

            dom: 'Bfrtlip',
            createdRow: function (row, data, index) {
                $('#appLogTable tbody').css('cursor', 'pointer');

            },
            "aLengthMenu": [[10, 25, 50, 75, -1], [10, 25, 50, 75, "All"]],
            "iDisplayLength": 10,
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('listing.portal.timeline.dt') }}",
                type: "post",
                data: function (d) {
                    d.status = filtersData?.status;
                    d.property_id = property_id ?? null;
                },


                error: function () {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                },
            },
            fnInitComplete: function (oSettings, json) {
                tableData = json.data;

                let first = $("#dataTable_wrapper .row div:first");
                first.addClass("col-md-5").removeClass("col-md-6")
                $(first).next().addClass("col-md-7").removeClass("col-md-6")
                $(".dataTables_filter label").addClass(
                    "d-flex justify-between-between align-items-center gap-2")
                $(`<div class="float-end">
    <div class="d-flex justify-content-end align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-soft-secondary waves-effect btn-m w-100 action-filter d-none"
                onclick="toggleAdvanceSearch();"><i
                class="ri-equalizer-fill me-1 align-bottom"></i>
            Advanced Filters
        </button>
        <a href="javascript:void(0)" class="text-secondary" title="Refresh table"
           id="refreshTableButton">
            <i class="ri-refresh-line me-1 align-bottom" style="font-size: 25px"></i>
        </a>
    </div>
</div>`).insertAfter(".dataTables_filter input")
            },
            "aoColumns": [{
                data: 'id',
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }
            ],
            "columnDefs": [{
                "targets": 0,
                visible: false,
                render: function (type, row, data, meta) {
                    return data.id;
                }
            }, {
                "targets": 1,
                render: function (type, row, data, meta) {
                    return data?.property?.ref_no ?? '-';

                }
            },

                {
                    "targets": 2,
                    render: function (type, row, data, meta) {
                        return data?.portal?.name ?? '-';

                    }
                }, {
                    "targets": 3,
                    render: function (type, row, data, meta) {
                        return data?.title;

                    }
                },
                {
                    "targets": 4,
                    render: function (type, row, data, meta) {
                        if(data?.status == 'success') {
                            return `<span class="badge bg-success text-white">` + data?.status + `</span>`;
                        } else if(data?.status == 'failed') {
                            return `<span class="badge bg-danger text-white">` + data?.status + `</span>`;
                        } else if(data?.status == 'pending') {
                            return `<span class="badge bg-warning text-dark">` + data?.status + `</span>`;
                        }
                        else {
                            return `<span class="badge bg-danger text-white">` + data?.status + `</span>`;
                        }
                        return data?.status;

                    }
                },
                {
                    "targets": 5,
                    render: function (type, row, data, meta) {
                        return moment(data.created_at).format('DD/MM/YYYY hh:mm:ss A');
                    }
                },
                {
                    "targets": 6,
                    render: function (type, row, data, meta) {
                        return data?.user?.full_name ?? '-';

                    }
                },

                {
                    targets: 7,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        return `<button class="btn btn-sm btn-soft-success view-button" data-id='${data.id}'>View</button>`
                    }
                }
            ]
        });


        $(document).on('click', '.view-button', function () {
            const id = $(this).data('id');

            $("#log_response").html("");
            $("#log_payload").html("");
            $("#log_description").html("-");
            $("#log_title").html("-");
            $("#log_status").html("-");
            $("#log_user").html("-");
            $("#log_created_at").html("-");

            $.ajax({
                url: "{{ route('listing.portal.timeline.getDetails') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function(res) {
                    const {status, data} = res;
                    if (status == 1) {
                        if (data) {
                            $("#log_description").html(data?.description ?? "-");
                            $("#log_title").html(data?.title ?? "-");
                            $("#log_status").html(data?.status ?? "-");
                            $("#log_user").html(data?.user?.full_name ?? "-");
                            $("#log_created_at").html(moment(data.created_at).format('DD/MM/YYYY hh:mm:ss A') ?? "-");

                            let payload = data.payload ? (typeof data.payload === 'string' ? JSON.parse(data.payload) : data.payload) : {};

                            $("#log_payload").jsonViewer(payload, {
                                collapsed: false,
                                withLinks: false,
                                withPadding: true,
                                withQuotes: true
                            });

                            let response = data.response ? (typeof data.response === 'string' ? JSON.parse(data.response) : data.response) : {};

                            $("#log_response").jsonViewer(response, {
                                collapsed: false,
                                withLinks: false,
                                withPadding: true,
                                withQuotes: true
                            });

                            $("#viewModal").modal("show");
                        }
                    }
                }
            });
        })


        $(document).on('change', 'tbody tr .selectable-checkbox', function () {

            $(this).parents('tr').toggleClass('selected');
            var checked = $(this).is(':checked');
            if (!checked) {
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
            updateMultiOption();
        });

        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        } // highlightRow


        dataTable.on('click', 'tbody tr', function (e) {
            var $cell = $(e.target).closest('td'),
                msg;
            //console.log('Index ==========='+$cell.index());
            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 9) {
                    // console.log($cell);
                } else {
                    //   viewData(data.id);
                    highlightRow($(this));

                }
            } // end if
        }); // table row click


        $(document).on("click", ".statusFilter", function() {
            filtersData.status = $(this).data("status");
            dataTable.ajax.reload(null, true);
        });
        $(document).on("click", ".statusFilterClear", function() {
            filtersData.status = null;
            dataTable.ajax.reload(null, true);
        });


        $('#sidebarSecurityActive').addClass('active');
        $('#sidebarSecurity').addClass('collapse show');
        $('#sidebarSecurityIndex').addClass('active');
    </script>
@endsection
