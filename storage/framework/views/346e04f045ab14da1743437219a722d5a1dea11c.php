<?php $__env->startSection('title'); ?>
  Request Log
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
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

       td{
           font-size: 12px;
       }

        #appLogTable_info{
            position: absolute;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <div class="flex-grow-1">
                    <h4 class="card-title mb-0 ">Request Log</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Security Log</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Log</a></li>
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
                                        <select name='search_user' id="search_user" class="form-control  search-inputs searchInput" multiple>

                                        </select>
                                        <div class="mt-1">
                                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                                <small for="">Show Inactive Users   </small>   <input type="checkbox" value="inactive" class="form-check-input" id="showInactiveAgents">
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
                                        <input type="text" class="form-control search-input searchInput" id="search_ip_address" name="ip_address"
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
                                        <input type="text" class="form-control search-input searchInput" autocomplete="off" id="search_url" name="url" placeholder="">

                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-4">
                                        <label for="search_method" class="form-label"> Method</label>

                                    </div>
                                    <div class="col-lg-8">
                                        <select class="form-control select2 search-inputs searchInput" name="search_method" id="search_method" multiple>
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
                                        <select class="form-control select2 search-inputs searchInput" name="response_code"
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
                                    <button type="submit" class="btn btn-sm  btn-primary action-search" id="searchSearchBtn">
                                        <i class="ri-search-line align-bottom me-1"></i> Search
                                    </button>
                                    <a href="javascript:void(0)" class="btn btn-sm text-warning action-clear" id="clearSearchBtn">Clear</a>
                                    <a href="javascript:void(0)" type="button" class="btn btn-sm text-danger action-close" onclick="toggleAdvanceSearch();">Close</a>
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




    <div class="row">
        <div class="col-xxl-12">
            <div class="card" id="contactList">
                <div class="card-body">

                    <table id="appLogTable" class="table align-middle" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>URL</th>
                            <th>IP</th>
                            <th>Method</th>
                            <th>Res. Code</th>
                            <th>Created at</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Modals -->

    <div id="viewModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">View Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-2">User:</div>
                        <div class="col-4" id="log_user"></div>
                        <div class="col-2">Type:</div>
                        <div class="col-4" id="log_type"></div>
                        <div class="col-2">Created at:</div>
                        <div class="col-4" id="log_created_at"></div>
                        <div class="col-2">IP:</div>
                        <div class="col-4" id="log_ip"></div>
                        <div class="col-2">Method:</div>
                        <div class="col-4" id="log_method"></div>
                        <div class="col-2">Response Code:</div>
                        <div class="col-4" id="log_response_code"></div>
                        <div class="col-12">Details:</div>
                        <div class="col-12" >
                           <pre class="language-markup" style="height: 275px;" tabindex="0">

                                   <table>
                                       <tbody id="log_description">

                                       </tbody>
                                   </table>

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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    



    <script src="<?php echo e(URL::asset('/assets/js/plugins/common.js')); ?>"></script>
    <script>

        let filtersData = [];
        $(document).on("submit", "#searchForm", function (e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);

            dataTable.ajax.reload();
        })
$("#search_user").getAgentSelect2('#collapseFilterForm',true,true,'Select User');
        $(document).on('change', '.operator-select', function() {
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

        function responseCodeBadge(code){
           if(code == '200'){
                return '<span class="badge border border-success text-success">'+code+'</span>';
            } if(code == '401'){
                return '<span class="badge border border-warning text-warning">'+code+'</span>';
            }else if(code == '403'){
                return '<span class="badge border border-danger text-danger">'+code+'</span>';
            }else if(code == '404'){
                return '<span class="badge border border-info text-info">'+code+'</span>';
            }else if(code == '429'){
                return '<span class="badge border border-danger text-danger">'+code+'</span>';
            }else if(code == '500'){
                return '<span class="badge border border-warning text-warning">'+code+'</span>';
            }
        }
let tableData = [];
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
            "aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
            "iDisplayLength": 10,
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "<?php echo e(route('admin.security.request.log.datatable')); ?>",
                type: "post",
                data: function (d) {
                    d.user_operator = filtersData?.search_user_operator;
                    d.user_id = filtersData?.search_user;
                    d.slug = filtersData?.slug;
                    d.ip_address = filtersData?.ip_address;
                    d.url = filtersData?.url;
                    d.method = filtersData?.search_method;
                    d.response_code = filtersData?.response_code;
                    d.created_at = filtersData?.search_created_date;
                    d.created_date_operator = filtersData?.search_created_date_operator;
                },


                error: function () {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                },
            },
                fnInitComplete: function(oSettings, json) {
                    tableData = json.data;

                    let first = $("#dataTable_wrapper .row div:first");
                    first.addClass("col-md-5").removeClass("col-md-6")
                    $(first).next().addClass("col-md-7").removeClass("col-md-6")
                    $(".dataTables_filter label").addClass(
                        "d-flex justify-between-between align-items-center gap-2")
                    $(`<div class="float-end">
    <div class="d-flex justify-content-end align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-soft-secondary waves-effect btn-m w-100 action-filter"
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
            },  {
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
            },{
                data: null,
                orderable: false,
            },{
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
            },{
                "targets": 1,
                render: function (type, row, data, meta) {
                    return data?.user?.full_name ?? '-';

                }
            }, {
                "targets": 2,
                render: function (type, row, data, meta) {
                    return data.slug;
                }
            },

                {
                    "targets": 3,
                    render: function (type, row, data, meta) {
                        return data.url;

                    }
                },{
                    "targets": 4,
                    render: function (type, row, data, meta) {
                        return data?.ip_address;

                    }
                },{
                    "targets": 5,
                    render: function (type, row, data, meta) {
                        return data?.method;

                    }
                },{
                    "targets": 6,
                    render: function (type, row, data, meta) {
                        return responseCodeBadge(data?.response_code);

                    }
                },
                {
                    "targets": 7,
                    render: function (type, row, data, meta) {
                        return moment(data.created_at).format('DD/MM/YYYY hh:mm:ss A');
                    }
                },
                {
                    targets: 8,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        return `<button class="btn btn-sm btn-soft-success viewDatas" onclick="viewData('`+data.id+`')" data-content="`+JSON.stringify(data.content)+`" >View</button>`
                    }
                }
            ]
        });

        function viewData(id){
            let data = tableData.find(x=>x.id == id);
            let content = JSON.parse(data.content);
            let html = ''; let val = '';
            for (const [key, value] of Object.entries(content)) {
                val = value;
                if(typeof value == 'object'){
                    val = JSON.stringify(value);
                }
                html+= `<tr>
                            <td>${key}: </td>
                            <td>${val}</td>
                        </tr>`;
                $("#log_description").html(html);
            }



                $("#log_type").html(data?.slug ?? "-");
                $("#log_user").html(data?.user?.full_name ?? "-");
                $("#log_response_code").html(data?.response_code ?? "-");
                $("#log_method").html(data?.method ?? "-");
                $("#log_ip").html(data?.ip_address ?? "-");
                $("#log_created_at").html(moment(data.created_at).format('DD/MM/YYYY hh:mm:ss A') ?? "-");

                        // $("#log_title").html(result.data.title);
                        // $("#log_type").html(result.data.type);
                        // $("#log_created_at").html(moment(result.data.created_at).format('DD/MM/YYYY hh:mm:ss A'));
                        $("#viewModal").modal("show");
        }





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



        $('#sidebarSecurityActive').addClass('active');
        $('#sidebarSecurity').addClass('collapse show');
        $('#sidebarSecurityIndex').addClass('active');
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/security/requestLog.blade.php ENDPATH**/ ?>