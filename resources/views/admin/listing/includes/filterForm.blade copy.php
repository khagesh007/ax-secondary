<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Advanced Filters</h6>
                </div>

                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            @include('admin.filters._agent')

                            @include('admin.filters._general_users',['id'=>'filter_telesales','filter_id'=>'filter_telesales_operator','label'=>'Telesales','agents'=>$telesales ?? []])


                            <div class="row mt-2">
                                <div class="col-4"><label for="search_unit_no" class="form-label"> Owners</label>
                                    <select name='search_owners_operator' id="search_owners_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select name='search_owners' id="search_owners"
                                        class="form-control select2 search-inputs searchInput" multiple>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-4"><label for="search_unit_no" class="form-label">
                                        Region</label>
                                    <select name='search_regions_operator' id="search_regions_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select name='search_regions' id="search_regions"
                                        class="form-control select2 search-inputs searchInput" multiple>
                                        @foreach ($regions ?? [] as $region)
                                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-4"><label for="search_communities_operator" class="form-label">
                                        Communities</label>
                                    <select name='search_communities_operator' id="search_communities_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select name='search_communities' id="search_communities"
                                        class="form-control select2 search-inputs searchInput" multiple>
                                        @foreach ($communities ?? [] as $community)
                                            <option value="{{ $community->id }}">{{ $community->path_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4"><label for="search_unit_no" class="form-label">Locations
                                    </label>
                                    <select name='search_locations_operator' id="search_locations_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <input type="hidden" class="search-inputs" name="sub_communities"
                                        id="search_sub_communities">
                                    <input type="hidden" class="search-inputs" name="search_properties"
                                        id="search_properties">
                                    <select name='search_locations' id="search_locations"
                                        class="form-control select2 search-inputs searchInput" multiple></select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_beds" class="form-label"> Beds</label>
                                    <select name='search_beds_operator' id="search_beds_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput" id="search_beds"
                                        name="search_beds" multiple>
                                        @foreach ($beds ?? [] as $bed)
                                            <option value="{{ $bed->id }}">{{ $bed->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_baths" class="form-label"> Baths</label>
                                    <select name='search_baths_operator' id="search_baths_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput" id="search_baths"
                                        name="search_baths" multiple>
                                        @foreach ($baths ?? [] as $bath)
                                            <option value="{{ $bath->id }}">{{ $bath->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_type" class="form-label"> Unit Type</label>

                                    <select name='search_type_operator' id="search_type_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="like">contains</option>
                                        <option value="ilike">doesn't contain</option>
                                        {{-- <option value="starts with">starts with</option>
                                        <option value="ends with">ends with</option> --}}
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>

                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control search-inputs searchInput"
                                        id="search_type" name="search_type" placeholder="Enter unit type">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_unit_no" class="form-label"> Unit No</label>
                                    <select name='search_unit_no_operator' id="search_unit_no_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="like">contains</option>
                                        <option value="ilike">doesn't contain</option>
                                        {{-- <option value="starts with">starts with</option>
                                        <option value="ends with">ends with</option> --}}
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control search-inputs searchInput"
                                        id="search_unit_no" name="search_unit_no" placeholder="Enter unit #">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4">
                                    <label for="search_rera_permit_no_operator" class="form-label"> Permit No </label>
                                    <select name='search_rera_permit_no_operator' id="search_rera_permit_no_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="like">contains</option>
                                        <option value="ilike">doesn't contain</option>
                                        {{-- <option value="starts with">starts with</option>
                                        <option value="ends with">ends with</option> --}}
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <input type="text" class="form-control search-inputs searchInput"
                                        id="search_rera_permit_no" name="search_rera_permit_no"
                                        placeholder="Enter Permit #">
                                </div>
                            </div>
                            <div class="row mt-2 mb-4">
                                <div class="col-lg-4">

                                    <label for="search_permit" class="form-label">Permit</label>
                                </div>
                                <div class="col-lg-8">
                                  <select class="form-control search-inputs searchInput"
                                            id="search_permit" name="search_permit">
                                      <option value="" selected disabled>Select Permit</option>
                                           <option value="VALID">Valid</option>
                                           <option value="EXPIRED">Expired</option>
                                           <option value="EMPTY">Empty</option>
                                           <option value="NOT_EMPTY">Not Empty</option>
                                        </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_property_statuses" class="form-label"> Property
                                        Statuses</label>
                                    <select name='search_property_statuses_operator'
                                        id="search_property_statuses_operator" class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        id="search_property_statuses" name="search_property_statuses" multiple>
                                        @foreach ($status ?? [] as $st)
                                            <option value="{{ $st->id }}">{{ $st->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_for" class="form-label"> Categories</label>
                                    <select name='search_for_operator' id="search_for_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput" id="search_for"
                                        name="search_for" multiple>
                                        @foreach (\App\Models\Property::FOR_SELECT ?? [] as $for)
                                            <option value="{{ $for }}">{{ $for }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4"><label for="search_unit_no" class="form-label"> State </label>
                                    <select name='search_state_operator' id="search_state_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select name='search_state' id="search_state"
                                        class="form-control select2 search-inputs searchInput">
                                        <option></option>
                                        <option value="PENDING">Pending</option>
                                        <option value="APPROVED">Approved</option>
                                        <option value="REFUSED">Refused</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4"><label for="search_unit_no" class="form-label"> Portals</label>
                                    <select name='search_portals_operator' id="search_portals_operator"
                                        class="form-control  operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select name='search_portals' id="search_portals"
                                        class="form-control select2 search-inputs searchInput" multiple>
                                        @foreach ($portals as $portal)
                                            <option value="{{ $portal->id }}">{{ $portal->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_categories" class="form-label"> Types</label>
                                    <select name='search_categories_operator' id="search_categories_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        id="search_categories" name="search_categories" multiple>
                                        @foreach ($categories ?? [] as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-4"><label for="search_unit_no" class="form-label"> Statuses</label>
                                    <select name='search_publish_statuses_operator'
                                        id="search_publish_statuses_operator" class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="notEqual">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select name='search_publish_statuses' id="search_publish_statuses"
                                        class="form-control select2 search-inputs searchInput" multiple>
                                        @foreach (\App\Models\Property::PUBLISH_STATUS_SELECT ?? [] as $key => $value)
                                            {{-- @if ( $key == 'ARCHIVED' && auth()->user()->hasRole(['Admin', 'Super Admin'])) --}} --}}
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            {{-- @endif --}}
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_property_request_statuses" class="form-label">Request Status</label>
                                    <select name='search_property_request_statuses_operator'
                                        id="search_property_request_statuses_operator" class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        id="search_property_request_statuses" name="search_property_request_statuses" multiple>
                                        <option value="UNPUBLISHED">UNPUBLISHED</option>
                                        <option value="POCKET">POCKET</option>
                                        <option value="PUBLISHED">PUBLISHED</option>
                                    </select>
                                </div>
                            </div>


                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_property_plot_size" class="form-label">Plot Size</label>
                                    <select name='search_property_plot_size_operator'
                                        id="search_property_plot_size_operator" class="form-control operator-select">
                                        <option value="in_range" selected>between</option>
                                        <option value="not_in_range">not between</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control search-inputs searchInput" id="search_property_plot_size_from" name="search_property_plot_size_from" placeholder="Plot Size">

                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control search-inputs searchInput" id="search_property_plot_size_to" name="search_property_plot_size_to" placeholder="Plot Size">

                                </div>
                            </div>


                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_price" class="form-label">Price From</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 inputsCriteria search-inputs searchInput" id="search_price_from"
                                        name="search_price_from">
                                        <option></option>
                                        <option value="10000">10,000</option>
                                        <option value="20000">20,000</option>
                                        <option value="30000">30,000</option>
                                        <option value="40000">40,000</option>
                                        <option value="50000">50,000</option>
                                        <option value="60000">60,000</option>
                                        <option value="850000">85,000</option>
                                        <option value="110000">110,000</option>
                                        <option value="135000">135,000</option>
                                        <option value="160000">160,000</option>
                                        <option value="185000">185,000</option>
                                        <option value="210000">210,000</option>
                                        <option value="235000">235,000</option>
                                        <option value="260000">260,000</option>
                                        <option value="285000">285,000</option>
                                        <option value="310000">310,000</option>
                                        <option value="335000">335,000</option>
                                        <option value="360000">360,000</option>
                                        <option value="410000">410,000</option>
                                        <option value="460000">460,000</option>
                                        <option value="510000">510,000</option>
                                        <option value="560000">560,000</option>
                                        <option value="610000">610,000</option>
                                        <option value="660000">660,000</option>
                                        <option value="710000">710,000</option>
                                        <option value="760000">760,000</option>
                                        <option value="810000">810,000</option>
                                        <option value="860000">860,000</option>
                                        <option value="910000">910,000</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_price" class="form-label">Price To</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 inputsCriteria search-inputs searchInput" id="search_price_to"
                                        name="search_price_to">
                                        <option></option>
                                        <option value="50000">50,000</option>
                                        <option value="60000">60,000</option>
                                        <option value="850000">85,000</option>
                                        <option value="110000">110,000</option>
                                        <option value="135000">135,000</option>
                                        <option value="160000">160,000</option>
                                        <option value="185000">185,000</option>
                                        <option value="210000">210,000</option>
                                        <option value="235000">235,000</option>
                                        <option value="260000">260,000</option>
                                        <option value="285000">285,000</option>
                                        <option value="310000">310,000</option>
                                        <option value="335000">335,000</option>
                                        <option value="360000">360,000</option>
                                        <option value="410000">410,000</option>
                                        <option value="460000">460,000</option>
                                        <option value="510000">510,000</option>
                                        <option value="560000">560,000</option>
                                        <option value="610000">610,000</option>
                                        <option value="660000">660,000</option>
                                        <option value="710000">710,000</option>
                                        <option value="760000">760,000</option>
                                        <option value="810000">810,000</option>
                                        <option value="860000">860,000</option>
                                        <option value="910000">910,000</option>
                                        <option value="1010000">1,010,000</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_completion_status" class="form-label"> Completion
                                        Status</label>
                                    <select name='search_completion_status_operator'
                                        id="search_completion_status_operator" class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        id="search_completion_status" name="search_completion_status" multiple>
                                        @foreach (\App\Models\Property::COMPLETION_STATUS_SELECT ?? [] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_from" class="form-label">Created At</label>
                                    <select name='search_created_from_operator' id="search_created_from_operator"
                                        class="form-control operator-select">
                                        <option value="on" selected>On</option>
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
                                        id="search_created_from" name="search_created_from"
                                        placeholder="Enter created date range">
                                </div>
                            </div>

                            <div class="row mt-2 mb-4">
                                <div class="col-lg-4">
                                    <label for="search_contract_dates" class="form-label">Contract Duration</label>
                                    <select name='search_contract_dates_operator' id="search_contract_dates_operator"
                                        class="form-control operator-select ">
                                        <option value="on" selected>On</option>

                                        <option value="before_start">Starts Before</option>
                                        <option value="before_end">Ends Before</option>

                                        <option value="after_end">Starts After</option>
                                        <option value="after_start">Ends After</option>
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
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_contract_dates"
                                        name="search_contract_dates" data-range-date="true"
                                        placeholder="Enter Contract Duration">
                                </div>
                            </div>

                            <div class="row mt-2 mb-4">
                                <div class="col-lg-4">
                                    <label for="search_updated_at" class="form-label">Updated at</label>
                                    <select name='search_updated_at_operator' id="search_updated_at_operator"
                                        class="form-control operator-select">
                                        <option value="on" selected>On</option>
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
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_updated_at"
                                        name="search_updated_at" data-range-date="true"
                                        placeholder="Enter Updated Date">
                                </div>
                            </div>



                            <div class="row mt-2 mb-4">
                                <div class="col-lg-4">
                                    <label for="search_updated_at" class="form-label">Updated By</label>
                                    <select name='search_updated_by_operator' id="search_updated_by_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_updated_by' id="search_updated_by" class="form-control search-inputs searchInput">
                                    {{-- @foreach ($all_agents ?? [] as $agent)
                                    <option data-image="{{ $agent->thumbnail }}" data-status="{{ $agent->status }}" value="{{ $agent->id }}"> {{ $agent->first_name . ' ' . $agent->last_name }}</option>
                                    @endforeach --}}
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_property_build_up_area" class="form-label">BUA</label>
                                    <select name='search_property_build_up_area_operator'
                                        id="search_property_build_up_area_operator" class="form-control operator-select">
                                        <option value="in_range" selected>between</option>
                                        <option value="not_in_range">not between</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control search-inputs searchInput" id="search_property_build_up_area_from" name="search_property_build_up_area_from" placeholder="BUA">

                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control search-inputs searchInput" id="search_property_build_up_area_to" name="search_property_build_up_area_to" placeholder="BUA">
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_furnishing" class="form-label">Furnishing</label>
                                    <select name='search_furnishing_operator'
                                        id="search_furnishing_operator" class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                    id="search_furnishing" name="search_furnishing" multiple>
                                    @foreach (\App\Models\Property::FURNISHED_SELECT ?? [] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                </div>

                            </div>


                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_published_date" class="form-label">Published Date</label>
                                    <select name='search_published_date_operator' id="search_published_date_operator"
                                        class="form-control operator-select">
                                        <option value="on" selected>On</option>
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
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_published_date"
                                        name="search_published_date" data-range-date="true"
                                        placeholder="Enter Published Date">
                                </div>
                            </div>

                            <div class="row mt-2 mb-4">
                                <div class="col-lg-4">
                                    <label for="search_rented_until" class="form-label">Rented Until</label>
                                    <select name='search_rented_until_operator' id="search_rented_until_operator"
                                        class="form-control operator-select">
                                        <option value="on" selected>On</option>
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
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_rented_until"
                                        name="search_rented_until" data-range-date="true" placeholder="Rented Until">
                                </div>
                            </div>
                            <div class="row mt-2 mb-4">
                                <div class="col-lg-4">
                                    <label for="search_rera_start" class="form-label">Rera Start</label>
                                    <select name='search_rera_start_operator' id="search_rera_start_operator"
                                        class="form-control operator-select ">
                                        <option value="on" selected>On</option>
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
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_rera_start"
                                        name="search_rera_start" data-range-date="true" placeholder="Rera End Start">
                                </div>
                            </div>

                            <div class="row mt-2 mb-4">
                                <div class="col-lg-4">
                                    <label for="search_rera_end" class="form-label">Rera End</label>
                                    <select name='search_rera_end_operator' id="search_rera_end_operator"
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
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_rera_end"
                                        name="search_rera_end" data-range-date="true" placeholder="Rera End Date">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_next_available_from" class="form-label"> Next Available</label>
                                    <select name='search_next_available_from_operator'
                                        id="search_next_available_from_operator" class="form-control operator-select">
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
                                        data-provider="flatpickr" data-date-format="Y-m-d"
                                        id="search_next_available_from" name="search_next_available_from"
                                        data-range-date="true" placeholder="Enter next available from date">
                                </div>
                            </div>

                            <div class="row text-end mt-2">


                                <hr>
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox"
                                            name="search_without_contract" role="switch"
                                            id="search_without_contract">
                                        <label class="form-check-label" for="search_without_contract">Listings Without
                                            Contract Dates</label>
                                    </div>
                                </div>
                                <input type="hidden" name="search_is_live" id="search_is_live">
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox"
                                            name="search_inactive" role="switch" id="search_inactive">
                                        <label class="form-check-label" for="search_inactive">Show Inactive
                                            Listing</label>
                                    </div>
                                </div>

                                @if (!auth()->user()->hasRole('Agent'))
                                    <div class="col-6">
                                        <div class="form-check form-switch form-switch-success">
                                            <input class="form-check-input search-inputs" type="checkbox"
                                                name="search_hot" role="switch" id="search_hot">
                                            <label class="form-check-label" for="search_hot">Show Hot</label>
                                        </div>
                                    </div>
                                    @if (auth()->user()->hasRole(['Admin', 'Super Admin']))
                                        <div class="col-6">
                                            <div class="form-check form-switch form-switch-success">
                                                <input class="form-check-input search-inputs" type="checkbox"
                                                    name="search_deleted" role="switch" id="search_deleted">
                                                <label class="form-check-label" for="search_deleted">Show
                                                    Deleted</label>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="mt-4   align-items-center gap-2 text-end">
                                <button type="submit" class="btn btn-primary btn-sm" id="searchSearchBtn">
                                    <i class="ri-search-line align-bottom me-1 action-search"></i> Search
                                </button>

                                <a href="javascript:void(0)" class="btn btn-sm text-warning btn-primary action-clear"
                                    id="clearSearchBtn">Clear</a>
                                <a href="javascript:void(0)" type="button"
                                    class="btn btn-sm text-danger action-close"
                                    onclick="toggleAdvanceSearch();">Close</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


@section('pagejs')
    <script>
        $(document).on('change', '.operator-select', function() {
            var operator = $(this).val();
            var input = $(this).closest('.row').find('.searchInput');
            var select2Element = input.next('.select2-container').find('select');
            var operators = ["null", "not_null", "today", "yesterday", "last_week", "last_month", "this_month",
                "this_week"
            ];
            if (operators.includes(operator)) {
                select2Element.prop('disabled', true);
                input.prop('disabled', true);
            } else {
                select2Element.prop('disabled', false);
                input.prop('disabled', false);
            }
        });
    </script>
@endsection
