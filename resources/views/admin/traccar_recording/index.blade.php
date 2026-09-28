@extends('admin.layouts.master')
@section('title')
    Spot Recordings
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
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

        .scrollable-notes .card-animate:hover {
            box-shadow: 0px 0px 0px 0px rgba(30, 32, 37, .12) !important;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">

        <div class="col-lg-12" id="top-bar">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Traccar</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Traccar</a></li>
                            <li class="breadcrumb-item active">Recordings</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">



                            <button type="button" class="btn btn-light d-none action-close" id="closeBtn">Close</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="collapse slideIn" id="collapseContactForm">
                <div class="">
                    <div class="">
                        <div>

                            @include('admin.traccar_recording.include._form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @includeIf('admin.traccar_recording.include._filterForm')
    <div class="row">
        <div class="col-xxl-12">
            <div class="table-sticky-header-container" id="table-sticky-header">
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link All  statusFilterClear active" data-bs-toggle="tab" id="All" href="#"
                            role="tab" aria-selected="false" tabindex="-1">
                            All
                        </a>
                    </li>

                    @foreach ($types ?? [] as $status)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link All  statusFilter text-uppercase" data-bs-toggle="tab"
                                id="status_id_{{ $status->id }}" href="javascript:void(0)"
                                data-status="{{ $status->id }}" role="tab" aria-selected="false" tabindex="-1">
                                <i class="{{ $status->icon }} me-1"></i>{{ $status->name }}
                            </a>
                        </li>
                    @endforeach

                </ul>
            </div>
            <div class="card" id="contactList">
                <div class="card-body">

                    <table id="datatable" class="table align-middle" style="width:100%">
                        <thead class="table-light2">
                            <tr class="text-uppercase">
                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                            data-com.bitwarden.browser.user-edited="yes">
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Name</th>


                            </tr>
                        </thead>
                        <body>
                            @php
                                $counter = 1;
                            @endphp

                            @foreach ($data as $item)
                                <tr>
                                    <td></td>
                                    <td>{{$counter}}</td>
                                    <td><a href="{{route('admin.traccar.recording.show',$item->user->id)}}">{{$item->user->full_name}}</a></td>
                                </tr>
                                @php
                                    $counter++;
                                @endphp
                            @endforeach
                        </body>

                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('admin.traccar_recording.include._modal')
@endsection
@section('script')
    @include('admin.partials.datatable')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    {{-- <script src=" "></script> --}}

    <script src="{{ URL::asset('/assets/js/plugins/event.js') }}?v={{ time() }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>
    <script>
        var isUpdate = false;
        let mode = 'view';

        function updateMultiOption() {
            var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
            $('#bulk_leads_count').html(countCheckedCheckboxes);
            $('#count_selected').html(countCheckedCheckboxes);
            if (countCheckedCheckboxes >= 1) {
                if ($('.multi_options').length === 0) {
                    $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:;" id="bulk_reassign_btn">Re-Assign</a>
                                <a class="dropdown-item" href="javascript:;">Change Status</a>
                                <div class="dropdown-divider"></div>
                                 @can('Contacts Delete')
                    <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
                    @endcan
                    </div>
                </div>
           </div>`).insertAfter('.dt-button');
                    //    $('#action-delete').removeClass('d-none')
                }
            } else {
                $('.multi_options').remove();
                // $('#action-delete').addClass('d-none')

            }
        }




        $(document).on("click", "#clearSearchBtn", function() {
            $(".search-inputs").val("").trigger("change");
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", "#refreshTableButton", function() {
            $("#searchSearchBtn").trigger("click")
        })

        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }



        $(document).ready(function() {
            var table = $('#contacts-table').DataTable();
            var searchBox = $('.dataTables_filter input');
            searchBox.attr('id', 'mySearchBox');
        });

        $(document).on("click", ".statusFilter", function() {
            let status = $(this).data("status");
            $("#search_contact_types option:selected").removeAttr("selected");
            $("#search_contact_types option[value='" + status + "']").attr("selected", true);
            $("#search_contact_types").trigger("change");
            $("#searchSearchBtn").trigger("click");
        });

        $(document).on("click", ".statusFilterClear", function() {
            $("#search_contact_types option:selected").removeAttr("selected").trigger("change");
            $("#searchSearchBtn").trigger("click");
        });


        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        }

        function toggleForm(show) {
            if (show == undefined) {
                $("#collapseContactForm").toggleClass("show")
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
                return
            }
            if (show) {
                $("#collapseContactForm").addClass("show");
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #bulkAssignBtn").addClass("d-none");
                $("#newBtn").removeClass("d-none");
                $("#collapseContactForm").removeClass("show");
            }
        }



        $(document).on("click", "#closeBtn", function() {
            toggleForm(false)
            mode = 'view';
        })
 /*
        let dataTable = $('#datatable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            'lengthMenu': [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            dom: `<'row'<'col-sm-10 text-left'B> <'col-sm-2 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],
            language: {
                searchPlaceholder: "Search contacts"
            },
            createdRow: function(row, data, index) {
                $('#datatable tbody').css('cursor', 'pointer');

            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.traccar.recording.dt') }}",
                type: "post",
                data: function(d) {
                    d.agents_operator = filtersData?.search_agents_operator;
                    d.agents = filtersData?.search_agents;
                },
                error: function() {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                }
            },
            fnInitComplete: function(oSettings, json) {
                let first = $("#dataTable_wrapper .row div:first");
                first.addClass("col-md-5").removeClass("col-md-6")
                $(first).next().addClass("col-md-7").removeClass("col-md-6")
                $(".dataTables_filter label").addClass(
                    "d-flex justify-between-between align-items-center gap-2")
                $(`@includeIf('admin.contact.include.filterButtons')`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [{
                    data: null,
                    orderable: false,
                }, {
                    data: 'id',
                    name: 'id',
                }, {
                    data: null,
                    name: null,
                    orderable: false,
                }, {
                    data: null,
                    name: null,
                    orderable: false,
                }

            ],
            "columnDefs": [{
                    "targets": 0,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `<div class="form-check">
                                                <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` +
                            data.id + `">
                                            </div>`;
                    }
                },

                {
                    "targets": 2,
                    orderable: false,
                    render: function(type, row, data, meta) {
                        data.user.last_name = data.user.last_name == null ? "" : data.user.last_name;
                        name = data.user.first_name + " " + data.user.last_name;

                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewCoordinate('` +
                            data.id + `')">` + name + `</a>`;
                    }
                },


                {
                    "targets": 3,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        if (data.path) {


                            if (data.path) {
                                return `<audio style="width: 100%" src="${data.path}" controls ></audio>`;
                            } else {
                                return '-';
                            }

                        } else {

                            return '-';
                        }
                    }
                }

            ]
        });
       */
        /*
        dataTable.on('click', 'tbody tr', function(e) {
            var $cell = $(e.target).closest('td'),
                msg;
            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                if ($cell.index() === 2) {

                } else {
                    if (mode == 'create' || mode == 'edit') {
                        var r = confirm("Are you sure you want to discard the changes?");
                        if (r == true) {
                            viewCoordinate(data.id);
                            highlightRow($(this));

                        } else {

                        }
                    } else {
                        viewCoordinate(data.id);
                        highlightRow($(this));
                    }
                }


            } // end if
            else {}


        });
        */


        $(document).on('click', '#editBtn', function(event) {
            @can('Contacts Edit')
                editData($('#id').val());
            @endcan
        });
         /**/






        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
            updateMultiOption();
        })


        // This code is working for sticky navbar
        window.onscroll = function() {
            myFunction()
            scrollFunction();
        };

        // Get the navbar
        var navbar = document.getElementById("top-bar");
        var tableHeader = document.getElementById("table-sticky-header");

        // Get the offset position of the navbar
        var sticky = navbar.offsetTop;

        function myFunction() {
            if (window.pageYOffset >= sticky) {
                navbar.classList.add("sticky-header");
                tableHeader.classList.add("sticky-table-header");
            } else {
                navbar.classList.remove("sticky-header");
                tableHeader.classList.remove("sticky-table-header");
            }
        }




        $(document).on('change', 'tbody tr .selectable-checkbox', function() {
            $(this).parents('tr').toggleClass('selected');
            var checked = $(this).is(':checked');
            if (!checked) {
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
            updateMultiOption();
        });


        $('#sidebarSecurityActive').addClass('active');
        $('#sidebarSecurity').addClass('collapse show');
        $('#sidebarSecurityRecoding').addClass('active');

    </script>
    <script src="{{ URL::asset('/assets/js/pages/contacts.js') }}"></script>
    @yield('pagejs')
@endsection
