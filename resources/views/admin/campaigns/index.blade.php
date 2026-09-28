@extends('admin.layouts.master')
@section('title')
    Campaigns
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
                        <h4 class="card-title mb-0 ">Campaigns</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">SMS</a></li>
                            <li class="breadcrumb-item active">Campaigns</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @include('admin.campaigns.includes.filterForm')

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
                                <th>Campaign Name</th>
                                <th>Campaign SMS</th>
                                <th>Started By</th>
                                <th>Status</th>
                                <th>Approved By</th>
                                <th>Created At</th>


                            </tr>
                        </thead>
                        <tbody class="form-check-all">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>





    @include('admin.campaigns.includes.modals')
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
            url: "{{ route('contacts.sms.campaigns.datatable') }}",
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
            $(`@includeIf('admin.campaigns.includes.filterButtons')`).insertAfter(".dataTables_filter input")




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
                        .batch_no + `"  onclick="viewData('${data.id}')" >` + data.name + `</a>`;

                },
                name: 'name',
                searchable: true
            },
            {
                data: function(data) {
                    if (data.messages == null) {
                        return '-';
                    } else {
                        return data.messages.length;
                    }
                },
                name: 'name',
                searchable: false
            },
            {
                data: function(data) {

                    if (data.created_by == null) {
                        return '-';
                    } else {
                        return data.created_by.full_name
                    }
                },
                name: 'created_by',
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
                            return `<span class="badge bg-danger" title="${moment(data.updated_at).format('DD/MM/YYYY hh:mm:ss')}">Failed/Rejected</span>`;
                        }
                    }
                },
                name: 'status',
                searchable: false
            },
            {
                data: function(data) {

                    if (data.approved_by == null) {
                        return '-';
                    } else {
                        return data.approved_by.full_name
                    }
                },
                name: 'approved_by',
                searchable: true
            },
            {
                data: function(data) {

                    if (data.created_at == null) {
                        return '-';
                    } else {
                        return moment(data.created_at).format('DD/MM/YYYY hh:mm:ss')
                    }
                },
                name: 'created_at',
                searchable: true
            },





        ],
    }); // end datatable init

    function viewData(id) {
        $.ajax({
            type: "POST",
            url: "{{ route('contacts.sms.campaigns.edit') }}",
            data: {
                id: id
            },
            success: function(response) {
                $('#edit-campaign').html(response);
                $('#campaign-update').modal('show');
                sumChar()
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }



    $(document).on("submit", "#update-parameters", function(e) {
        e.preventDefault();
        var formData = new FormData($("#update-parameters")[0]);
        $.ajax({
            url: "{{ route('contacts.sms.campaigns.update') }}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(result) {
                dataTable.ajax.reload();
                showToastify(result.msg, "success")
                $('#campaign-update').modal('hide');
                $('.inputs').val('');
            },
            error: function(jqXhr, json, errorThrown) {

            },
        });

    })

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



    $(document).on('keyup', '.sms-counter', function() {

        sumChar()
    });


function sumChar(){
    var message = $('#message').val();
        $('#msg-preview').html(message);
        var custom_link = $('#custom_link').val();
        var link = `<a href="${custom_link}">${custom_link}</a>`;
        $('#link-preview').html(link);


        var messageLength = message.length;
        var remaining = 160 - messageLength;

        var msgpreview = $('#msg-preview').text();

        var linkpreview = $('#link-preview').text();


        var msgpreviewCount = msgpreview.length;
        var linkpreviewCount = linkpreview.length;
        var totalCharacter = msgpreviewCount + linkpreviewCount;

        $('#total-character').html(totalCharacter);
        $('#total-parts').html(Math.ceil(totalCharacter / 160));

}











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


</script>
@endsection
