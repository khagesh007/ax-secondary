<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4">Agents</div>
                                <div class="col-8">
                                    <select name='search_agents' id="search_agents" class="form-control select2 search-inputs" multiple>
                                        @foreach ($agents ?? [] as $agent)
                                            <option
                                                value="{{ $agent->id }}">{{$agent->first_name." ".$agent->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2 d-none">
                                <div class="col-4">Transaction Types</div>
                                <div class="col-8">
                                    <select name='search_transaction_types' id="search_transaction_types"
                                            class="form-control select2 search-inputs" multiple>
                                        @foreach ($transactionTypes ?? [] as $transactionType)
                                            <option
                                                value="{{ $transactionType->id }}">{{ $transactionType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_leads" class="form-label"> Tenants/Buyers</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_leads' id="search_leads"
                                            class="form-control select2 search-inputs" multiple>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_contacts" class="form-label"> Landlords/Sellers</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_contacts' id="search_contacts"
                                            class="form-control select2 search-inputs" multiple>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4">Deal Types</div>
                                <div class="col-8">
                                    <select name='search_deal_types' id="search_deal_types"
                                            class="form-control select2 search-inputs" multiple>
                                        <option value="Secondary Sale">Secondary Sale</option>
                                        <option value="Primary Resale">Primary Resale</option>
                                        <option value="Rental">Rental</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_deal_statuses" class="form-label"> Deal Statuses</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_deal_statuses' id="search_deal_statuses"
                                            class="form-control select2 inputs" multiple>
                                       <option value="DRAFT">Draft</option>
                                       <option value="PENDING_APPROVAL">Pending Approval</option>
                                       <option value="APPROVED">Approved</option>
                                       <option value="REJECTED">Rejected</option>
                                       <option value="COMPLETED">Completed</option>
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
                            @if(auth()->user()->hasRole(["Admin","Super Admin"]))
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox" name="search_deleted" role="switch" id="search_deleted">
                                        <label class="form-check-label"
                                               for="search_deleted">Show Deleted</label>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="mt-4 d-flex justify-content-start align-items-center gap-2">
                                    <button type="submit" class="btn  btn-sm btn-primary action-search" id="searchSearchBtn">
                                        <i class="ri-search-line align-bottom me-1"></i> Search
                                    </button>
                                    <a href="javascript:void(0)" class="btn btn-sm text-warning action-clear" id="clearSearchBtn">Clear</a>
                                    <a href="javascript:void(0)" type="button" class="btn btn-sm text-danger action-close" onclick="toggleAdvanceSearch();">Close</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
