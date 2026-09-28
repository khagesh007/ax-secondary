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

                            @include('admin.filters._general_users',['id'=>'search_managers','filter_id'=>'search_managers_operator','label'=>'Manager','agents'=>$managers ?? []])

                            @include('admin.filters._agent')

                            <div class="row mt-2">
                                <div class="col-4"><label for="search_unit_no" class="form-label">
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
                                <div class="col-4"><label for="search_unit_no" class="form-label">
                                        Sub Communities</label>
                                    <select name='search_sub_communities_operator' id="search_sub_communities_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select name='search_sub_communities' id="search_sub_communities"
                                        class="form-control select2 search-inputs searchInput" multiple>
                                        @foreach ($sub_communities ?? [] as $community)
                                            <option value="{{ $community->id }}">{{ $community->path_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>




                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12 ">
                            <div class="row mt-2 d-none">
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
                                        Community Properties</label>
                                    <select name='search_community_properties_operator' id="search_community_properties_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <select name='search_community_properties' id="search_community_properties"
                                        class="form-control select2 search-inputs searchInput" multiple>
                                        @foreach ($community_properties ?? [] as $community)
                                            <option value="{{ $community->id }}">{{ $community->path_name }}</option>
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
                                    <label for="search_lead_statuses" class="form-label">Statuses</label>
                                    <select name='search_lead_statuses_operator' id="search_lead_statuses_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput" name="search_lead_statuses"
                                        id="search_lead_statuses" multiple>
                                        @foreach ($statuses ?? [] as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
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
                                    <label for="sources" class="form-label"> Source</label>
                                    <select name='search_sources_operator' id="search_sources_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs searchInput" id="search_source"
                                        name="search_source" multiple>
                                        @foreach ($sources ?? [] as $source)
                                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
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
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_created_from"
                                        name="search_created_from" data-range-date="true"
                                        placeholder="Enter Created Date">

                                    {{-- <input type="date" class="form-control search-inputs"
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_created_to"
                                        name="search_created_to" placeholder="Enter create to date"> --}}
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Update Date</label>
                                    <select name='search_date_updated_operator' id="search_date_updated_operator"
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
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_updated_from"
                                        name="search_updated_from" data-range-date="true"
                                        placeholder="Enter updated Date">

                                    {{-- <input type="date" class="form-control search-inputs"
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_created_to"
                                        name="search_created_to" placeholder="Enter create to date"> --}}
                                </div>
                            </div>

                        </div>


                        <div class="col-xl-3 col-md-6 col-sm-12">

                            <div class="row text-end mt-2">


                                <hr>

                                @if (!auth()->user()->hasRole('Agent'))
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
