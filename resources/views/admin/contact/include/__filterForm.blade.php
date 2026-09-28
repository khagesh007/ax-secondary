<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Advanced Filters</h6>
                </div>
                <div class="card-body bg-light">
                    <div class="row" id="filterFinder">
                    <div class="col-xl-4 col-md-6 col-sm-12">

                        <div class="row mt-2">
                            <div class="col-4">
                                <label for="applicator" class="form-label">Apply Filter</label>

                            </div>
                            <div class="col-8">
                                <select class="form-control select2 search-inputs searchInput"
                                    name="applicator" id="applicator" multiple>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 col-sm-12 d-none filtercols" data-name="col_agent">
                        @include('admin.filters._agent')
                    </div>


                    <div class="col-xl-4 col-md-6 col-sm-12 d-none filtercols" data-name="col_bitrix">

                        <div class="row mt-2">
                            <div class="col-4">
                                <label for="search_bitrix_id_operator" class="form-label"> Bitrix ID </label>
                                <select name='search_bitrix_id_operator' id="search_bitrix_id_operator"
                                    class="form-control  operator-select">
                                    <option value="in" selected>is</option>
                                    <option value="not_in">is not</option>
                                    <option value="like">contains</option>
                                    <option value="ilike">doesn't contain</option>
                                    <option value="null">is empty</option>
                                    <option value="not_null">is not empty</option>
                                </select>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control search-inputs searchInput"
                                    id="search_bitrix_id" name="search_bitrix_id" placeholder="Enter Bitrix #">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-sm-12 d-none filtercols" data-name="col_type">

                        <div class="row mt-2">
                            <div class="col-4">
                                <label for="search_contact_types" class="form-label">Contact Types</label>
                                <select name='search_contact_types_operator' id="search_contact_types_operator"
                                    class="form-control operator-select">
                                    <option value="in" selected>is</option>
                                    <option value="not_in">is not</option>
                                    <option value="null">is empty</option>
                                    <option value="not_null">is not empty</option>
                                </select>
                            </div>
                            <div class="col-8">
                                <select class="form-control select2 search-inputs searchInput"
                                    name="search_contact_types" id="search_contact_types" multiple>
                                    @foreach ($types ?? [] as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-sm-12 d-none filtercols" data-name="col_gender">

                        <div class="row mt-2">
                            <div class="col-lg-4">
                                <label for="search_genders" class="form-label"> Gender</label>
                                <select name='search_genders_operator' id="search_genders_operator"
                                    class="form-control operator-select">
                                    <option value="in" selected>is</option>
                                    <option value="not_in">is not</option>
                                    <option value="null">is empty</option>
                                    <option value="not_null">is not empty</option>
                                </select>
                            </div>
                            <div class="col-lg-8">
                                <select class="form-control select2 search-inputs searchInput" name="search_genders"
                                    id="search_genders" multiple>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 col-sm-12 d-none filtercols" data-name="col_source">
                        <div class="row mt-2">
                            <div class="col-lg-4">
                                <label for="search_contact_sources" class="form-label"> Contact Sources</label>
                                <select name='search_contact_sources_operator' id="search_contact_sources_operator"
                                    class="form-control operator-select">
                                    <option value="in" selected>is</option>
                                    <option value="not_in">is not</option>
                                    <option value="null">is empty</option>
                                    <option value="not_null">is not empty</option>
                                </select>
                            </div>
                            <div class="col-lg-8">
                                <select class="form-control select2 search-inputs searchInput"
                                    name="search_contact_sources" id="search_contact_sources" multiple>
                                    @foreach ($sources ?? [] as $src)
                                        <option value="{{ $src->id }}">{{ $src->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-sm-12 d-none filtercols" data-name="col_status">
                        <div class="row mt-2">
                            <div class="col-lg-4">
                                <label for="search_contact_statuses" class="form-label"> Contact Statuses</label>
                                <select name='search_contact_statuses_operator' id="search_contact_statuses_operator"
                                    class="form-control operator-select">
                                    <option value="in" selected>is</option>
                                    <option value="not_in">is not</option>
                                    <option value="null">is empty</option>
                                    <option value="not_null">is not empty</option>
                                </select>
                            </div>
                            <div class="col-lg-8">
                                <select class="form-control select2 search-inputs searchInput"
                                    name="search_contact_statuses" id="search_contact_statuses" multiple>
                                    @foreach ($statuses ?? [] as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-sm-12 d-none filtercols" data-name="col_dob">
                        <div class="row mt-2">
                            <div class="col-lg-4">
                                <label for="search_date_of_birth" class="form-label">Date of Birth</label>
                                <select name='search_date_of_birth_operator' id="search_date_of_birth_operator"
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
                                    id="search_date_of_birth" name="search_date_of_birth"
                                    placeholder="Enter DOB date range">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-sm-12 d-none filtercols" data-name="col_created_at">
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
                    </div>
                    <div class="col-xl-4 col-md-6 col-sm-12 d-none filtercols" data-name="col_updated_at">
                        <div class="row mt-2">
                            <div class="col-lg-4">
                                <label for="search_updated_date" class="form-label">Updated Date</label>
                                <select name='search_updated_date_operator' id="search_updated_date_operator"
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
                                    data-provider="flatpickr" data-range-date="true" data-date-format="Y-m-d"
                                    id="search_updated_date" name="search_updated_date"
                                    placeholder="Enter created date range">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-sm-12 d-none filtercols" data-name="col_crm_id">

                        <div class="row mt-2">
                            <div class="col-lg-4">
                                <label for="search_my_crm_id" class="form-label">My CRM ID</label>
                                <select name='search_my_crm_id_operator' id="search_my_crm_id_operator"
                                    class="form-control operator-select">
                                    <option value="in" selected>is</option>
                                    <option value="not_in">is not</option>
                                    <option value="null">is empty</option>
                                    <option value="not_null">is not empty</option>
                                </select>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control search-inputs searchInput"
                                    id="search_my_crm_id" name="search_my_crm_id" placeholder="Enter My Crm ID">
                            </div>
                        </div>

                        {{-- <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Created To</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs" data-provider="flatpickr"
                                           data-date-format="Y-m-d" id="search_created_to" name="search_created_to" placeholder="Enter created to date">
                                </div>
                            </div> --}}

                        <hr>
                        @if (auth()->user()->hasRole(['Admin', 'Super Admin']))
                            <div class="row">
                                <div class="col-6">

                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox"
                                            name="search_deleted" role="switch" id="search_deleted">
                                        <label class="form-check-label" for="search_deleted">Show Deleted</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="mt-4  gap-2 text-end">
                            <button type="submit" class="btn btn-sm  btn-primary action-search"
                                id="searchSearchBtn">
                                <i class="ri-search-line align-bottom me-1"></i> Search
                            </button>
                            <a href="javascript:void(0)" class="btn btn-sm text-warning action-clear"
                                id="clearSearchBtn">Clear</a>
                            <a href="javascript:void(0)" type="button" class="btn btn-sm text-danger action-close"
                                onclick="toggleAdvanceSearch();">Close</a>
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

$(document).ready(function() {
    $('#filterFinder .filtercols').each(function() {
        var option = $('<option></option>');
        option.val($(this).data('name'));
        option.text($(this).data('name'));
        $('#applicator').append(option);
    });
});

$('#applicator').on('change', function() {
    var selected = $(this).val();
    $('#filterFinder .filtercols').addClass('d-none');
    selected.forEach(function(item) {
        $('#filterFinder .filtercols[data-name="' + item + '"]').removeClass('d-none');
    });
});

    </script>
@endsection
