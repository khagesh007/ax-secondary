@extends('admin.layouts.master')
@section('title')
    Listing Reports
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="card-body p-0">
                    <div class="row row-cols-md-3 row-cols-1">
                        <div class="col col-lg border-end">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Total Listings
                                    {{-- <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i> --}}
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class=" ri-numbers-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $totalProperties }}">{{ $totalProperties }}</span></h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->

                        <div class="col col-lg border-end">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">With Rera
                                    {{-- <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i> --}}
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class=" ri-numbers-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $withRera }}">{{ $withRera }}</span></h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->

                        <div class="col col-lg border-end">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Without Rera
                                    {{-- <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i> --}}
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class=" ri-numbers-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $withoutRera }}">{{ $withoutRera }}</span></h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->

                    </div><!-- end row -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div>

        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="card-body p-0">
                    <div class="row row-cols-md-3 row-cols-1">
                        <div class="col col-lg border-end">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Expired
                                    {{-- <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i> --}}
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class=" ri-numbers-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $expired }}">{{ $expired }}</span></h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg border-end">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Expiring in over 21 days
                                    {{-- <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5> --}}
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h2 class="mb-0"><span class="counter-value"
                                                    data-target="{{ $expiring_21_days }}">{{ $expiring_21_days }}</span>
                                            </h2>
                                        </div>
                                    </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg border-end">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Expiring in over 14 days
                                    {{-- <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5> --}}
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h2 class="mb-0"><span class="counter-value"
                                                    data-target="{{ $expiring_14_days }}">{{ $expiring_14_days }}</span>
                                            </h2>
                                        </div>
                                    </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg border-end">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">
                                    Expiring in 7 days
                                    {{-- <i class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i> --}}
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-pulse-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $expiring_7_days }}">{{ $expiring_7_days }}</span></h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg border-end">
                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">
                                    Expiring in 3 days
                                    {{-- <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i> --}}
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-trophy-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $expiring_3_days }}">{{ $expiring_3_days }}</span></h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col col-lg">
                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">
                                    Expiring Today
                                    {{-- <i class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i> --}}
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-database-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $expiring_today }}">{{ $expiring_today }}</span></h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                    </div><!-- end row -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div>
    </div>
    <div class="row sticky-top-duration position-sticky">
        <div class="col-md-12">
            <div class="card p-2"
                style="
            /* background: #e9e9ee; */
            border: none;
            box-shadow: none;
            padding: 5px 5px !important;
        ">
                <div class="card-body p-1" style="overflow: scroll">
                    <div class="d-flex flex-nowrap">
                        <div class="me-1">
                            <div class="form-check form-check-inline">
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseWithicon2" aria-expanded="false"
                                    aria-controls="collapseWithicon2">
                                    <i class="ri-filter-2-line"></i>
                                </button>
                            </div>
                        </div>

                        {{-- <div class="me-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" checked name="inlineRadioOptions1"
                                    id="inlineRadio2" value="option2">
                                <label class="form-check-label" for="inlineRadio2">Expiring</label>
                            </div>
                        </div>
                        <div class="me-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions1"
                                    id="inlineRadio1" value="option1">
                                <label class="form-check-label" for="inlineRadio1">Expired</label>
                            </div>

                        </div> --}}
                        <div class="me-1" style="min-width: 150px!important">
                            <input type="date" class="form-control-sm form-control" id="datefilters" name="dates"
                                data-provider="flatpickr" data-date-format="d M, Y" data-multiple-date="true"
                                {{-- max="{{ now()->format('Y-m-d') }}" --}}>
                        </div>
                        @if (request()->duration == 'custom')
                            <div class="me-1">
                                <button
                                    class="btn text-truncate {{ request()->duration == 'custom' ? 'btn-primary' : 'btn-outline-primary' }} {{ !request()->duration ? 'btn-primary' : '' }} dateFilter btn-sm w-100"
                                    value="all">Custom</button>
                            </div>
                        @endif
                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'all' ? 'btn-primary' : 'btn-outline-primary' }} {{ !request()->duration ? 'btn-primary' : '' }} dateFilter btn-sm w-100"
                                value="all">All</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'today' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="today">Today</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'yesterday' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="yesterday">Yesterday</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'this_week' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="this_week">This Week</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_week' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_week">Last Week</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'this_month' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="this_month">This Month</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_month' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_month">Last Month</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_7_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_7_days">Last 7 Days</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_14_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_14_days">Last 14 Days</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_21_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_21_days">Last 21 Days</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_28_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_28_days">Last 28 Days</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'next_7_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="next_7_days">Next 7 Days</button>
                        </div>
                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'next_14_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="next_14_days">Next 14 Days</button>
                        </div>

                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'next_21_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="next_21_days">Next 21 Days</button>
                        </div>
                        <div class="me-1">
                            <button
                                class="btn text-truncate {{ request()->duration == 'next_28_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="next_28_days">Next 28 Days</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div class="collapse" id="collapseWithicon2">
                <div class="card mb-0">
                    <div class="card-body">
                        <form id="searchForm">
                            <div class="row">
                                <div class="col-md-4">

                                    <div class="row mb-3">
                                        <div class="col-lg-3">
                                            <label for="listing_type" class="form-label">Listing Type</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="listing_type" id="listing_type" multiple class="form-control select2">
                                                <option value="live">Live</option>
                                                <option value="not_live">Not Live</option>

                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-4">

                                    <div class="row mb-3">
                                        <div class="col-lg-3">
                                            <label for="publish_status" class="form-label">Publish Status</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="publish_status" id="publish_status" multiple
                                                class="form-control select2">
                                                <option value="PUBLISHED">Published</option>
                                                <option value="DRAFT">Draft</option>
                                                <option value="PENDING">Pending</option>
                                                <option value="POCKET">Pocket</option>
                                                <option value="REFUSED">Refused</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-4">

                                    <div class="row mb-3">
                                        <div class="col-lg-3">
                                            <label for="validity" class="form-label">Validity</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="validity" id="validity" multiple class="form-control select2">
                                                <option value="expired">Expired</option>
                                                <option value="not_expired">Not Expired</option>
                                                <option value="invalid">Invalid/Empty</option>

                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-4">

                                    <div class="row mb-3">
                                        <div class="col-lg-3">
                                            <label for="completion_status" class="form-label">Completion Status</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="completion_status" id="completion_status" multiple
                                                class="form-control select2">
                                                @foreach (\App\Models\Property::COMPLETION_STATUS_SELECT ?? [] as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 text-end">
                                            <button class="btn btn-primary btn-sm" type="submit"
                                                id="filterSearch">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body" style="overflow-x: auto;">
                    <table id="dataTable" class="table table-bordered table-striped" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 10px;">
                                    <div class="form-check">
                                        <input class="form-check-input fs-15" type="checkbox" id="checkAll"
                                            value="option">
                                    </div>
                                </th>
                                {{-- <th data-ordering="false">ID.</th> --}}
                                <th>Agent</th>
                                <th>Ref No.</th>
                                <th>Type</th>
                                <th style="width: 20px">Location</th>
                                <th>Status</th>
                                <th>Expring in</th>
                                <th>Rera Status</th>
                                {{-- <th>Rera</th> --}}



                            </tr>
                        </thead>
                        <tbody class="form-check-all">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
@endsection
@section('script')
    @include('admin.partials.datatable')
@section('global_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection
<script src="{{ URL::asset('assets/js/pages/listing.js') }}"></script>
<script>
    $(document).ready(function() {

        $('#datefilters').daterangepicker({
            maxDate: moment(),
            locale: {
                format: 'DD/MM/YYYY'
            }

        });

        $('#publish_status').select2({
            placeholder: "Select",
            allowClear: true,
            closeOnSelect: false

        });

        $('#validity').select2({
            placeholder: "Select Validity Status",
            allowClear: true,
            closeOnSelect: false
        });

        $('#completion_status').select2({
            placeholder: "Select Completion Status",
            allowClear: true,
            closeOnSelect: false
        });
        $('#listing_type').select2({
            placeholder: "Select Listing Nature",
            allowClear: true,
            closeOnSelect: false
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
        $('#_publish_status').change(function() {
            var selectedStatuses = $(this).val();
            var url = new URL(window.location.href);

            if (!selectedStatuses || selectedStatuses.length === 0) {
                url.searchParams.delete('publish_status');
            } else {
                // url.searchParams.set('publish_status', JSON.stringify(selectedStatuses));
                // save in  array format like publish_status=[PUBLISHED,DRAFT,PENDING]
                url.searchParams.set('publish_status', selectedStatuses.join(','));
            }

            var newUrl = url.toString();
            history.pushState(null, '', newUrl);
        });

        let filtersData = [];
        $(document).on("submit", "#searchForm", function(e) {
            e.preventDefault();

            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);
            console.log(filtersData);
            dataTable.ajax.reload();
        })


        // $('#filterSearch').click(function() {
        //     var selectedStatuses = $('#publish_status').val();
        //     var url = new URL(window.location.href);

        //     if (!selectedStatuses || selectedStatuses.length === 0) {
        //         url.searchParams.delete('publish_status');
        //     } else {
        //         // url.searchParams.set('publish_status', JSON.stringify(selectedStatuses));
        //         // save in  array format like publish_status=[PUBLISHED,DRAFT,PENDING]
        //         url.searchParams.set('publish_status', selectedStatuses.join(','));
        //     }

        //     var newUrl = url.toString();
        //     history.pushState(null, '', newUrl);
        //     dataTable.ajax.reload();
        // });


        /*New Datatable*/
        dataTable = $('#dataTable').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [
                [1, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            dom: 'Bfrtip',
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('admin.reports.listings.datatable') }}",
                type: "post",
                data: function(d) {
                    d.duration = "{{ request()->duration }}";
                    d.from = "{{ request()->from }}";
                    d.to = "{{ request()->to }}";
                    d.publish_status = filtersData.publish_status;
                    d.validity = filtersData.validity;
                    d.completion_status = filtersData.completion_status;
                    d.listing_type = filtersData.listing_type;
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
                $('#dataTable tbody').css('cursor', 'pointer');
            },
            columns: [{
                    data: function(data) {
                        return `<div class="form-check"><input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="${data.id}"></div>`;
                    },
                    searchable: false,
                    sortable: false
                },
                // {
                //     data: 'id'
                // },
                {
                    data: function(data) {

                        if (!data.agent_id)
                            return `-`;
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.agent.id}"  title="${data.agent.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.agent.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.agent.first_name}</div>
                                </div>`;
                    },
                    name: 'agent_id',
                    searchable: true
                },
                {
                    data: function(data) {
                        text = data?.for?.replace(/\s/g, "-");
                        return `<span title="` + data.for+
                            `"><a href="javascript:void(0)"  class="font-12 badge badge-border ` +
                            text +
                            `">` + data.ref_no + `</a></span>`;

                    },
                    name: 'ref_no',
                    searchable: true
                },

                {
                    data: function(data) {
                        return data.property_category ? data.property_category.title : "-";
                    },
                    name: 'property_category',
                    searchable: true
                },
                {
                    data: function(data) {
                        if (data.community_property) {
                            let pathName = data.community_property.path_name;
                            return '<p class="datatableLocation mb-0"><strong>' + pathName
                                .split(/,(.+)/)[0] + '</strong>,<br>' + pathName.split(/,(.+)/)[
                                    1] + '</p>';
                        }
                        if (data.sub_community) {
                            let pathName = data.sub_community.path_name;
                            return '<p class="datatableLocation mb-0"><strong>' + pathName
                                .split(/,(.+)/)[0] + '</strong>,<br>' + pathName.split(/,(.+)/)[
                                    1] + '</p>';
                        }

                        return "-";
                    },
                    name: 'property_category',
                    searchable: true
                }, {
                    data: function(data) {
                        let url = "{{ route('admin.listing.index') }}" + "?ref_number=" + data
                            .ref_no;
                        let new_tab =
                            '<a title="Open Listing in New Tab" target="_blank" href="' + url +
                            '"><span class="ri-external-link-line"></span></a>';

                        let publishStatus = createPublishStatusBadge(data.publish_status);
                        let propertyStatus = data.property_status ? data.property_status.title :
                            "";


                        var icons = new Map([
                            ['APPROVED', 'bx bx-check-circle text-success'],
                            ['REFUSED', 'bx bx-x-circle text-danger'],
                            ['PENDING', 'bx bx-hourglass text-warning'],
                        ]);



                        return `<div title="${data.publish_status}" class="d-flex justify-content-between gap-2 align-items-center">
                            <span class="btn btn-soft-${data.publish_status == "PUBLISHED" ? "success" : (data.publish_status == "DRAFT" ? "primary" : data.publish_status == "PENDING" ? "warning" : data.publish_status == "POCKET" ? "secondary" : "danger")} btn-label btn-sm rounded-pill pl-12">
                                ${data.publish_status}${data.is_live ? "<span class='is_live'></span>":""}
                            </span>
                            ${new_tab}
                        </div>`;
                    },
                    name: 'property_category',
                    searchable: true
                },
                {
                    data: function(data) {
                        let start_date = "";
                        let end_date = "";
                        if (data.rera_issue_date) {
                            start_date = data.rera_issue_date;
                            end_date = data.rera_expiry_date;
                        }
                        // if end date is less than today then it is expired
                        if (end_date) {
                            // show in human readable format
                            let diff = moment(end_date).diff(moment(), 'days');
                            if (diff < 0) {
                                return `<div class="d-flex align-items-center">
                                <div class="flex-grow-1 ">${data.rera_permit_number}</div><br>
                                <span class="text-danger">${diff} days</span></div>`;
                            } else {
                                return `<div class="d-flex align-items-center">
                                <div class="flex-grow-1 ">${data.rera_permit_number}</div><br>
                                <span class="bdge text-success ">${diff} days</span></div>`;
                            }
                        }
                        return "-";
                    },
                    name: 'agent_id',
                    searchable: true
                },
                {
                    data: function(data) {
                        let start_date = "";
                        let end_date = "";
                        if (data.rera_issue_date) {
                            start_date = data.rera_issue_date;
                            end_date = data.rera_expiry_date;
                        }
                        // if end date is less than today then it is expired
                        if (end_date) {
                            if (new Date(end_date) < new Date()) {
                                return `<div class="d-flex align-items-center">
                                <div class="flex-grow-1 ">${data.rera_permit_number}</div><br>
                                <span class="text-danger">${start_date} - ${end_date}</span></div>`;
                            } else {
                                return `<div class="d-flex align-items-center">
                                <div class="flex-grow-1 ">${data.rera_permit_number}</div><br>
                                <span class="bdge text-success ">${start_date} - ${end_date}</span></div>`;
                            }
                        }
                        return "-";
                    },
                    name: 'agent_id',
                    searchable: true
                },
                // {
                //     data: function(data) {
                //         if (data.rera_permit_number) {
                //             return `<div class="d-flex align-items-center">
                //             <div class="flex-grow-1"><button class="btn btn-primary btn-sm" data-permit="${data.rera_permit_number}">Revalidate</button></div></div>`;
                //         } else {
                //             return '-';
                //         }

                //     },
                //     name: 'agent_id',
                //     searchable: true
                // },

            ],
        }); // end datatable init


        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
        // checkbox select all functionality

    });
    var checkAll = document.getElementById("checkAll");
    checkAll && (checkAll.onclick = function() {
        var e = document.querySelectorAll('.form-check-all input[name="chk_child"]');
        1 == checkAll.checked ? e.forEach((function(e) {
            e.checked = !0
        })) : e.forEach((function(e) {
            e.checked = !1
        }))
    });


    function updateReraContract() {
        if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                if (1 == e.checked) {
                    var t = e.value;
                    ids_array.push(t)

                }
            })), "undefined" != typeof ids_array && ids_array.length > 0) {
            var content = document.createElement('div');
            content.innerHTML = "You are about to Update <b>" + ids_array.length +
                " Rera Contract(s)</b>. Are you sure you want to Update?";
            Swal.fire({

                    html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/wzwygmng.json" trigger="hover" style="width:250px;height:250px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                        ids_array.length + ' Listings(s)?</p></div></div>',
                    showCancelButton: !0,
                    confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                    confirmButtonText: "Yes, Update " + ids_array.length + " Listings(s).",
                    cancelButtonClass: "btn btn-soft-primary w-xs mb-1",
                    buttonsStyling: !1,
                    showCloseButton: !0


                })
                .then(function(t) {

                    if (t.isConfirmed) { // on confirm delete the category
                        $.ajax({
                            url: "{{ route('admin.reports.listings.updateReraContract') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                ids: ids_array
                            },
                            success: function(result) {
                                if (result.status == '1') {
                                    showToastify(result.message, "success")
                                    $('.multi_options').remove();
                                    dataTable.row().draw();
                                } else {
                                    showToastify(result.message, "error")
                                }
                            },
                            error: function(result) {
                                console.log(result);
                            }
                        });
                    }
                })

        } else {
            showToastify("Please select at least one checkbox", "error")
        }
    }

    function updateMultiOption() {
        var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
        console.log(countCheckedCheckboxes);
        // $('#bulk_leads_count').html(countCheckedCheckboxes);
        // $('#count_selected').html(countCheckedCheckboxes);
        if (countCheckedCheckboxes >= 1) {
            if ($('.multi_options').length === 0) {
                $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">

                    <a class="dropdown-item" href="javascript:;" onclick="updateReraContract()">Update Rera</a>

                    </div>
                </div>
           </div>`).insertAfter('.dt-buttons');
            }
        } else {
            $('.multi_options').remove();

        }
    }
    $("#checkAll").change(function() {
        $(".selectable-checkbox").prop("checked", this.checked)
        updateMultiOption();
    })
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
</script>

@yield('pageDT')
@endsection
