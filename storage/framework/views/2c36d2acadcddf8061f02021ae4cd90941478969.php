<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">

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
                            <div class="btn-group admin_approval_buttons " role="group">
                                <button id="btnGroupDropFilters" type="button"
                                    class="btn btn-sm action-clear waves-effect dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Filter Actions
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDropFilters">

                                    <li><a class="dropdown-item" id="saveMyFilter">Save my filter preferences</a></li>

                                    <li><a class="dropdown-item" id="clearSaved">Clear my filter preferences</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body bg-light">
                    <div class="row" id="append-here">
                        <div class="col-md-4 d-none" id="show_communities">
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label for="filter_communities" class="form-label">Communities</label>
                                    <select name='search_communities_operator' id="filter_communities_operator"
                                        class="form-control operator-select">
                                        <option value="hasCommunities" selected>is</option>
                                        <option value="hasNotCommunities">is not</option>
                                    </select>

                                </div>
                                <div class="col-8">
                                    <select class="form-control search-inputs searchInput" multiple
                                        id="filter_communities" name="filter_communities"></select>
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span data-template="" data-variable="communities" data-operator="contains"
                                        data-name="communities" data-label="communities" data-type="communities"
                                        class="hide-section waves-effect waves-light"><i
                                            class="ri-delete-bin-5-line"></i></span>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-none" id="show_sub_communities">
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label for="filter_locations" class="form-label">Location</label>
                                    <select name='filter_sub_communities_operator' id="filter_sub_communities_operator"
                                        class="form-control   operator-select">
                                        <option value="hasSubCommunities" selected>is</option>
                                        <option value="hasNotSubCommunities">is not</option>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <input type="hidden" name="filter_locations" id="filter_locations">
                                    <select class="select2  search-inputs searchInput" multiple
                                        id="filter_sub_communities" name="filter_sub_communities"></select>
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span data-template="" data-variable="sub_communities" data-operator="contains"
                                        data-name="sub_communities" data-label="locations" data-type="sub_communities"
                                        class="hide-section waves-effect waves-light"><i
                                            class="ri-delete-bin-5-line"></i></span>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 d-none" id="show_prices">
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label for="filter_min_price" class="form-label">Price</label>

                                </div>
                                <div class="col-4">

                                    <select class="form-control" id="filter_min_price" name="filter_min_price">
                                        <option></option>
                                        <option value="10000">10,000</option>
                                        <option value="20000">20,000</option>
                                        <option value="30000">30,000</option>
                                        <option value="40000">40,000</option>
                                        <option value="50000">50,000</option>
                                        <option value="60000">60,000</option>
                                        <option value="850000">85,000</option>
                                        <option value="110000">110,000</option>
                                        <option value="135000">135,000</option>
                                        <option value="160000">160,000</option>
                                        <option value="185000">185,000</option>
                                        <option value="210000">210,000</option>
                                        <option value="235000">235,000</option>
                                        <option value="260000">260,000</option>
                                        <option value="285000">285,000</option>
                                        <option value="310000">310,000</option>
                                        <option value="335000">335,000</option>
                                        <option value="360000">360,000</option>
                                        <option value="410000">410,000</option>
                                        <option value="460000">460,000</option>
                                        <option value="510000">510,000</option>
                                        <option value="560000">560,000</option>
                                        <option value="610000">610,000</option>
                                        <option value="660000">660,000</option>
                                        <option value="710000">710,000</option>
                                        <option value="760000">760,000</option>
                                        <option value="810000">810,000</option>
                                        <option value="860000">860,000</option>
                                        <option value="910000">910,000</option>

                                    </select>
                                </div>
                                <div class="col-4">
                                    <select class="form-control" id="filter_max_price" name="filter_max_price">
                                        <option></option>

                                        <option value="50000">50,000</option>
                                        <option value="60000">60,000</option>
                                        <option value="850000">85,000</option>
                                        <option value="110000">110,000</option>
                                        <option value="135000">135,000</option>
                                        <option value="160000">160,000</option>
                                        <option value="185000">185,000</option>
                                        <option value="210000">210,000</option>
                                        <option value="235000">235,000</option>
                                        <option value="260000">260,000</option>
                                        <option value="285000">285,000</option>
                                        <option value="310000">310,000</option>
                                        <option value="335000">335,000</option>
                                        <option value="360000">360,000</option>
                                        <option value="410000">410,000</option>
                                        <option value="460000">460,000</option>
                                        <option value="510000">510,000</option>
                                        <option value="560000">560,000</option>
                                        <option value="610000">610,000</option>
                                        <option value="660000">660,000</option>
                                        <option value="710000">710,000</option>
                                        <option value="760000">760,000</option>
                                        <option value="810000">810,000</option>
                                        <option value="860000">860,000</option>
                                        <option value="910000">910,000</option>
                                        <option value="1010000">1,010,000</option>
                                    </select>

                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span data-template="" data-variable="prices" data-operator="contains"
                                        data-name="prices" data-label="prices" data-type="prices"
                                        class="hide-section waves-effect waves-light"><i
                                            class="ri-delete-bin-5-line"></i></span>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 d-none" id="show_baths">
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label for="filter_locations" class="form-label">Baths</label>
                                </div>
                                <div class="col-4">
                                    <select class="form-control search-inputs select2" name="filter_min_bath_id"
                                        id="filter_min_bath_id">
                                        <option value=""></option>

                                        <?php $__currentLoopData = $baths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($bath->id); ?>"><?php echo e($bath->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select class="form-control search-inputs select2" name="filter_max_bath_id"
                                        id="filter_max_bath_id">
                                        <option value=""></option>

                                        <?php $__currentLoopData = $baths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($bath->id); ?>"><?php echo e($bath->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span data-template="" data-variable="baths" data-operator="contains"
                                        data-name="baths" data-label="baths" data-type="baths"
                                        class="hide-section waves-effect waves-light"><i
                                            class="ri-delete-bin-5-line"></i></span>

                                </div>
                            </div>

                        </div>

                        <div class="col-md-4 d-none" id="show_beds">
                            <div class="row mt-2">
                                <div class="col-3">
                                    <label for="filter_locations" class="form-label">Beds</label>
                                </div>
                                <div class="col-4">
                                    <select class="form-control  search-inputs select2" name="filter_min_bed_id"
                                        id="filter_min_bed_id">
                                        <option value=""></option>
                                        <?php $__currentLoopData = $beds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($bed->id); ?>"><?php echo e($bed->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select class="form-control search-inputs select2" name="filter_max_bed_id"
                                        id="filter_max_bed_id">
                                        <option value=""></option>

                                        <?php $__currentLoopData = $beds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($bed->id); ?>"><?php echo e($bed->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-1 filter_deletion_btn">
                                    <span data-template="" data-variable="beds" data-operator="contains"
                                        data-name="beds" data-label="beds" data-type="beds"
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
                                        <?php $__currentLoopData = config('configurations.filters.Lead'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                    <div class="row mt-2">
                        <div class="col-3 mb-2">
                            <div class="form-check form-switch form-switch-success">
                                <input class="form-check-input search-inputs" type="checkbox"
                                    name="search_incomplete_leads" role="switch" id="search_incomplete_leads">
                                <label class="form-check-label" data-toggle="tooltip" data-placement="top" title="Showing leads after 15 march 2024" for="search_incomplete_leads">Show Incomplete
                                    Leads</label>
                            </div>
                        </div>

                        <div class="col-3 mb-2">
                            <div class="form-check form-switch form-switch-success">
                                <input class="form-check-input search-inputs" type="checkbox" name="search_my_data"
                                    role="switch" id="search_my_data">
                                <label class="form-check-label" for="search_my_data">Filter My
                                    Data</label>
                            </div>
                        </div>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Bulk SMS')): ?>
                        <div class="col-3">
                        <button type="button" id="send-sms" class="btn btn-sm btn-primary btn-label waves-effect waves-light"><i class="ri-message-3-line label-icon align-middle fs-16 me-2"></i> Start SMS Campaign</button>
                        </div>
                        <?php endif; ?>

                        <?php if(auth()->user()->hasRole(['Admin', 'Super Admin'])): ?>
                            <div class="col-3 mb-2">
                                <div class="form-check form-switch form-switch-success">
                                    <input class="form-check-input search-inputs" type="checkbox"
                                        name="search_deleted" role="switch" id="search_deleted">
                                    <label class="form-check-label" for="search_deleted">Show Deleted
                                        Also</label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if(auth()->user()->hasRole(['Admin', 'Super Admin'])): ?>
                            <div class="col-3 mb-2">
                                <div class="form-check form-switch form-switch-success">
                                    <input class="form-check-input search-inputs" type="checkbox"
                                        name="search_archive" role="switch" id="search_archive">
                                    <label class="form-check-label" for="search_archive">Show Archived
                                        Also</label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if(auth()->user()->hasRole([
                                    'Admin',
                                    'Super Admin',
                                    'Primary Sales Manager',
                                    'Primary Sales Director',
                                    'Line Manager',
                                    'Team Leader',
                                    'Sales Director',
                                    'Head of Secondary',
                                    'CEO',
                                ])): ?>
                            <div class="col-3 mb-2">
                                <div class="form-check form-switch form-switch-success">
                                    <input class="form-check-input search-inputs" type="checkbox"
                                        name="search_inactive_agents" role="switch" id="search_inactive_agents">
                                    <label class="form-check-label" for="search_inactive_agents">Show
                                        Inactive Agents Data</label>
                                </div>
                            </div>
                        <?php endif; ?>


                    </div>

                    <div class="mt-4 align-items-center gap-2 text-end">
                        <button type="submit" class="btn btn-sm  btn-primary action-search me-3"
                            id="searchSearchBtn">
                            <i class="ri-search-line align-bottom me-1"></i> Search
                        </button>
                        <?php if(auth()->user()->hasRole(['Super Admin'])): ?>
                            
                        <?php endif; ?>
                        <a href="javascript:void(0)" class="btn-sm btn text-warning action-clear me-3"
                            id="clearSearchBtn">Clear</a>
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
        $(document).on('click', '#saveMyFilter', function() {
            localStorage.removeItem('leadFilterData');
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
            localStorage.setItem('leadFilterData', JSON.stringify(dataArray));
            showToastify('Personalized filters saved.', "success");

        });



        $(document).on('click', '#clearSaved', function() {
            localStorage.removeItem('leadFilterData');
            showToastify('Personalized filters cleared.', "success");
        });

        $(document).ready(function() {


            var storedData = localStorage.getItem('leadFilterData');
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
                }, {
                    "dataName": "lead_type",
                    "dataVariable": "type",
                    "dataOperator": "is",
                    "dataTemplateName": "",
                    "dataLabel": "type",
                    "dataType": "select"
                }, {
                    "dataName": "lead_sources",
                    "dataVariable": "source",
                    "dataOperator": "is",
                    "dataTemplateName": "",
                    "dataLabel": "sources",
                    "dataType": "select"
                }, {
                    "dataName": "communities",
                    "dataVariable": "communities",
                    "dataOperator": "contains",
                    "dataTemplateName": "",
                    "dataLabel": "communities",
                    "dataType": "communities"
                }, {
                    "dataName": "sub_communities",
                    "dataVariable": "sub_communities",
                    "dataOperator": "contains",
                    "dataTemplateName": "",
                    "dataLabel": "locations",
                    "dataType": "sub_communities"
                }, {
                    "dataName": "mobile",
                    "dataVariable": "mobile",
                    "dataOperator": "is",
                    "dataTemplateName": "",
                    "dataLabel": "mobile",
                    "dataType": "text"
                }, {
                    "dataName": "agents",
                    "dataVariable": "agents",
                    "dataOperator": "is",
                    "dataTemplateName": "agents",
                    "dataLabel": "agents",
                    "dataType": "select"
                }]


                localStorage.setItem('leadFilterData', JSON.stringify(autosave));
                var storedData = localStorage.getItem('leadFilterData');
                loadFilterData(storedData);
            }


        });

        function loadFilterData(storedData) {
            var leadsFilterData = JSON.parse(storedData);
            var numItems = Object.keys(leadsFilterData).length;
            var progressStep = 100 / numItems;
            var currentProgress = 0;
            $('#filterLoder').css('width', currentProgress + '%').attr('aria-valuenow', currentProgress);
            leadsFilterData.forEach(function(item) {
                $('#applicator').val(item.dataName).trigger('change');
                currentProgress += progressStep;
                $('#filterLoder').css('width', currentProgress + '%').attr('aria-valuenow',
                    currentProgress);
            });
        }

        $(document).on('change', '#applicator', function() {
            var operator = $(this).find(':selected').data('operator');
            var name = $(this).find(':selected').data('name');
            var type = $(this).find(':selected').data('type');
            var label = $(this).find(':selected').data('label');
            var variable = $(this).find(':selected').data('variable');
            var template = $(this).find(':selected').data('template');
            let input = '';
            var validTypes = ['communities', 'sub_communities', 'beds', 'baths', 'prices'];
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
                    url: "<?php echo e(route('admin.work.leads.filterHelper')); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        type: variable,
                    },
                    success: function(result) {
                        console.log(result);
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
                                    ${selectall}
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
                    url: "<?php echo e(route('admin.work.leads.filterHelper')); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        type: variable,
                    },
                    success: function(result) {
                        console.log(result);
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
                            $('#search_' + name).select2({
                                placeholder: 'Select ' + label,
                                allowClear: true,
                                focusOnOpen: false,
                                closeOnSelect: false,
                                minimumResultsForSearch: Infinity,
                            });



                            $('#search_' + name + '_sleep').removeClass('d-none');
                        } else if (template == 'telesales' || template == 'agents' || template =='previous_agents' || template == 'updated_by') {

                            let placeholder = 'Select ' + removeUnderScore(label);
                            $('#search_' + name).html(options);

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
                                                    text: item.full_name ??
                                                        "No Name",
                                                    id: item?.id ?? '-',
                                                    img: item?.thumbnail ?? '-',
                                                    status: item?.status ?? '-'
                                                }
                                            })
                                        };
                                    },
                                    cache: true
                                },
                                templateResult: formatAgentTwo,

                            });
                            $('#search_' + name + '_sleep').removeClass('d-none');

                        } else if (template == 'managers') {

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
                                                    text: item.full_name ??
                                                        "No Name",
                                                    id: item?.id ?? '-',
                                                    img: item?.thumbnail ?? '-',
                                                    status: item?.status ?? '-'
                                                }
                                            })
                                        };
                                    },
                                    cache: true
                                },
                                templateResult: formatAgentTwo,

                            });


                            $('#search_' + name + '_sleep').removeClass('d-none');
                        } else if (template == 'keypair') {
                            result?.data.map(item => {
                                options +=
                                    `<option value="${item.id}">${item.name}</option>`;

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
                        } else if (template == 'property_ref') {
                            result?.data.map(item => {
                                console.log(item)
                                options += `<option value="${item.id}">${item.title}</option>`;

                            })
                            $('#search_' + name).html(options);
                            $('#search_property_ref').select2({
                                placeholder: 'Search Property',
                                minimumInputLength: 0,
                                ajax: {
                                    url: "<?php echo e(route('admin.work.leads.autocomplete')); ?>",
                                    dataType: 'json',
                                    delay: 250,
                                    data: function(params) {
                                        var requestData = {
                                            term: params.term
                                        };
                                        if ($('#search_agents').length) {
                                            requestData.agents = $('#search_agents').val();
                                        }
                                        return requestData;
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: $.map(data, function(item) {
                                                return {
                                                    text: '[' + item.ref_no + '] ' +
                                                        (item.title ?? "No Name"),
                                                    id: item?.id ?? '-',
                                                    unit_no: item?.unit_no ?? '-',
                                                    ref_no: item?.ref_no ?? '-',
                                                    location: item
                                                        ?.community_property
                                                        ?.name ?? '-',
                                                    type: item?.property_category
                                                        ?.title ?? '-',
                                                    beds: item?.bed?.name ?? '-',
                                                    first_image: item?.first_image
                                                        ?.thumbnail ?? '-',
                                                }
                                            })
                                        };
                                    },
                                    cache: true
                                },
                                templateResult: formatProperty2,

                            });

                            $('#search_' + name + '_sleep').removeClass('d-none');

                        } else {
                            if (name == 'communities') {
                                filter_locationSelect2();
                            } else {
                                result?.data.map(item => {
                                    options +=
                                        `<option value="${item.id}">${item.name}</option>`;

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
            $(input).insertBefore('.append-before');


            $(this).find(':selected').remove();

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
    </script>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('agent_filter'); ?>
    <script>
        $(document).on('change', '#showInactiveAgents', function(e) {
            getAgents();
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
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/lead/include/filterForm.blade.php ENDPATH**/ ?>