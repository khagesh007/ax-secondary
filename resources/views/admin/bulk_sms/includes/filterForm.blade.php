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
                                    <select name="search_agent_id" id="search_agent_id" class="form-control select2">
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
                                    <label for="Agent" class="form-label">Status</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <select name="search_status" id="search_status" class="form-control select2">
                                        <option value=""></option>
                                        <option value="1">Sent</option>
                                        <option value="0">Failed</option>
                                        <option value="2">Pending</option>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4 mt-2">
                                    <label for="Agent" class="form-label">Confirmed</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <select name="search_confirmation" id="search_confirmation" class="form-control select2">
                                        <option value=""></option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4 mt-2">
                                    <label for="Agent" class="form-label">Batch No</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input name="search_batch_no" placeholder="Search Batch No" id="search_batch_no" class="form-control">
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
