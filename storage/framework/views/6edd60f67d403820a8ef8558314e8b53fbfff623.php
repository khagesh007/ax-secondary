<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <input type="hidden" name="search_is_live" id="search_is_live">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="card-title mb-0">Advanced Filters</h6>
                        </div>
                        <div class="col-md-3 text-right" style="min-width: 150px">
                            <?php if(auth()->user()->hasRole(['Super Admin'])): ?>
                                
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3 text-end">
                            <div class="btn-group " role="group">
                                <button id="btnGroupDropFilters" type="button"
                                    class="btn btn-sm action-clear dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Filter Actions
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDropFilters">

                                    <li><a class="dropdown-item" id="saveMyFilter"><i
                                                class="ri-save-2-line p-1 m-auto"></i> Save my filter preferences</a>
                                    </li>

                                    <li><a class="dropdown-item" id="clearSaved"><i
                                                class="ri-delete-back-2-line p-1 m-auto"></i> Clear my filter
                                            preferences</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body bg-light">

                    
                    
                    <div class="row d-non" id="append-here">

                        <div class="col-md-4 d-none" id="show_communities">
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label for="search_communities_operator" class="form-label">Communities</label>
                                    <select name='search_communities_operator' id="search_communities_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>

                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>

                                </div>
                                <div class="col-8">
                                    <select class="form-control search-inputs searchInput" multiple
                                        id="search_communities" name="search_communities">
                                        <?php $__currentLoopData = $communities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($community->id); ?>"><?php echo e($community->path_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span data-template="" data-variable="communities" data-operator="contains"
                                        data-name="communities" data-label="communities" data-type="communities"
                                        class="hide-section waves-effect waves-light"><i
                                            class="ri-delete-bin-5-line"></i></span>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 d-none" id="show_locations">
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label for="search_locations_operator" class="form-label">Locations</label>
                                    <select name='search_locations_operator' id="search_locations_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>

                                </div>
                                <div class="col-8">
                                    <input type="hidden" class="search-inputs" name="sub_communities"
                                        id="search_sub_communities">
                                    <input type="hidden" class="search-inputs" name="search_properties"
                                        id="search_properties">
                                    <select name='search_locations' id="search_locations"
                                        class="form-control select2 search-inputs searchInput" multiple></select>
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span data-template="" data-variable="locations" data-operator="contains"
                                        data-name="Locations" data-label="locations" data-type="locations"
                                        class="hide-section waves-effect waves-light"><i
                                            class="ri-delete-bin-5-line"></i></span>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 d-none" id="show_property_build_up_area">
                            <div class="row mt-2">
                                <div class="col-lg-3">
                                    <label for="search_property_build_up_area" class="form-label">BUA</label>
                                    <select name='search_property_build_up_area_operator'
                                        id="search_property_build_up_area_operator"
                                        class="form-control operator-select">
                                        <option value="in_range" selected>between</option>
                                        <option value="not_in_range">not between</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control search-inputs searchInput"
                                        id="search_property_build_up_area_from"
                                        name="search_property_build_up_area_from" placeholder="BUA">

                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control search-inputs searchInput"
                                        id="search_property_build_up_area_to" name="search_property_build_up_area_to"
                                        placeholder="BUA">
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span data-template="" data-variable="property_build_up_area"
                                        data-operator="contains" data-name="property_build_up_area"
                                        data-label="property_build_up_area" data-type="property_build_up_area"
                                        class="hide-section waves-effect waves-light"><i
                                            class="ri-delete-bin-5-line"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 d-none" id="show_search_property_plot_size">
                            <div class="row mt-2">
                                <div class="col-lg-3">
                                    <label for="search_property_plot_size" class="form-label">Plot Size</label>
                                    <select name='search_property_plot_size_operator'
                                        id="search_property_plot_size_operator" class="form-control operator-select">
                                        <option value="in_range" selected>between</option>
                                        <option value="not_in_range">not between</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control search-inputs searchInput"
                                        id="search_property_plot_size_from" name="search_property_plot_size_from"
                                        placeholder="Plot Size">

                                </div>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control search-inputs searchInput"
                                        id="search_property_plot_size_to" name="search_property_plot_size_to"
                                        placeholder="Plot Size">

                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span data-template="" data-variable="search_property_plot_size"
                                        data-operator="contains" data-name="search_property_plot_size"
                                        data-label="search_property_plot_size" data-type="search_property_plot_size"
                                        class="hide-section waves-effect waves-light"><i
                                            class="ri-delete-bin-5-line"></i></span>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4 append-before">
                            <div class="row">
                                <div class="col-12">
                                    <select name="" id="applicator" class="form-control select2">
                                        <option value="">Select Filter to Apply</option>
                                        <?php $__currentLoopData = config('configurations.filters.Listing'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value['name']); ?>"
                                                data-operator="<?php echo e($value['operator']); ?>"
                                                data-name="<?php echo e($value['name']); ?>" data-label="<?php echo e($value['label']); ?>"
                                                data-type="<?php echo e($value['type']); ?>"
                                                data-variable="<?php echo e($value['variable']); ?>"
                                                data-template="<?php echo e($value['template']); ?>">
                                                <?php echo e(removeUnderScore($value['label'])); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row mt-3">
                        <hr>
                        <div class="col-3">
                            <div class="form-check form-switch form-switch-success">
                                <input class="form-check-input search-inputs" type="checkbox"
                                    name="search_without_contract" role="switch" id="search_without_contract">
                                <label class="form-check-label" for="search_without_contract">Listings Without
                                    Contract Dates</label>
                            </div>
                        </div>

                        <?php if(auth()->user()->hasRole(['Admin', 'Super Admin'])): ?>
                            <div class="col-3">
                                <div class="form-check form-switch form-switch-success">
                                    <input class="form-check-input search-inputs" type="checkbox"
                                        name="search_inactive" role="switch" id="search_inactive">
                                    <label class="form-check-label" for="search_inactive">Show Inactive
                                        Listing</label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if(!auth()->user()->hasRole('Agent')): ?>
                            <div class="col-3">
                                <div class="form-check form-switch form-switch-success">
                                    <input class="form-check-input search-inputs" type="checkbox" name="search_hot"
                                        role="switch" id="search_hot">
                                    <label class="form-check-label" for="search_hot">Show Hot</label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-check form-switch form-switch-success">
                                    <input class="form-check-input search-inputs" type="checkbox" name="search_refered"
                                        role="switch" id="search_refered">
                                    <label class="form-check-label" for="search_refered">Show Refered</label>
                                </div>
                            </div>

                            <?php if(auth()->user()->hasRole(['Admin', 'Super Admin'])): ?>
                                <div class="col-3">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox"
                                            name="search_deleted" role="switch" id="search_deleted">
                                        <label class="form-check-label" for="search_deleted">Show
                                            Deleted</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(auth()->user()->hasRole(['Admin', 'Super Admin'])): ?>
                            <div class="col-3 d-none">
                                <div class="form-check form-switch form-switch-success">
                                    <input class="form-check-input search-inputs" type="checkbox"
                                        name="search_need_to_archive" role="switch" id="search_need_to_archive">
                                    <label class="form-check-label" for="search_need_to_archive">Show
                                        Need to Archive</label>
                                </div>
                            </div>
                        <?php endif; ?>
                            <?php if(auth()->user()->hasRole(['Super Admin'])): ?>
                                <div class="col-3 filter_saver d-none">
                                    <input type="text" name="save_filter_name" id="save_filter_name"
                                        class="form-control form-control-sm" placeholder="Filter Name">
                                </div>
                            <?php endif; ?>
                            <?php if(auth()->user()->hasRole(['Admin', 'Super Admin','Sales Director','Line Manager','Primary Sales Manager','Admin Head','	Head of Secondary'])): ?>
                                <div class="col-3">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox"
                                            name="search_exclusive" role="switch" id="search_exclusive">
                                        <label class="form-check-label" for="search_exclusive">Show
                                            Exclusive</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(auth()->user()->hasRole(['Admin', 'Super Admin'])): ?>
                                <div class="col-3">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox"
                                            name="search_republish_requested" role="switch" id="search_republish_requested">
                                        <label class="form-check-label" for="search_republish_requested">Show Republish</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>

                    <div class="mt-4 align-items-center gap-2 text-end">
                        <button type="submit" class="btn btn-sm  btn-primary action-search me-3"
                            id="searchSearchBtn">
                            <i class="ri-search-line align-bottom me-1"></i> Search
                        </button>

                        <?php if(auth()->user()->hasRole(['Super Admin'])): ?>
                            <button type="button" class="btn btn-sm  btn-warning action-clear me-3"
                                id="saveAndSearch">
                                <i class="ri-search-line align-bottom me-1"></i> Save and Search
                            </button>

                            <button type="button" class="btn btn-sm  btn-warning action-clear me-3"
                                id="bulkReraCheckReport">
                                <i class="ri-search-line align-bottom me-1"></i> Bulk Rera Check
                            </button>

                            
                        <?php endif; ?>


                        <a href="javascript:void(0)" class="btn-sm btn text-warning action-clear me-3"
                            id="clearSearchBtn">Reset Filter</a>
                        <a href="javascript:void(0)" type="button" class="btn btn-sm text-danger action-close"
                            onclick="toggleAdvanceSearch();">Close</a>
                    </div>
                </div>


            </div>

        </form>
    </div>
