<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4">Requested By</div>
                                <div class="col-8">
                                    <select name='search_requested_by' id="search_requested_by" class="form-control select2 search-inputs" multiple>
                                        @foreach ($agents ?? [] as $agent)
                                            <option
                                                value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4">Types</div>
                                <div class="col-8">
                                    <select name='search_types' id="search_types"
                                            class="form-control select2 search-inputs" multiple>
                                        @foreach (\App\Models\Job::TYPE_SELECT ?? [] as $type)
                                            <option
                                                value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4">Priorities</div>
                                <div class="col-8">
                                    <select name='search_priorities' id="search_priorities"
                                            class="form-control select2 search-inputs" multiple>
                                        @foreach (\App\Models\Job::PRIORITY_SELECT ?? [] as $priority)
                                            <option
                                                value="{{ $priority }}">{{ $priority }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-lg-4">
                                    <label for="search_assigned_to" class="form-label"> Assigned To</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_assigned_to' id="search_assigned_to"
                                            class="form-control select2 search-inputs" multiple>
                                        @foreach ($agents ?? [] as $agent)
                                            <option
                                                value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_approved_by" class="form-label"> Approved By</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_approved_by' id="search_approved_by"
                                            class="form-control select2 search-inputs" multiple>
                                        @foreach ($agents ?? [] as $agent)
                                            <option
                                                value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_statuses" class="form-label"> Statuses</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_statuses' id="search_statuses"
                                            class="form-control select2 search-inputs" multiple>
                                        @foreach (\App\Models\Job::JOB_STATUS_SELECT ?? [] as $status)
                                            <option
                                                value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_from" class="form-label">Created From</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs" data-provider="flatpickr"
                                           data-date-format="Y-m-d" id="search_created_from" name="search_created_from" placeholder="Enter created from date">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Created To</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs" data-provider="flatpickr"
                                           data-date-format="Y-m-d" id="search_created_to" name="search_created_to" placeholder="Enter created to date">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox" name="search_deleted" role="switch" id="search_deleted">
                                        <label class="form-check-label"
                                               for="search_deleted">Show Deleted</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-start align-items-center gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary action-serach" id="searchSearchBtn">
                                        <i class="ri-search-line align-bottom me-1"></i> Search
                                    </button>
                                    <a href="javascript:void(0)" class="btn btn-sm text-warning action-close" id="clearSearchBtn">Clear</a>
                                    <a href="javascript:void(0)" type="button" class="btn btn-sm text-danger action-delete" onclick="toggleAdvanceSearch();">Close</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
