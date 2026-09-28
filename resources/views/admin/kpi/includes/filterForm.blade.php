<div class="collapse slideIn" id="collapseFilterForm">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4 mt-2">
                                    <label for="Agent" class="form-label">Agent Name</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <select name="search_agent_id" id="search_agent_id" class="form-control select2 search-inputs">
                                        <option value=""></option>

                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->full_name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4 mt-2">
                                    <label for="start_date" class="form-label">Start date</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="date" data-provider="flatpickr" data-date-format="Y-m-d" data-range-date="true" name="search_start_date" id="search_start_date"
                                        class="form-control search-inputs">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4 mt-2">
                                    <label for="end_date" class="form-label">End date</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="date" data-provider="flatpickr" data-date-format="Y-m-d" data-range-date="true" name="search_end_date" id="search_end_date"
                                        class="form-control search-inputs">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4 mt-2">
                                    <label for="kpi" class="form-label">KPI</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <select name="search_kpi" id="search_kpi" class="form-control select2 search-inputs" multiple>
                                        @include('admin.kpi.includes._kpi_options',['selected' => null])
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row text-end">
                        <div class="col-xl-12 col-md-12 col-sm-12">

                            <div class="mt-4 d-flex justify-content-end align-items-center gap-2">
                                <button type="submit" class="btn btn-sm btn-primary action-search"
                                    id="searchSearchBtn">
                                    <i class="ri-search-line align-bottom me-1"></i> Search
                                </button>
                                <a href="javascript:void(0)" class="btn btn-sm text-warning action-clear"
                                    id="clearSearchBtn">Clear</a>
                                <a href="javascript:void(0)" type="button" class="btn btn-sm text-danger action-close"
                                    onclick="toggleAdvanceSearch();">Close</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