</div>

<?php $__env->startSection('pagejs'); ?>
    <script>
        $(document).ready(function() {
            $('#open_filter').select2({
                placeholder: 'Select Filter',
                allowClear: true,
            });
        })

        // $(document).on('click', '#saveMyFilter', function() {
        //     let clone = $('#append-here').clone();
        //     clone.find('.append-before').remove();
        //     let data = clone.html();
        //     localStorage.setItem('listingsFilterData', data);
        //     showToastify('Data Stored', "success");
        // });

        $(document).on('click', '#saveMyFilter', function() {
            localStorage.removeItem('listingsFilterData');
            var dataArray = [];
            $('#append-here .col-md-4').each(function(index) {
                var colMd4 = $(this);
                if (colMd4.hasClass('append-before')) {
                    return true;
                }
                if (colMd4.hasClass('d-none')) {
                    return true;
                }
                var dataName = colMd4.find('.col-1 span').data('name');
                var dataVariable = colMd4.find('.col-1 span').data('variable');
                var dataOperator = colMd4.find('.col-1 span').data('operator');
                var dataTemplateName = colMd4.find('.col-1 span').data('template');
                var dataLabel = colMd4.find('.col-1 span').data('label');
                var dataType = colMd4.find('.col-1 span').data('type');


                var dataObject = {
                    dataName: dataName,
                    dataVariable: dataVariable,
                    dataOperator: dataOperator,
                    dataTemplateName: dataTemplateName,
                    dataLabel: dataLabel,
                    dataType: dataType
                };

                dataArray.push(dataObject);
            });
            // Store dataArray in local storage
            localStorage.setItem('listingsFilterData', JSON.stringify(dataArray));
            showToastify('Personalized filters saved.', "success");

        });



        $(document).on('click', '#clearSaved', function() {
            localStorage.removeItem('listingsFilterData');
            showToastify('Personalized filters cleared.', "success");
        });

        // $(document).ready(function() {
        //     var storedData = localStorage.getItem('listingsFilterData');
        //     if (storedData) {
        //         // filterLoder
        //         // showToastify('Personalized filters loading started.', "info");
        //         var listingsFilterData = JSON.parse(storedData);
        //         listingsFilterData.forEach(function(item) {
        //             $('#applicator').val(item.dataName).trigger('change');
        //         });
        //         // showToastify('Personalized filters enabled.', "success");
        //         // showToastify('Personalized filters loaded.', "success");

        //     }
        // });

        $(document).ready(function() {
            var storedData = localStorage.getItem('listingsFilterData');
            if (storedData) {
                loadFilterData(storedData);
            } else {
                let autosave = [{
                        "dataName": "for",
                        "dataVariable": "for",
                        "dataOperator": "is",
                        "dataTemplateName": "constants",
                        "dataLabel": "Category",
                        "dataType": "select"
                    },
                    {
                        "dataName": "communities",
                        "dataVariable": "communities",
                        "dataOperator": "contains",
                        "dataTemplateName": "",
                        "dataLabel": "communities",
                        "dataType": "communities"
                    },
                    {
                        "dataName": "Locations",
                        "dataVariable": "locations",
                        "dataOperator": "contains",
                        "dataTemplateName": "",
                        "dataLabel": "Locations",
                        "dataType": "locations"
                    },
                    {
                        "dataName": "beds",
                        "dataVariable": "beds",
                        "dataOperator": "is",
                        "dataTemplateName": "",
                        "dataLabel": "beds",
                        "dataType": "select"
                    },
                    {
                        "dataName": "baths",
                        "dataVariable": "baths",
                        "dataOperator": "is",
                        "dataTemplateName": "",
                        "dataLabel": "baths",
                        "dataType": "select"
                    },
                    {
                        "dataName": "price_from",
                        "dataVariable": "price_from",
                        "dataOperator": "is",
                        "dataTemplateName": "",
                        "dataLabel": "price_from",
                        "dataType": "text"
                    },
                    {
                        "dataName": "price_to",
                        "dataVariable": "price_to",
                        "dataOperator": "is",
                        "dataTemplateName": "",
                        "dataLabel": "price_to",
                        "dataType": "text"
                    },
                    {
                        "dataName": "unit_no",
                        "dataVariable": "unit_no",
                        "dataOperator": "is",
                        "dataTemplateName": "",
                        "dataLabel": "unit_no",
                        "dataType": "text"
                    },
                    {
                        "dataName": "agents",
                        "dataVariable": "agents",
                        "dataOperator": "is",
                        "dataTemplateName": "agents",
                        "dataLabel": "agents",
                        "dataType": "select"
                    }
                ]

                localStorage.setItem('listingsFilterData', JSON.stringify(autosave));
                var storedData = localStorage.getItem('listingsFilterData');
                loadFilterData(storedData);
            }


        });

        function loadFilterData(storedData) {

            var listingsFilterData = JSON.parse(storedData);
            var numItems = Object.keys(listingsFilterData).length;
            var progressStep = 100 / numItems;
            var currentProgress = 0;
            $('#filterLoder').css('width', currentProgress + '%').attr('aria-valuenow', currentProgress);
            listingsFilterData.forEach(function(item) {
                $('#applicator').val(item.dataName).trigger('change');
                currentProgress += progressStep;
                $('#filterLoder').css('width', currentProgress + '%').attr('aria-valuenow',
                    currentProgress);
            });
        }



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

        $(document).on('change', '#applicator', function() {


            var operator = $(this).find(':selected').data('operator');
            var name = $(this).find(':selected').data('name');
            var type = $(this).find(':selected').data('type');
            var label = $(this).find(':selected').data('label');
            var variable = $(this).find(':selected').data('variable');
            var template = $(this).find(':selected').data('template');
            let input = '';
            var validTypes = ['communities', 'locations', 'search_property_plot_size', 'property_build_up_area'];
            if (type == 'text' || type == 'number' || type == 'date' || type == 'email' || type == 'phone') {
                let extras = '';
                if (type == 'date') {
                    extras = 'data-provider="flatpickr" data-date-format="Y-m-d" data-range-date="true"';
                }
                input = ` <div class="col-md-4 mb-2">
                            <div class="row">
                                <div class="col-3">
                                    <label for="agent" class="form-label">${removeUnderScore(label)}</label>
                                    <select name='search_${name}_operator' id="search_${name}_operator"
                                        class="form-control operator-select">

                                    </select>
                                </div>
                                <div class="col-8">
                                    <input type="${type}" ${extras} class="form-control search-inputs searchInput" id="search_${name}"
                                        name="search_${name}" placeholder="Search ${removeUnderScore(label)}" value="" >
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span  data-template="${template}"  data-variable="${variable}" data-operator="${operator}" data-name="${name}" data-label="${label}" data-type="${type}" class="remove-filter waves-effect waves-light"><i class="ri-delete-bin-5-line"></i></span>
                                </div>
                            </div>
                        </div>`

                $.ajax({
                    url: "<?php echo e(route('admin.listing.properties.filterHelper')); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        type: variable,
                    },
                    success: function(result) {
                        // console.log(result);
                        let operator_options = '';

                        Object.entries(result?.operators).forEach(function([key, value]) {
                            operator_options += `<option value="${key}">${value}</option>`;
                        });
                        $('#search_' + name + '_operator').html(operator_options);

                        if (type == 'date') {
                            $("#search_" + name).flatpickr({
                                altInput: true,
                                mode: "range",
                                dateFormat: "Y-m-d H:i",
                            });
                        }

                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            } else if (type == 'select') {
                let selectall = '';
                let agentsOptions = '';
                let customClass = ''
                if (name == 'agents') {
                    customClass = 'scrollable-div'
                    selectall =
                        ' <div class="form-check form-switch form-check-inline " dir="ltr"><small for="">All </small> <input type="checkbox" value="all" class="form-check-input" id="selectall"> </div>';
                    agentsOptions = ` <div class="mt-1">
                <div class="row">
                <div class="col-md-6">
                    <div class="form-check form-switch form-check-inline" dir="ltr">
                        <small for="showInactiveAgents">Inactive </small> <input type="checkbox" value="inactive"
                            class="form-check-input" id="showInactiveAgents" name="showInactiveAgents">
                    </div>
                </div>
                <?php if(auth()->user()->hasRole(['Super Admin', 'Line Manager', 'Team Leader', 'Sales Director', 'Head of Secondary', 'CEO'])): ?>
                    <div class="col-md-6">
                        <div class="form-check form-switch form-check-inline" dir="ltr">
                            <small for="showMyTeamAgents">Team </small> <input type="checkbox" value="myteam"
                                class="form-check-input" id="showMyTeamAgents" name="showMyTeamAgents">

                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>`;
                }
                if (name == 'managers') {
                    customClass = 'scrollable-div'
                    agentsOptions = ` <div class="mt-1">
                <div class="row">
                <div class="col-md-6">
                    <div class="form-check form-switch form-check-inline" dir="ltr">
                        <small for="showInactiveManagers">Inactive </small> <input type="checkbox" value="inactive"
                            class="form-check-input" id="showInactiveManagers" name="showInactiveManagers">
                    </div>
                </div>
            </div>
        </div>`;
                }

                input = `<div class="col-md-4 mb-2 d-none" id="search_${name}_sleep">
                            <div class="row">
                                <div class="col-3">
                                    <label for="agent" class="form-label">${removeUnderScore(label)}</label>
                                    <select name='search_${name}_operator' id="search_${name}_operator"
                                        class="form-control operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                    ${selectall }
                                </div>
                                <div class="col-8 ${customClass}">
                                    <select name="search_${name}" id="search_${name}" class="form-control search-inputs searchInput" multiple="multiple">

                                    </select>
                                    ${agentsOptions}
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span  data-template="${template}"  data-variable="${variable}" data-operator="${operator}" data-name="${name}" data-label="${label}" data-type="${type}" class="remove-filter waves-effect waves-light"><i class="ri-delete-bin-5-line"></i></span>

                                </div>
                            </div>
                        </div>`

                $.ajax({
                    url: "<?php echo e(route('admin.listing.properties.filterHelper')); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        type: variable,
                    },
                    success: function(result) {
                        // console.log(result);
                        let operator_options = '';

                        Object.entries(result?.operators).forEach(function([key, value]) {
                            operator_options +=
                                `<option value="${key}">${value}</option>`;
                        });

                        $('#search_' + name + '_operator').html(operator_options);
                        let options = "";

                        if (template == 'constants') {
                            Object.keys(result?.data).forEach(function(key) {
                                options +=
                                    `<option value="${key}">${result?.data[key]}</option>`;
                            });
                            $('#search_' + name).html(options);
                            let placeholder = 'Select ' + removeUnderScore(name);
                            $('#search_' + name).select2({
                                placeholder: placeholder,
                                allowClear: true,
                                focusOnOpen: false,
                                closeOnSelect: false,
                                minimumResultsForSearch: Infinity,
                            });


                            $('#search_' + name + '_sleep').removeClass('d-none');

                        } else if (template == 'telesales' || template == 'agents' || template == 'previous_agents' || template == 'updated_by') {


                            let placeholder = 'Select ' + removeUnderScore(label);


                            $('#search_' + name).select2({
                                placeholder: 'Search Agents',
                                minimumInputLength: 0,
                                ajax: {
                                    url: "<?php echo e(route('admin.work.leads.autocompleteAgents')); ?>",
                                    dataType: 'json',
                                    delay: 250,
                                    processResults: function(data) {
                                        return {
                                            results: $.map(data, function(item) {
                                                return {
                                                    text: item.full_name ?? "No Name",
                                                    id: item?.id ?? '-',
                                                    img: item?.thumbnail ?? '-',
                                                    status : item?.status ?? '-'
                                                }
                                            })
                                        };
                                    },
                                    cache: true
                                },
                                templateResult: formatAgentTwo,

                            });


                            $('#search_' + name + '_sleep').removeClass('d-none');
                        }else if (template == 'managers') {

                            result?.data.map(item => {
                                options +=
                                    `<option data-image="${item.thumbnail}" data-status="${item.status}" value="${ item.id }">${ item.first_name } ${ item.last_name }</option>`;

                            })
                            let placeholder = 'Select ' + removeUnderScore(label);
                            $('#search_' + name).html(options);


                            $('#search_' + name).select2({
                                placeholder: 'Search Manager',
                                minimumInputLength: 0,
                                ajax: {
                                    url: "<?php echo e(route('admin.work.leads.autocompleteManager')); ?>",
                                    dataType: 'json',
                                    delay: 250,
                                    processResults: function(data) {
                                        return {
                                            results: $.map(data, function(item) {
                                                return {
                                                    text: item.full_name ?? "No Name",
                                                    id: item?.id ?? '-',
                                                    img: item?.thumbnail ?? '-',
                                                    status : item?.status ?? '-'
                                                }
                                            })
                                        };
                                    },
                                    cache: true
                                },
                                templateResult: formatAgentTwo,

                            });


                            $('#search_' + name + '_sleep').removeClass('d-none');
                        } else {
                            // console.log(result?.data)
                            if (name == 'communities') {
                                filter_locationSelect2();
                                $('#search_' + name + '_sleep').removeClass('d-none');
                            } else {
                                result?.data.map(item => {

                                    if (item.name) {
                                        options +=
                                            `<option value="${item.id}">${item.name}</option>`;
                                    } else if (item.title) {
                                        options +=
                                            `<option value="${item.id}">${item.title}</option>`;
                                    }


                                })
                                $('#search_' + name).html(options);
                                $('#search_' + name).select2({
                                    placeholder: 'Select ' + label,
                                    allowClear: true,
                                    focusOnOpen: false,
                                    closeOnSelect: false,
                                    minimumResultsForSearch: Infinity,
                                });
                                $('#search_' + name + '_sleep').removeClass('d-none');
                            }

                        }
                        $('#search_' + name + '_sleep').removeClass('d-none');

                    },
                    error: function(result) {
                        console.log(result);
                    }
                });

            } else if (validTypes.includes(type)) {
                $('#show_' + type).removeClass('d-none').insertBefore('.append-before');
            }
            $('#search_' + name + '_sleep').removeClass('d-none');

            $(input).insertBefore('.append-before');
            $(this).find(':selected').remove();

            // removeLoader('listingsFilterData')


        });




        $(document).on('click', '.remove-filter', function() {
            var operator = $(this).data('operator');
            var name = $(this).data('name');
            var type = $(this).data('type');
            var label = $(this).data('label');
            var variable = $(this).data('variable');
            var template = $(this).data('template');
            var option =
                `<option value="${name}" data-template="${template}" data-variable="${variable}" data-operator="${operator}" data-name="${name}" data-label="${label}" data-type="${type}">${removeUnderScore(label)}</option>`;
            $('#applicator').append(option);
            $(this).closest('.col-md-4').remove();
        })

        $(document).on('click', '.hide-section', function() {

            var operator = $(this).data('operator');
            var name = $(this).data('name');
            var type = $(this).data('type');
            var label = $(this).data('label');
            var variable = $(this).data('variable');
            var template = $(this).data('template');
            var option =
                `<option value="${name}" data-template="${template}" data-variable="${variable}" data-operator="${operator}" data-name="${name}" data-label="${label}" data-type="${type}">${removeUnderScore(label)}</option>`;
            $('#applicator').append(option);
            // find nearest col-md and reset its select2
            $(this).closest('.col-md-4').find('.searchInput').val(null).trigger('change');

            $(this).closest('.col-md-4').addClass('d-none');
        })


        function formatAgent(e) {
            let img = $(e.element).data('image');
            let status = $(e.element).data('status');
            let onlineClass = "online"
            if (!status) {
                onlineClass = "away";
            }
            return e.id ? $('<div class="d-flex align-items-center"><div class="flex-shrink-0 chat-user-img ' +
                onlineClass + ' align-self-center me-2 ms-0"><div class="avatar-xxs mb-2"> <img src="' + img +
                '" class="rounded-circle img-fluid userprofile" alt=""><span class="user-status"></span> </div></div><div class="flex-grow-1 overflow-hidden"><p class="text-truncate mb-0">' +
                e.text + '</p></div></div>') : e.text;
        }
        function formatAgentTwo(e) {
            let img = $(e.element).data('img');
            let status = (e.status);
            let onlineClass = "online"
            if (!status) {
                onlineClass = "away";
            }
            return e.id ? $('<div class="d-flex align-items-center"><div class="flex-shrink-0 chat-user-img ' +
                onlineClass + ' align-self-center me-2 ms-0"><div class="avatar-xxs mb-2"> <img src="' + e.img +
                '" class="rounded-circle img-fluid userprofile" alt=""><span class="user-status"></span> </div></div><div class="flex-grow-1 overflow-hidden"><p class="text-truncate mb-0">' +
                e.text + '</p></div></div>') : e.text;
        }
        function formatAgentView(e) {
            let img = $(e.element).data('image');
            let status = $(e.element).data('status');
            let onlineClass = "online"
            if (!status) {
                onlineClass = "away";
            }
            return (e.text ?? "No Name")
        }

        function removeUnderScore(str) {
            return str.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

        }

        function removeLoader(variablename) {
            var storedData = localStorage.getItem(variablename);
            if (storedData) {
                var filterData = JSON.parse(storedData); // Parse stored data to JavaScript object
                var allIdsPresent = true; // Flag to track if all IDs are present

                for (var i = 0; i < filterData.length; i++) {
                    var dataName = filterData[i].dataName;
                    // Check if element with ID search_${dataName} exists
                    if (!document.getElementById(`search_${dataName}`) || !document.getElementById(`${dataName}`) ==
                        'property_build_up_area') {
                        // console.log(dataName)
                        allIdsPresent = false; // Set flag to false if any ID is missing
                        break; // No need to continue checking if one ID is missing
                    }
                }

                if (allIdsPresent) {
                    console.log('All IDs are present');
                    $('#filterLoaderDiv').addClass('d-none');
                    $('#append-here').removeClass('d-none');
                } else {
                    console.log('All IDs are not present');

                }
            } else {
                $('#filterLoaderDiv').addClass('d-none');
                $('#append-here').removeClass('d-none');

            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('agent_filter'); ?>
    <script>
        $(document).on('change', '#showInactiveAgents', function(e) {
            getAgents();
        });
        $(document).on('change', '#showInactiveManagers', function(e) {
            getManagers();
        });
        $(document).on('change', '#showMyTeamAgents', function(e) {
            getAgents();
        });




        function getAgents() {
            if ($('#showInactiveAgents').is(':checked')) {
                all_agents = 'true';
            } else {
                all_agents = 'false';
            }

            if ($('#showMyTeamAgents').is(':checked')) {
                my_team = 'true';
            } else {
                my_team = 'false';
            }
            $.ajax({
                url: "<?php echo e(route('ajax.getAgentsBasedOnStatus')); ?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    all_agents: all_agents,
                    my_team: my_team,
                },
                success: function(result) {
                    $("#search_agents").html('');

                    // make option and append to select #search_agents
                    let options = "";
                    result?.data.map(item => {
                        // options +=  `<option value="${item.id}">${item.full_name}</option>`;

                        options +=
                            `<option data-image="${item.thumbnail??item.thumb_url}" data-status="${item.status}" value="${item.id}">${item.full_name}</option>`;
                    });
                    $("#search_agents").html(options);

                    $('#search_agents').select2({
                        width: '100%',
                        placeholder: 'Select Agent',
                        allowClear: true,
                        templateResult: formatAgent,
                        templateSelection: formatAgentView
                    });
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log()
                },
            });
            $(document).on('change', '#selectall', function() {
                if ($(this).is(':checked')) {

                    var agents = $('#search_agents option').map(function() {
                        return $(this).val();
                    }).get();

                    $('#search_agents').val(agents).trigger('change');

                } else {
                    $('#search_agents').val(null).trigger('change');
                }
            });
        }
        function getManagers() {
            if ($('#showInactiveManagers').is(':checked')) {
                all_agents = 'true';
            } else {
                all_agents = 'false';
            }
            my_team = 'false';
            $.ajax({
                url: "<?php echo e(route('ajax.getAgentsBasedOnStatus')); ?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    all_agents: all_agents,
                    my_team: my_team,
                },
                success: function(result) {
                    $("#search_managers").html('');

                    // make option and append to select #search_agents
                    let options = "";
                    result?.data.map(item => {
                        // options +=  `<option value="${item.id}">${item.full_name}</option>`;

                        options +=
                            `<option data-image="${item.thumbnail??item.thumb_url}" data-status="${item.status}" value="${item.id}">${item.full_name}</option>`;
                    });
                    $("#search_managers").html(options);

                    $('#search_managers').select2({
                        width: '100%',
                        placeholder: 'Select Manager',
                        allowClear: true,
                        templateResult: formatAgent,
                        templateSelection: formatAgentView
                    });
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log()
                },
            });
            $(document).on('change', '#selectall', function() {
                if ($(this).is(':checked')) {

                    var agents = $('#search_agents option').map(function() {
                        return $(this).val();
                    }).get();

                    $('#search_agents').val(agents).trigger('change');

                } else {
                    $('#search_agents').val(null).trigger('change');
                }
            });
        }
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/listing/includes/filterForm.blade.php ENDPATH**/ ?>