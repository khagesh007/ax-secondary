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
                                    <select name='search_type' id="search_type"
                                            class="form-control select2 search-inputs">
                                        <option value="" selected disabled>Select Type</option>
                                        <option value="Standard">Standard Only</option>
                                        <option value="Luxury">Luxury</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_assigned_to" class="form-label"> Assigned To</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_assigned_to' id="search_assigned_to"
                                            class="form-control select2 search-inputs" multiple>
                                        @foreach($photographers ?? [] as $pg)
                                            <option value="{{$pg->id}}" >{{$pg->full_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">

                            <div class="row">
                                <div class="col-lg-4">
                                    <label for="search_approved_by" class="form-label"> Approved By</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_approved_by' id="search_approved_by"
                                            class="form-control select2 search-inputs" multiple>
                                        @foreach ($admins ?? [] as $admin)
                                            <option
                                                value="{{ $admin->id }}">{{ $admin->first_name }} {{ $admin->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_rejected_by" class="form-label"> Rejected By</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_rejected_by' id="search_rejected_by"
                                            class="form-control select2 search-inputs" multiple>
                                        @foreach ($admins ?? [] as $admin)
                                            <option
                                                value="{{ $admin->id }}">{{ $admin->first_name }} {{ $admin->last_name }}</option>
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
                                        @foreach (\App\Models\PropertyShootBooking::STATUS_SELECT ?? [] as $status)
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
                                           data-date-format="Y-m-d" id="search_created_from" name="search_created_from" placeholder="Select created from date">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Created To</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs" data-provider="flatpickr"
                                           data-date-format="Y-m-d" id="search_created_to" name="search_created_to" placeholder="Select created to date">
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
