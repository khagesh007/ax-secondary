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
                                    <label for="search_communities" class="form-label">Communities</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <select name="search_communities" multiple id="search_communities"  class="form-control searchInput">
                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4 mt-2">
                                    <label for="search_sub_communities" class="form-label">Sub Communities</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <select name="search_sub_communities" multiple id="search_sub_communities"  class="form-control searchInput">
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4 mt-2">
                                    <label for="search_community_property" class="form-label">Community Property</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" name="search_community_property" id="search_community_property" class="searchInput form-control" placeholder="Search Community Property">

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
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/location/includes/community_property/filterForm.blade.php ENDPATH**/ ?>