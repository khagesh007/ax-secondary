@extends('admin.layouts.master')
@section('title')
   App Logs
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

        .tasks-board {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            overflow-x: auto;
            -webkit-box-align: stretch;
            -ms-flex-align: stretch;
            align-items: stretch;
            height: 350px;
        }

        .tasks-board .tasks-list {
            min-width: 300px;
            margin-right: 24px;
        }

        .tasks-board::-webkit-scrollbar {
            -webkit-appearance: none
        }

        .tasks-board::-webkit-scrollbar:vertical {
            width: 10px
        }

        .tasks-board::-webkit-scrollbar:horizontal {
            height: 8px
        }

        .tasks-board::-webkit-scrollbar-thumb {
            background-color: rgba(var(--vz-dark-rgb), .075);
            border-radius: 8px
        }

        .tasks-board::-webkit-scrollbar-track {
            border-radius: 8px
        }

        .tasks-board .tasks-list {
            max-width: 200px !important;
            min-width: 175px !important;
        }

        .schedules .card-body {
            padding: 0.2rem 0.2rem !important;

        }

        .card-body .photographer {
            padding: 0.5rem !important;

        }

        .booked .card-body {
            padding: 0.2rem 0.2rem !important;
            background: #fff7f3;
            color: white !important;
        }

        #photographerEventCard.fullscreen {
            z-index: 999;
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #f0f0f0;
            width: 82% !important;
            z-index: 1050;
            margin-left: 255px;
            border: solid 1px;
            right: 103px;
        }

        .active-photographer {
            background: #c5dbc5;
            border: 1px solid #25c425;
            border-radius: 6px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <div class="flex-grow-1">
                    <h4 class="card-title mb-0 ">App Log</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">App</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Log</a></li>
                    </ol>
                </div>
                <div class="flex-shrink-0">

                </div>
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
                            <th scope="col" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>User</th>
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
                       <div class="col-4">Title:</div>
                       <div class="col-8" id="log_title"></div>
                       <div class="col-4">Type:</div>
                       <div class="col-8" id="log_type"></div>
                       <div class="col-4">Logged at:</div>
                       <div class="col-8" id="log_created_at"></div>
                       <div class="col-12">Description/Details:</div>
                       <div class="col-12" >
                           <pre class="language-markup" style="height: 275px;" tabindex="0">
                               <code class="language-markup" id="log_description">
                                 </code>
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


    <script>

        let shootPage = "{{route('admin.jobs.shoot.index')}}";
        let propertyView = "{{route('admin.listing.index')}}";
        let calendarEventView = "{{route('admin.events.index')}}?event=";
    </script>
    <script src="{{ URL::asset('/assets/js/plugins/common.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/job-shoot.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/event.js') }}?v={{time()}}"></script>
    <script>

        let filtersData = [];
        $(document).on("submit", "#searchForm", function (e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);
            dataTable.ajax.reload();
        })




        $(document).on("click", "#clearSearchBtn", function () {
            $(".search-inputs").val("").trigger("change");
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", "#refreshTableButton", function () {
            $("#searchSearchBtn").trigger("click")
        })



        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })


        $(document).on("click", "#closeBtn", function () {
            $(".edit-assignment").addClass("d-none")
            toggleForm(false)
        })

        $(document).on('click', '#deleteBtn', function (event) {
            @can('Property Shoot Delete')
            deleteData($('#id').val());
            @endcan
        });





        let dataTable = $('#appLogTable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search"
            },

            dom: 'Bfrtip',
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],
            createdRow: function (row, data, index) {
                $('#appLogTable tbody').css('cursor', 'pointer');

            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.app.log.dt') }}",
                type: "post",
                data: function (d) {

                },
                error: function () {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                }
            },
            "aoColumns": [{
                data: null,
                orderable: false,
            }, {
                data: 'id',
                name: 'id',
                visible: false
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
            }
            ],
            "columnDefs": [{
                "targets": 0,
                visible: false,
                render: function (type, row, data, meta) {
                    return `<div class="form-check">
                                            <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` +
                        data.id + `">
                                </div>`;
                }
            }, {
                    "targets": 2,
                    render: function (type, row, data, meta) {
                        return data.title;
                    }
                },

                {
                    "targets": 3,
                    render: function (type, row, data, meta) {
                        return data.type;

                    }
                },{
                    "targets": 4,
                    render: function (type, row, data, meta) {
                        return data?.user?.full_name;

                    }
                },
               {
                    "targets": 5,
                    render: function (type, row, data, meta) {
                            return moment(data.created_at).format('DD/MM/YYYY hh:mm:ss A');
                    }
                },
                {
                    targets: 6,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        return `<button class="btn btn-sm btn-soft-info" onclick="viewData(`+data.id+`)">View</button>`
                    }
                }
            ]
        });

function viewData(id){
    $.ajax({
        url: "{{ route("admin.app.log.get") }}",
        type: "get",
        data: {id: id},
        dataType: "json",
        success: function (result) {
            if (result.status == '1') {
                $("#log_description").html(result.data.description);
                $("#log_title").html(result.data.title);
                $("#log_type").html(result.data.type);
                $("#log_created_at").html(moment(result.data.created_at).format('DD/MM/YYYY hh:mm:ss A'));
                $("#viewModal").modal("show");

            } else {
                showToastify(result.message, "error");
            }
        }
    });
}

        function updateMultiOption() {
            var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
            console.log(countCheckedCheckboxes);
            $('#bulk_leads_count').html(countCheckedCheckboxes);
            $('#count_selected').html(countCheckedCheckboxes);
            if (countCheckedCheckboxes >= 1) {
                if ($('.multi_options').length === 0) {
                    $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">

                    @can('Property Shoot Multi Delete')
                    <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
                    @endcan
                    </div>
                </div>
           </div>`).insertAfter('.dt-buttons');
                }
            } else {
                $('.multi_options').remove();

            }
        }

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
            updateMultiOption();
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
                    viewData(data.id);
                    highlightRow($(this));

                }
            } // end if
        }); // table row click



        $("#sidebarNotifications").addClass('collapse show');
        $("#sidebarNotiActive").addClass('active');
        $("#sidebarAppLogs").addClass('active');
    </script>
@endsection
