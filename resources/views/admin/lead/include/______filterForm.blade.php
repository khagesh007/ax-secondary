<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Advanced Filters</h6>
                </div>
                <div class="card-body bg-light">
                   <div class="row">
                    <div class="col-md-4 append-before">
                        <div class="row">
                            <div class="col-12">
                                <select name="" id="applicator" class="form-control select2">
                                    <option value="">Select Filter to Apply</option>
                                    @foreach (config('configurations.filters.Lead') as $key => $value)
                                        <option value="" data-operator="{{ $value['operator'] }}"
                                            data-name="{{ $value['name'] }}" data-label="{{ $value['label'] }}"
                                            data-type="{{ $value['type'] }}"
                                            data-variable="{{ $value['variable'] }}"
                                            data-template="{{ $value['template'] }}">
                                            {{ removeUnderScore($value['label']) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                   </div>
                    <div class="row">

                        {{-- <div class="col-xl-4 col-md-6 col-sm-12"> --}}

                            <div class="row ">
                                <div class="col-4">
                                    <label for="agent" class="form-label">Lead ID</label>
                                    <select name='search_id_operator' id="search_id_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">

                                    <input type="number" class="form-control search-inputs searchInput" id="search_id"
                                        name="id" placeholder="Search ID" value="">
                                </div>
                            </div>
                            @include('admin.filters._general_users',['id'=>'search_managers','filter_id'=>'search_managers_operator','selectType'=>'','label'=>'Manager','agents'=>$managers ?? []])

                            @include('admin.filters._agent')
                            @include('admin.filters._general_users', [
                                'id' => 'previous_agent',
                                'filter_id' => 'previous_agent_operator',
                                'label' => 'Previous Agent',
                                'agents' => $agents ?? [],
                            ])

                            <div class="row mt-2 ">
                                <div class="col-4">
                                    <label for="agent" class="form-label">Property</label>
                                    <select name='property_ref_operator' id="property_ref_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select name='property_ref' id="property_ref"
                                        class="form-control select2 search-inputs searchInput">
                                        <option value="">Search</option>
                                        @foreach ($properties ?? [] as $property)
                                            <option value="{{ $property->id }}">{{ $property->ref_no }}
                                                {{ $property->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- @can('Bulk Telesales Head Assignments')
                                <div class="row mt-2 ">
                                    <div class="col-4">
                                        <label for="agent" class="form-label">Sales Team Leader</label>
                                        <select name='filter_telesales_head_operator' id="filter_telesales_head_operator"
                                            class="form-control operator-select">
                                            <option value="in" selected>is</option>
                                            <option value="not_in">is not</option>
                                            <option value="null">is empty</option>
                                            <option value="not_null">is not empty</option>
                                        </select>
                                    </div>
                                    <div class="col-8">

                                        <select name='filter_telesales_head' id="filter_telesales_head"
                                            class="form-control select2 search-inputs searchInput" multiple>

                                            @foreach ($telesalesheads ?? [] as $head)
                                                <option value="{{ $head->id }}">{{ $head->first_name }}
                                                    {{ $head->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endcan --}}

                            {{-- @can('Bulk Telesales Assignments') --}}

                            @include('admin.filters._general_users', [
                                'id' => 'filter_telesales',
                                'filter_id' => 'filter_telesales_operator',
                                'label' => 'Telesales',
                                'agents' => $telesales ?? [],
                            ])


                            {{-- @endcan --}}
                            <div class="row mt-2 ">
                                <div class="col-4"><label for="lead_for" class="form-label">Lead For</label>
                                    <select name='search_for_operator' id="search_for_operator"
                                        class="form-control   operator-select">
                                        <option value="whereJsonContains" selected>is</option>
                                        <option value="whereJsonNotContains">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select name='search_for' id="search_for"
                                        class="form-control select2 search-inputs searchInput" multiple>
                                        @foreach (\App\Models\Lead::FOR_SELECT ?? [] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2 ">
                                <div class="col-lg-4">
                                    <label for="search_lead_statuses" class="form-label"> Lead Statuses</label>
                                    <select name='search_lead_statuses_operator' id="search_lead_statuses_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        name="search_lead_statuses" id="search_lead_statuses" multiple>
                                        @foreach ($statuses ?? [] as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2 ">
                                <div class="col-lg-4">
                                    <label for="search_priorities" class="form-label"> Priorities</label>
                                    <select name='search_priorities_operator' id="search_priorities_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        name="search_priorities" id="search_priorities" multiple>
                                        @foreach (\App\Models\Lead::PRIORITY_SELECT ?? [] as $priority)
                                            <option value="{{ $priority }}">{{ $priority }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        {{-- </div> --}}
                        {{-- <div class="col-xl-4 col-md-6 col-sm-12"> --}}

                            <div class="row mt-2 ">
                                <div class="col-4">
                                    <label for="lead_sources" class="form-label">Lead Sources</label>
                                    <select name='search_lead_sources_operator' id="search_lead_sources_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        name="search_lead_sources" id="search_lead_sources" multiple>
                                        @foreach ($sources ?? [] as $src)
                                            <option value="{{ $src->id }}">{{ $src->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2 ">
                                <div class="col-lg-4">
                                    <label for="search_lead_medium" class="form-label">Lead Medium</label>
                                    <select name='search_lead_medium_operator' id="search_lead_medium_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        name="search_lead_medium" id="search_lead_medium" multiple>
                                        @foreach ($mediums ?? [] as $src)
                                            <option value="{{ $src->id }}">{{ $src->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2 ">
                                <div class="col-lg-4">
                                    <label for="search_lead_types" class="form-label">Lead Types</label>
                                    <select name='search_lead_types_operator' id="search_lead_types_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        name="search_lead_types" id="search_lead_types" multiple>

                                        @foreach ($lead_types ?? [] as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2 ">
                                <div class="col-lg-4">
                                    <label for="search_genders" class="form-label"> Gender</label>
                                    <select name='search_genders_operator' id="search_genders_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control search-inputs select2 searchInput"
                                        name="search_genders" id="search_genders" multiple>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2 ">
                                <div class="col-lg-4">
                                    <label for="search_nationalities" class="form-label"> Nationality</label>
                                    <select name='search_nationalities_operator' id="search_nationalities_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        name="search_nationalities" id="search_nationalities" multiple>

                                        @foreach ($countries ?? [] as $country)
                                            <option value="{{ $country->id }}">{{ $country->nationality }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2 ">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Create Date</label>
                                    <select name='search_date_created_operator' id="search_date_created_operator"
                                        class="form-control   operator-select">
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
                                        data-provider="flatpickr" data-date-format="Y-m-d" data-range-date="true" id="search_created_from"
                                        name="search_created_from"
                                        placeholder="Enter Created Date">

                                    {{-- <input type="date" class="form-control search-inputs"
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_created_to"
                                        name="search_created_to" placeholder="Enter create to date"> --}}
                                </div>
                            </div>

                            <div class="row mt-2 ">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Follow Up Date</label>
                                    <select name='search_date_followupdate_operator'
                                        id="search_date_followupdate_operator" class="form-control   operator-select">
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
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_follow_up_date"
                                        name="search_follow_up_date" data-range-date="true"
                                        placeholder="Enter Follow Up Date">

                                    {{-- <input type="date" class="form-control search-inputs"
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_created_to"
                                        name="search_created_to" placeholder="Enter create to date"> --}}
                                </div>
                            </div>
                            <div class="row mt-2 ">
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
                            <div class="row mt-2 ">
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

                        {{-- </div>
                        <div class="col-xl-4 col-md-6 col-sm-12"> --}}


                            <div class="row mt-2 ">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Bitrix Data</label>
                                    <select name='search_bitrix_data_operator' id="search_bitrix_data_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="like">contain</option>
                                        <option value="ilike">does not contain</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control search-inputs searchInput"
                                        id="search_bitrix_data" name="search_bitrix_data"
                                        placeholder="Enter keyword">
                                </div>
                            </div>
                            <div class="row mt-2 ">
                                <div class="col-4">
                                    <label for="filter_communities" class="form-label">Communities</label>
                                    <select name='filter_communities_operator' id="filter_communities_operator"
                                        class="form-control   operator-select">
                                        <option value="hasCommunities" selected>is</option>
                                        <option value="hasNotCommunities">is not</option>
                                        {{-- <option value="null">is empty</option>
                                    <option value="not_null">is not empty</option> --}}
                                    </select>

                                </div>
                                <div class="col-8">
                                    <select class="form-control search-inputs searchInput" multiple
                                        id="filter_communities" name="filter_communities"></select>
                                </div>
                            </div>

                            <div class="row mt-2 ">
                                <div class="col-4">
                                    <label for="filter_locations" class="form-label">Sub Communities</label>
                                    <select name='filter_sub_communities_operator'
                                        id="filter_sub_communities_operator" class="form-control   operator-select">
                                        <option value="hasSubCommunities" selected>is</option>
                                        <option value="hasNotSubCommunities">is not</option>
                                        {{-- <option value="null">is empty</option>
                                    <option value="not_null">is not empty</option> --}}
                                    </select>
                                </div>
                                <div class="col-8">
                                    <input type="hidden" name="filter_locations" id="filter_locations">
                                    <select class="select2  search-inputs searchInput" multiple
                                        id="filter_sub_communities" name="filter_sub_communities"></select>
                                </div>
                            </div>

                            <div class="row mt-2 ">
                                <div class="col-4">
                                    <label for="filter_locations" class="form-label">Beds</label>
                                </div>
                                <div class="col-4">
                                    <select class="form-control  search-inputs select2" name="filter_min_bed_id"
                                        id="filter_min_bed_id">
                                        <option value=""></option>
                                        @foreach ($beds as $bed)
                                            <option value="{{ $bed->id }}">{{ $bed->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select class="form-control search-inputs select2" name="filter_max_bed_id"
                                        id="filter_max_bed_id">
                                        <option value=""></option>

                                        @foreach ($beds as $bed)
                                            <option value="{{ $bed->id }}">{{ $bed->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2 ">
                                <div class="col-4">
                                    <label for="filter_locations" class="form-label">Baths</label>
                                </div>
                                <div class="col-4">
                                    <select class="form-control search-inputs select2" name="filter_min_bath_id"
                                        id="filter_min_bath_id">
                                        <option value=""></option>

                                        @foreach ($baths as $bath)
                                            <option value="{{ $bath->id }}">{{ $bath->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select class="form-control search-inputs select2" name="filter_max_bath_id"
                                        id="filter_max_bath_id">
                                        <option value=""></option>

                                        @foreach ($baths as $bath)
                                            <option value="{{ $bath->id }}">{{ $bath->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2 ">
                                <div class="col-4">
                                    <label for="filter_furnished" class="form-label">Furnished</label>
                                    <select name='filter_furnished_operator' id="filter_furnished_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select class="form-control select2 search-inputs searchInput"
                                        id="filter_furnished" name="filter_furnished">
                                        <option value="" selected disabled>Select</option>
                                        <option value="FURNISHED">Furnished</option>
                                        <option value="SEMI_FURNISHED">Semi Furnished</option>
                                        <option value="UNFURNISHED">Unfurnished</option>
                                    </select>
                                </div>
                            </div>


                            <div class="row mt-2 ">
                                <div class="col-4">
                                    <label for="filter_min_price" class="form-label">Price</label>

                                </div>
                                <div class="col-4">

                                    <select class="form-control" id="filter_min_price" name="filter_min_price">
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
                                <div class="col-4">
                                    <select class="form-control" id="filter_max_price" name="filter_max_price">
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

                            <div class="row mt-2  text-end">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-6">

                                        </div>
                                        <div class="col-6">
                                            <div class="form-check form-switch form-switch-success">
                                                <input class="form-check-input search-inputs" type="checkbox"
                                                    name="search_my_data" role="switch" id="search_my_data">
                                                <label class="form-check-label" for="search_my_data">Filter My
                                                    Data</label>
                                            </div>
                                        </div>
                                    </div>

                                    @if (auth()->user()->hasRole(['Admin', 'Super Admin']))
                                        <div class="row">
                                            <div class="col-6">

                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-success">
                                                    <input class="form-check-input search-inputs" type="checkbox"
                                                        name="search_deleted" role="switch" id="search_deleted">
                                                    <label class="form-check-label" for="search_deleted">Show Deleted
                                                        Also</label>
                                                </div>
                                                <div class="form-check form-switch form-switch-success">
                                                    <input class="form-check-input search-inputs" type="checkbox"
                                                        name="search_archive" role="switch" id="search_archive">
                                                    <label class="form-check-label" for="search_archive">Show Archived
                                                        Also</label>
                                                </div>
                                                <div class="form-check form-switch form-switch-success">
                                                    <input class="form-check-input search-inputs" type="checkbox"
                                                        name="search_inactive_agents" role="switch"
                                                        id="search_inactive_agents">
                                                    <label class="form-check-label" for="search_inactive_agents">Show
                                                        Inactive Agents Data</label>
                                                </div>




                                            </div>
                                        </div>
                                    @endif
                                    <div class="mt-4 align-items-center gap-2 text-end">
                                        <button type="submit" class="btn btn-sm  btn-primary action-search me-3"
                                            id="searchSearchBtn">
                                            <i class="ri-search-line align-bottom me-1"></i> Search
                                        </button>
                                        <a href="javascript:void(0)" class="btn-sm btn text-warning action-clear me-3"
                                            id="clearSearchBtn">Clear</a>
                                        <a href="javascript:void(0)" type="button"
                                            class="btn btn-sm text-danger action-close"
                                            onclick="toggleAdvanceSearch();">Close</a>
                                    </div>
                                </div>
                            </div>

                        {{-- </div> --}}

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
