{{-- <div class="scrollable-container">
    <div class="row sticky-top-duration position-sticky">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-body p-1">
                    <div class="row row-cols-md-3 row-cols-1">

                        <div class="col col-lg">
                            <button
                                class="btn text-truncate {{ request()->duration == 'all' ? 'btn-primary' : 'btn-outline-primary' }} {{ !request()->duration ? 'btn-primary' : '' }} dateFilter btn-sm w-100"
                                value="all">All</button>
                        </div>

                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'today' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="today">Today</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'yesterday' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="yesterday">Yesterday</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'this_month' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="this_month">This
                                Month</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_month' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_month">Last
                                Month</button>
                        </div>


                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'this_week' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="this_week">This
                                Week</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_week' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_week">Last
                                Week</button>
                        </div>

                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_7_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_7_days">Last 7
                                Days</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_14_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_14_days">Last 14
                                Days</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_21_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_21_days">Last 21
                                Days</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_28_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_28_days">Last 28
                                Days</button>
                        </div>

                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_30_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_30_days">Last 30
                                Days</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_60_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_60_days">60
                                Days</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_90_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_90_days">90
                                Days</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_120_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_120_days">4 Months</button>
                        </div>
                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_180_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_180_days">6 Months</button>
                        </div>

                        <div class="col col-lg  ">
                            <button
                                class="btn text-truncate {{ request()->duration == 'last_365_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100"
                                value="last_365_days">1 Year</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div> --}}






<div class="row sticky-top-duration position-sticky">
    <div class="col-md-12">
        <div class="card p-2" style="
        /* background: #e9e9ee; */
        border: none;
        box-shadow: none;
        padding: 5px 5px !important;
    ">
            <div class="card-body p-1" style="overflow: scroll">
                <div class="d-flex flex-nowrap">
                    <div class="me-1" style="min-width: 150px!important">
                        <input type="date" class="form-control-sm form-control" id="datefilters" name="dates" data-provider="flatpickr" data-date-format="d M, Y" data-multiple-date="true" max="{{ now()->format('Y-m-d') }}">
                    </div>
                    @if(request()->duration == 'custom')
                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'custom' ? 'btn-primary' : 'btn-outline-primary' }} {{ !request()->duration ? 'btn-primary' : '' }} dateFilter btn-sm w-100" value="all">Custom</button>
                    </div>
                    @endif
                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'all' ? 'btn-primary' : 'btn-outline-primary' }} {{ !request()->duration ? 'btn-primary' : '' }} dateFilter btn-sm w-100" value="all">All</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'today' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="today">Today</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'yesterday' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="yesterday">Yesterday</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'this_week' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="this_week">This Week</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_week' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_week">Last Week</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'this_month' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="this_month">This Month</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_month' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_month">Last Month</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_7_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_7_days">Last 7 Days</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_14_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_14_days">Last 14 Days</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_21_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_21_days">Last 21 Days</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_28_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_28_days">Last 28 Days</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_30_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_30_days">Last 30 Days</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_60_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_60_days">60 Days</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_90_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_90_days">90 Days</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_120_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_120_days">4 Months</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_180_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_180_days">6 Months</button>
                    </div>

                    <div class="me-1">
                        <button class="btn text-truncate {{ request()->duration == 'last_365_days' ? 'btn-primary' : 'btn-outline-primary' }} dateFilter btn-sm w-100" value="last_365_days">1 Year</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
