<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
           

            <div class="card ">
              
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4">Agents</div>
                                <div class="col-8">
                                    <select name='search_agents' id="search_agents" class="form-control select2 search-inputs" multiple>
                                        @foreach ($agents ?? [] as $agent)
                                            <option
                                                value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4">Owners</div>
                                <div class="col-8">
                                    <select name='search_owners' id="search_owners" class="form-control select2 search-inputs" multiple>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_categories" class="form-label"> Categories</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs" id="search_categories"
                                            name="search_categories" multiple>
                                        @foreach ($categories ?? [] as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_beds" class="form-label"> Beds</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs" id="search_beds"
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
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs" id="search_baths"
                                            name="search_baths" multiple>
                                        @foreach ($baths ?? [] as $bath)
                                            <option value="{{ $bath->id }}">{{ $bath->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_type" class="form-label"> Type</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control search-inputs" id="search_type" name="search_type" placeholder="Search unit type">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_unit_no" class="form-label"> Unit No</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control search-inputs" id="search_unit_no" name="search_unit_no" placeholder="Search unit no">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4">Communities</div>
                                <div class="col-8">
                                    <select name='search_communities' id="search_communities" class="form-control select2 search-inputs" multiple>
                                        @foreach ($communities ?? [] as $comm)
                                            <option value="{{ $comm->id }}">{{ $comm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4">Sub Communities</div>
                                <div class="col-8">
                                    <select name='search_sub_communities' id="search_sub_communities" class="form-control select2 search-inputs" multiple></select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_properties" class="form-label"> Properties</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs" id="search_properties"
                                            name="search_properties" multiple>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_property_statuses" class="form-label"> Property Statuses</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs" id="search_property_statuses"
                                            name="search_property_statuses" multiple>
                                        @foreach ($status ?? [] as $st)
                                            <option value="{{$st->id}}">{{$st->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_next_available_from" class="form-label"> Next Available From</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs" data-provider="flatpickr"
                                           data-date-format="Y-m-d" id="search_next_available_from" name="search_next_available_from">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_next_available_to" class="form-label"> Next Available To</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs" data-provider="flatpickr"
                                           data-date-format="Y-m-d" id="search_next_available_to" name="search_next_available_to">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-lg-4">
                                    <label for="search_price" class="form-label">Price</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control search-inputs" id="search_price" name="search_price" placeholder="Search Price">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4">Statuses</div>
                                <div class="col-8">
                                    <select name='search_publish_statuses' id="search_publish_statuses" class="form-control select2 search-inputs" multiple>
                                        @foreach (\App\Models\Property::PUBLISH_STATUS_SELECT ?? [] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4">Portals</div>
                                <div class="col-8">
                                    <select name='search_portals' id="search_portals" class="form-control select2 search-inputs" multiple>
                                        @foreach ($portals as $portal)
                                            <option value="{{ $portal->id }}">{{ $portal->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_featured" class="form-label"> Featured</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs" id="search_featured"
                                            name="search_featured">
                                        <option value="">Search Featured</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_from" class="form-label">Created From</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs" data-provider="flatpickr"
                                           data-date-format="Y-m-d" id="search_created_from" name="search_created_from">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Created To</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs" data-provider="flatpickr"
                                           data-date-format="Y-m-d" id="search_created_to" name="search_created_to">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox" name="search_inactive" role="switch" id="search_inactive">
                                        <label class="form-check-label"
                                               for="search_inactive">Show Inactive Listing</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox" name="search_hot" role="switch" id="search_hot">
                                        <label class="form-check-label"
                                               for="search_hot">Show Hot</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox" name="search_deleted" role="switch" id="search_deleted">
                                        <label class="form-check-label"
                                               for="search_deleted">Show Deleted</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-start align-items-center gap-2">
                                    <button type="submit" class="btn btn-primary" id="searchSearchBtn">
                                        <i class="ri-search-line align-bottom me-1"></i> Search
                                    </button>
                                    <a href="javascript:void(0)" class="text-warning" id="clearSearchBtn">Clear</a>
                                    <a href="javascript:void(0)" type="button" class="text-danger" onclick="toggleAdvanceSearch();">Close</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
